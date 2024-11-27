// Función para cargar la imagen seleccionada
function loadImage(event) {
    const image = document.getElementById('logo-plaza');
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
        fetch('backend/update_plaza_info.php', {
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
            showMessage('Error al actualizar el logo de la plaza', 'error');
        });

        cropContainer.style.display = 'none';
        cropper.destroy();
    };
}

function showMessage(message, type = 'success') {
    const tooltipContainer = document.createElement('div');
    tooltipContainer.className = `tooltip-container ${type}`;
    const tooltipContent = document.createElement('div');
    tooltipContent.className = 'tooltip-content';
    tooltipContent.innerText = message;
    tooltipContainer.appendChild(tooltipContent);
    document.body.appendChild(tooltipContainer);

    setTimeout(() => {
        tooltipContainer.remove();
    }, 3000);
}
document.getElementById('plaza-info-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevenir el envío del formulario

    const formData = new FormData(this);

    fetch('backend/update_plaza_info.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showMessage(data.message);
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error al actualizar los datos de la plaza', 'error');
    });
});

function loadImage(event) {
    const image = document.getElementById('logo-plaza');
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
        fetch('backend/update_plaza_info.php', {
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
            showMessage('Error al actualizar el logo de la plaza', 'error');
        });

        cropContainer.style.display = 'none';
        cropper.destroy();
    };
}

function showMessage(message, type = 'success') {
    const tooltipContainer = document.createElement('div');
    tooltipContainer.className = `tooltip-container ${type}`;
    const tooltipContent = document.createElement('div');
    tooltipContent.className = 'tooltip-content';
    tooltipContent.innerText = message;
    tooltipContainer.appendChild(tooltipContent);
    document.body.appendChild(tooltipContainer);

    setTimeout(() => {
        tooltipContainer.remove();
    }, 3000);
}