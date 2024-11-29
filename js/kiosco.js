document.addEventListener('DOMContentLoaded', function() {
    const locals = document.querySelectorAll('.local');
    const popup = document.createElement('div');
    popup.classList.add('info-popup');
    document.body.appendChild(popup);

    locals.forEach(local => {
        local.addEventListener('click', function() {
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

            popup.innerHTML = `
                <h2>${nombre}</h2>
                <img src="${logo}" alt="${nombre}">
                <p><strong>Descripción:</strong> ${descripcion}</p>
                <p><strong>Teléfono:</strong> ${telefono}</p>
                <p><strong>Horario:</strong> ${horarioApertura} - ${horarioCierre}</p>
                <p><strong>Sitio Web:</strong> <a href="${sitioWeb}" target="_blank">${sitioWeb}</a></p>
                <p><strong>Facebook:</strong> <a href="${facebook}" target="_blank">${facebook}</a></p>
                <p><strong>Instagram:</strong> <a href="${instagram}" target="_blank">${instagram}</a></p>
            `;
            popup.style.display = 'block';
        });
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

function formatTime(time) {
    const [hour, minute] = time.split(':');
    let hours = parseInt(hour);
    const minutes = parseInt(minute);
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // La hora '0' debe ser '12'
    return `${hours}:${minutes < 10 ? '0' + minutes : minutes} ${ampm}`;
}