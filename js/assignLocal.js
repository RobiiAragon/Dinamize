document.addEventListener('DOMContentLoaded', function() {
    const locales = document.querySelectorAll('.local-clickable');
    const popup = document.getElementById('popup');
    const closeBtn = document.getElementById('close-popup');
    const assignForm = document.getElementById('assign-form');

    // Función para mostrar el popup
    const showPopup = (event, localId) => {
        event.stopPropagation();
        document.getElementById('local_id').value = localId;
        popup.style.display = 'block';
    };

    // Agregar listeners a todos los locales
    locales.forEach(local => {
        const localId = local.id.replace('local', '');
        local.addEventListener('click', (e) => showPopup(e, localId));
        
        // Agregar listeners a los elementos internos del local
        const localContent = local.querySelector('.local');
        const localLogo = local.querySelector('.local-logo');
        const localName = local.querySelector('.local-name');
        
        if (localContent) {
            localContent.addEventListener('click', (e) => showPopup(e, localId));
        }
        if (localLogo) {
            localLogo.addEventListener('click', (e) => showPopup(e, localId));
        }
        if (localName) {
            localName.addEventListener('click', (e) => showPopup(e, localId));
        }
    });

    // Cerrar popup
    closeBtn.addEventListener('click', function() {
        popup.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === popup) {
            popup.style.display = 'none';
        }
    });

    // Modificar el manejo del formulario
    assignForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        // Primero verificar y liberar el local actual si existe
        fetch('backend/checkLocal.php', {
            method: 'POST',
            body: JSON.stringify({
                local_id: formData.get('local_id')
            }),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.hasNegocio) {
                // Si existe un negocio, primero liberamos el local
                return fetch('backend/liberarLocal.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        local_id: formData.get('local_id')
                    }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
            }
            return Promise.resolve();
        })
        .then(() => {
            // Ahora asignamos el nuevo negocio
            return fetch('backend/assignLocal.php', {
                method: 'POST',
                body: formData
            });
        })
        .then(response => response.text())
        .then(data => {
            alert('Cambios guardados exitosamente');
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al guardar los cambios');
        });
    });
});