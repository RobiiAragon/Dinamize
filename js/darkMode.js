document.addEventListener('DOMContentLoaded', () => {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const icon = darkModeToggle.querySelector('i');
    
    // Función para actualizar el modo
    const updateTheme = (isDark, withTransition = false) => {
        // Aplicar o remover transición solo cuando se especifica
        if (withTransition) {
            document.body.classList.add('transition');
            document.documentElement.classList.add('transition');
            
            // Remover clase de transición después de que termine
            setTimeout(() => {
                document.body.classList.remove('transition');
                document.documentElement.classList.remove('transition');
            }, 300);
        }

        // Actualizar clases de modo oscuro
        document.body.classList.toggle('dark-mode', isDark);
        document.documentElement.classList.toggle('dark-mode', isDark);
        document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
        
        // Actualizar icono
        icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        
        // Guardar preferencia
        localStorage.setItem('darkMode', isDark);
        document.cookie = `darkMode=${isDark}; path=/; max-age=31536000`;
    };
    
    // Aplicar tema inicial sin transición
    const storedTheme = localStorage.getItem('darkMode');
    if (storedTheme !== null) {
        updateTheme(storedTheme === 'true', false);
    } else {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
        updateTheme(prefersDark.matches, false);
    }
    
    // Aplicar transición solo en el click del botón
    darkModeToggle.addEventListener('click', () => {
        updateTheme(!document.body.classList.contains('dark-mode'), true);
    });
});