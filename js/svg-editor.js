document.addEventListener('DOMContentLoaded', function() {
    const svgEditor = document.getElementById('svg-editor');
    const modal = document.getElementById('modal');
    const closeButton = document.querySelector('.close-button');
    const businessList = document.getElementById('business-list');
    let selectedLocalId = null;

    // Ajustar el viewBox para asegurar que el SVG se vea completo
    svgEditor.setAttribute('viewBox', '0 0 553.1 727.11');

    // Agregar interactividad básica
    const locales = svgEditor.querySelectorAll('[id^="local"]');

    locales.forEach(local => {
        local.addEventListener('click', function(e) {
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
        console.log('Asignando negocio:', businessId, 'al local:', selectedLocalId); // Mensaje de depuración
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
                } else {
                    alert('Error al asignar el negocio: ' + result.error);
                }
                modal.style.display = 'none';
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', status, error); // Mensaje de depuración
                alert('Error en la solicitud AJAX.');
                modal.style.display = 'none';
            }
        });
    }
});