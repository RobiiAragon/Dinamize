document.addEventListener('DOMContentLoaded', () => {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const icon = darkModeToggle.querySelector('i');
    
    // Función para actualizar el modo
    const updateTheme = (isDark, withTransition = true) => {
        if (withTransition) {
            document.body.classList.add('transition');
            window.setTimeout(() => {
                document.body.classList.remove('transition');
            }, 300);
        }
        document.body.classList.toggle('dark-mode', isDark);
        document.documentElement.classList.toggle('dark-mode', isDark);
        document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
        
        // Actualizar icono
        icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        
        // Guardar preferencia en localStorage y cookies
        localStorage.setItem('darkMode', isDark);
        document.cookie = `darkMode=${isDark}; path=/; max-age=31536000`; // 1 año
    };
    
    // Verificar preferencia guardada
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
    const storedTheme = localStorage.getItem('darkMode');
    
    // Aplicar tema inicial sin transición
    if (storedTheme !== null) {
        updateTheme(storedTheme === 'true', false);
    } else {
        updateTheme(prefersDark.matches, false);
    }
    
    // Escuchar cambios en el botón
    darkModeToggle.addEventListener('click', () => {
        updateTheme(!document.body.classList.contains('dark-mode'));
    });
    
    // Escuchar cambios en preferencias del sistema
    prefersDark.addEventListener('change', (e) => {
        if (localStorage.getItem('darkMode') === null) {
            updateTheme(e.matches, false);
        }
    });
});