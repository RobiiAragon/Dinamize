// Función para habilitar la edición de campos
function enableEdit(fieldId) {
    const field = document.getElementById(fieldId);
    field.removeAttribute('readonly');
    field.focus();
    const saveButton = document.querySelector('.btn-save');
    saveButton.style.display = 'block';
}

// Función para mostrar el mensaje flotante
function showMessage(message, type = 'success') {
    const messageContainer = document.createElement('div');
    messageContainer.className = `tooltip-container ${type}`;
    const messageContent = document.createElement('div');
    messageContent.className = 'tooltip-content';
    messageContent.textContent = message;
    messageContainer.appendChild(messageContent);
    document.body.appendChild(messageContainer);

    setTimeout(() => {
        messageContainer.style.display = 'none';
    }, 2000);
}

// Función para cargar la imagen seleccionada
function loadImage(event) {
    const image = document.getElementById('profile-pic');
    const cropContainer = document.getElementById('crop-container');
    const cropperImage = document.getElementById('cropper');

    image.src = URL.createObjectURL(event.target.files[0]);
    cropContainer.style.display = 'block';
    cropperImage.src = image.src;

    const cropper = new Cropper(cropperImage, {
        aspectRatio: 1,
        viewMode: 1,
        autoCropArea: 1,
    });

    document.querySelector('.btn-crop').onclick = function() {
        const canvas = cropper.getCroppedCanvas();
        const croppedImage = canvas.toDataURL('image/jpeg');
        
        // Crear FormData y agregar la imagen
        const formData = new FormData();
        formData.append('croppedImage', croppedImage);

        // Enviar la imagen mediante AJAX
        fetch('backend/update_user_info.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                image.src = croppedImage;
                showMessage(data.message);
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Error al actualizar la foto de perfil', 'error');
        });

        cropContainer.style.display = 'none';
        cropper.destroy();
    };
}
// Función para cargar el logo seleccionado
function loadLogo(event) {
    const logo = document.getElementById('plaza-logo');
    const cropContainerLogo = document.getElementById('crop-container-logo');
    const cropperLogoImage = document.getElementById('cropper-logo');

    logo.src = URL.createObjectURL(event.target.files[0]);
    cropContainerLogo.style.display = 'block';
    cropperLogoImage.src = logo.src;

    const cropperLogo = new Cropper(cropperLogoImage, {
        aspectRatio: 1,
        viewMode: 1,
        autoCropArea: 1,
    });

    document.querySelector('.btn-crop').onclick = function() {
        const canvas = cropperLogo.getCroppedCanvas();
        const croppedLogo = canvas.toDataURL('image/jpeg');
        
        // Crear FormData y agregar el logo
        const formData = new FormData();
        formData.append('croppedLogo', croppedLogo);

        // Enviar el logo mediante AJAX
        fetch('backend/update_plaza_info.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                logo.src = croppedLogo;
                showMessage('Logo de la plaza actualizado correctamente');
            } else {
                showMessage('Error al actualizar el logo', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Error al actualizar el logo', 'error');
        });

        cropContainerLogo.style.display = 'none';
        cropperLogo.destroy();
    };
}

// Función para cambiar de sección
document.querySelectorAll('.sidebar ul li a').forEach(link => {
    link.addEventListener('click', function(event) {
        event.preventDefault();
        document.querySelectorAll('.sidebar ul li a').forEach(a => a.classList.remove('active'));
        link.classList.add('active');
        const sectionId = link.getAttribute('data-section');
        document.querySelectorAll('.main-content section').forEach(section => {
            section.style.display = 'none';
        });
        document.getElementById(sectionId).style.display = 'block';
    });
});

// Inicializar la primera sección visible
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.sidebar ul li a.active').click();

    // Ocultar el mensaje de éxito después de 2 segundos
    const successMessage = document.querySelector('.tooltip-container.success');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 2000);
    }
});