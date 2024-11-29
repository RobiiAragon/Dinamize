document.addEventListener('DOMContentLoaded', function() {
    const svgEditor = document.getElementById('svg-editor');
    const divContainer = document.getElementById('div-container');
    const modal = document.getElementById('modal');
    const closeButton = document.querySelector('.close-button');
    const businessList = document.getElementById('business-list');
    let selectedLocalId = null;
    const scaleFactor = 2; // Factor de escala fijo

    // Ajustar el viewBox para asegurar que el SVG se vea completo
    svgEditor.setAttribute('viewBox', '0 0 553.1 727.11');

    // Agregar interactividad básica
    const locales = svgEditor.querySelectorAll('[id^="local"]');

    // Función para cargar los negocios asignados
    function loadAssignedBusinesses() {
        // Primero limpiamos los textos y logos existentes
        svgEditor.querySelectorAll('.business-info').forEach(el => el.remove());

        $.ajax({
            url: 'backend/get_assigned_businesses.php',
            type: 'GET',
            success: function(response) {
                const businesses = JSON.parse(response);
                businesses.forEach(business => {
                    if (business.nombre) {
                        const local = svgEditor.querySelector(`#local${business.local_id}`);
                        if (local) {
                            // Crear grupo para el texto y logo
                            const group = document.createElementNS("http://www.w3.org/2000/svg", "g");
                            group.classList.add('business-info');
                            
                            // Obtener el centro del local
                            const shape = local.querySelector('polygon, rect');
                            const bbox = shape.getBBox();
                            const centerX = bbox.x + (bbox.width / 2);
                            const centerY = bbox.y + (bbox.height / 2);
                            
                            // Si hay logo, añadir imagen
                            if (business.logo) {
                                const image = document.createElementNS("http://www.w3.org/2000/svg", "image");
                                image.setAttribute("x", centerX - 15);
                                image.setAttribute("y", centerY - 30);
                                image.setAttribute("width", "30");
                                image.setAttribute("height", "30");
                                image.setAttribute("href", `data:image/jpeg;base64,${business.logo}`);
                                group.appendChild(image);
                            }

                            // Añadir texto
                            const text = document.createElementNS("http://www.w3.org/2000/svg", "text");
                            text.setAttribute("class", "business-name");
                            text.textContent = business.nombre;
                            text.setAttribute("x", centerX);
                            text.setAttribute("y", business.logo ? centerY + 10 : centerY);
                            text.setAttribute("text-anchor", "middle");
                            
                            group.appendChild(text);
                            local.appendChild(group);
                        }
                    }
                });
            },
            error: function() {
                console.error('Error al cargar los negocios asignados');
            }
        });
    }

    locales.forEach(local => {
        local.addEventListener('click', function() {
            // Remover la clase 'selected-local' de todos los locales
            svgEditor.querySelectorAll('.selected-local').forEach(el => {
                el.classList.remove('selected-local');
            });

            // Agregar la clase 'selected-local' al elemento polygon o rect dentro del local
            const shape = this.querySelector('polygon, rect');
            if (shape) {
                shape.classList.add('selected-local');
            }

            selectedLocalId = this.id.replace('local', '');
            fetchBusinesses();
            modal.style.display = 'block';
        });
    });

    closeButton.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });

    function fetchBusinesses() {
        $.ajax({
            url: 'backend/get_businesses.php',
            type: 'GET',
            success: function(response) {
                const businesses = JSON.parse(response);
                businessList.innerHTML = '';
                businesses.forEach(business => {
                    const li = document.createElement('li');
                    li.textContent = `${business.id} - ${business.nombre}`;
                    li.dataset.businessId = business.id;
                    li.addEventListener('click', function() {
                        assignBusinessToLocal(this.dataset.businessId);
                    });
                    businessList.appendChild(li);
                });
            },
            error: function() {
                alert('Error al obtener la lista de negocios.');
            }
        });
    }

    function assignBusinessToLocal(businessId) {
        console.log('Asignando negocio:', businessId, 'al local:', selectedLocalId);
        $.ajax({
            url: 'backend/update_local.php',
            type: 'POST',
            data: {
                localId: selectedLocalId,
                negocioId: businessId
            },
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert('Negocio asignado correctamente.');
                    loadAssignedBusinesses(); // Recargar los negocios asignados
                    createDivsForLocals(); // Recargar la distribución del kiosco
                } else {
                    alert('Error al asignar el negocio: ' + result.error);
                }
                modal.style.display = 'none';
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', status, error);
                alert('Error en la solicitud AJAX.');
                modal.style.display = 'none';
            }
        });
    }

    // Cargar los negocios asignados al inicio
    loadAssignedBusinesses();

    // Deseleccionar local si se hace clic fuera del SVG
    document.addEventListener('click', function(event) {
        if (!svgEditor.contains(event.target)) {
            svgEditor.querySelectorAll('.selected-local').forEach(el => {
                el.classList.remove('selected-local');
            });
            selectedLocalId = null;
        }
    });

    // Crear y posicionar los divs sobre los elementos local
    function createDivsForLocals() {
        locales.forEach(local => {
            const shape = local.querySelector('polygon, rect');
            const bbox = shape.getBBox();
            const div = document.createElement('div');
            div.classList.add('local-label');
            div.style.position = 'absolute';

            // Aplicar escala, rotación y reposicionamiento específicos para locales 12 y 13
            if (local.id === 'local12') {
                const specificScaleFactor = 1.5;
                div.style.left = `${(bbox.x * specificScaleFactor) + 77}px`;
                div.style.top = `${(bbox.y * specificScaleFactor) + 105}px`;
                div.style.width = `${bbox.width * specificScaleFactor}px`;
                div.style.height = `${bbox.height * specificScaleFactor}px`;
                div.style.transform = 'rotate(45deg)';
                div.style.transformOrigin = 'center center';
            } else if (local.id === 'local13') {
                const specificScaleFactor = 1.5;
                div.style.left = `${(bbox.x * specificScaleFactor) + 95}px`;
                div.style.top = `${(bbox.y * specificScaleFactor) + 130}px`;
                div.style.width = `${bbox.width * specificScaleFactor}px`;
                div.style.height = `${bbox.height * specificScaleFactor}px`;
                div.style.transform = 'rotate(45deg)';
                div.style.transformOrigin = 'center center';
            } else {
                div.style.left = `${bbox.x * scaleFactor}px`;
                div.style.top = `${bbox.y * scaleFactor}px`;
                div.style.width = `${bbox.width * scaleFactor}px`;
                div.style.height = `${bbox.height * scaleFactor}px`;
            }

            // Añadir el div al contenedor
            divContainer.appendChild(div);

            // Realizar la consulta para obtener el negocio correspondiente
            $.ajax({
                url: 'backend/get_business_by_local.php',
                type: 'GET',
                data: { localId: parseInt(local.id.replace('local', '')) }, // Asegurarse de que el localId sea un número
                success: function(response) {
                    console.log('Respuesta del servidor:', response); // Agregar este log
                    try {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        if (response && !response.error) {
                            // Añadir logo y nombre del negocio al div
                            if (response.logo) {
                                const img = document.createElement('img');
                                img.src = `data:image/jpeg;base64,${response.logo}`;
                                img.style.width = '30px';
                                img.style.height = '30px';
                                div.appendChild(img);
                            } else {
                                const img = document.createElement('img');
                                img.src = 'img/noPlazaLogo.png';
                                img.style.width = '30px';
                                img.style.height = '30px';
                                div.appendChild(img);
                            }

                            const name = document.createElement('p');
                            name.textContent = response.nombre || 'Sin asignar';
                            div.appendChild(name);

                            // Añadir clase para centrar el contenido
                            div.classList.add('center-content');
                        } else {
                            console.warn('No business found for local', local.id);
                        }
                    } catch (e) {
                        console.error('Error parsing JSON response:', e);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al obtener el negocio para el local', local.id, status, error);
                }
            });
        });
    }

    createDivsForLocals();
});