document.addEventListener('DOMContentLoaded', function() {
    const locals = document.querySelectorAll('.local');
    const popup = document.createElement('div');
    popup.classList.add('info-popup');
    document.body.appendChild(popup);

    locals.forEach(local => {
        // Agregar el event listener al div local y a la imagen
        const handleClick = function() {
            const nombre = local.getAttribute('data-nombre');
            const logo = local.getAttribute('data-logo');
            const descripcion = local.getAttribute('data-descripcion');
            const telefono = local.getAttribute('data-telefono');
            const horarioApertura = formatTime(local.getAttribute('data-horario-apertura'));
            const horarioCierre = formatTime(local.getAttribute('data-horario-cierre'));
            let sitioWeb = local.getAttribute('data-sitio-web');
            let facebook = local.getAttribute('data-facebook');
            let instagram = local.getAttribute('data-instagram');

            // Verificar y completar URLs si es necesario
            sitioWeb = sitioWeb && !/^https?:\/\//i.test(sitioWeb) ? `http://${sitioWeb}` : sitioWeb;
            facebook = facebook && !/^https?:\/\//i.test(facebook) ? `http://${facebook}` : facebook;
            instagram = instagram && !/^https?:\/\//i.test(instagram) ? `http://${instagram}` : instagram;

            // Todos los valores vienen de lo que escribe cada negocio: se escapan antes de
            // insertarlos como HTML para evitar XSS en el kiosco público.
            const e = escapeHtml;
            const link = (url) => url ? `<a href="${e(url)}" target="_blank" rel="noopener noreferrer">${e(url)}</a>` : '';
            popup.innerHTML = `
                <h2>${e(nombre)}</h2>
                <img src="${/^data:image\//.test(logo || '') ? e(logo) : ''}" alt="${e(nombre)}">
                <p><strong>Descripción:</strong> ${e(descripcion)}</p>
                <p><strong>Teléfono:</strong> ${e(telefono)}</p>
                <p><strong>Horario:</strong> ${e(horarioApertura)} - ${e(horarioCierre)}</p>
                <p><strong>Sitio Web:</strong> ${link(sitioWeb)}</p>
                <p><strong>Facebook:</strong> ${link(facebook)}</p>
                <p><strong>Instagram:</strong> ${link(instagram)}</p>
            `;
            popup.style.display = 'block';
        };

        // Agregar el evento al div local
        local.addEventListener('click', handleClick);
        
        // Agregar el evento a la imagen dentro del div
        const localImage = local.querySelector('img');
        if (localImage) {
            localImage.addEventListener('click', function(e) {
                e.stopPropagation(); // Evitar que el evento se dispare dos veces
                handleClick();
            });
        }
    });

    popup.addEventListener('click', function() {
        popup.style.display = 'none';
    });

    document.addEventListener('click', function(event) {
        if (!popup.contains(event.target) && !event.target.classList.contains('local')) {
            popup.style.display = 'none';
        }
    });
});

function escapeHtml(value) {
    return String(value == null ? '' : value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function formatTime(time) {
    if (!time || !time.includes(':')) return '';
    const [hour, minute] = time.split(':');
    let hours = parseInt(hour);
    const minutes = parseInt(minute);
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    return `${hours}:${minutes < 10 ? '0' + minutes : minutes} ${ampm}`;
}