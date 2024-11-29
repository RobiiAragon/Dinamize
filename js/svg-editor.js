document.addEventListener('DOMContentLoaded', function() {
    const svgEditor = document.getElementById('svg-editor');
    
    // Ajustar el viewBox para asegurar que el SVG se vea completo
    svgEditor.setAttribute('viewBox', '0 0 553.1 727.11');
    
    // Agregar interactividad básica
    const locales = svgEditor.querySelectorAll('[id^="local"]');
    
    locales.forEach(local => {
        local.addEventListener('click', function(e) {
            const localId = this.id.replace('local', '');
            const negocioId = prompt('Ingrese el ID del negocio que desea asignar a este local:');
            
            if (negocioId) {
                $.ajax({
                    url: 'update_local.php',
                    type: 'POST',
                    data: {
                        localId: localId,
                        negocioId: negocioId
                    },
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.success) {
                            alert('Negocio asignado correctamente.');
                        } else {
                            alert('Error al asignar el negocio: ' + result.error);
                        }
                    },
                    error: function() {
                        alert('Error en la solicitud AJAX.');
                    }
                });
            }
        });
    });
});