// FILE: js/svg-editor.js

document.addEventListener('DOMContentLoaded', function() {
    const svgEditor = document.getElementById('svg-editor');

    // Ejemplo de cómo agregar un rectángulo al SVG
    const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
    rect.setAttribute('x', 50);
    rect.setAttribute('y', 50);
    rect.setAttribute('width', 100);
    rect.setAttribute('height', 100);
    rect.setAttribute('fill', 'blue');
    svgEditor.appendChild(rect);

    // Aquí puedes agregar más lógica para el editor SVG
});