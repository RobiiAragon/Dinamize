document.addEventListener('DOMContentLoaded', function() {
    const local = document.getElementById('local1');
    const localContent = document.querySelector('.local');
    const localLogo = document.querySelector('.local-logo');
    const localName = document.querySelector('.local-name');
    const popup = document.getElementById('popup');
    const closeBtn = document.getElementById('close-popup');
    const assignForm = document.getElementById('assign-form');

    // Función para mostrar el popup
    const showPopup = (event) => {
        event.stopPropagation(); // Evitar propagación del evento
        popup.style.display = 'block';
    };

    // Agregar listeners a todos los elementos clickeables
    local.addEventListener('click', showPopup);
    
    if (localContent) {
        localContent.addEventListener('click', showPopup);
    }
    
    if (localLogo) {
        localLogo.addEventListener('click', showPopup);
    }
    
    if (localName) {
        localName.addEventListener('click', showPopup);
    }

    // Cerrar popup al hacer click en el botón de cerrar
    closeBtn.addEventListener('click', function() {
        popup.style.display = 'none';
    });

    // Cerrar popup al hacer click fuera de él
    window.addEventListener('click', function(event) {
        if (event.target === popup) {
            popup.style.display = 'none';
        }
    });

    assignForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('local_id', '1');

        fetch('backend/assignLocal.php', {
            method: 'POST',
            body: formData
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