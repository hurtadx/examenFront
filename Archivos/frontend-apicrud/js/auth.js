document.addEventListener('DOMContentLoaded', function() {
    // No verificar autenticación en login.html
    if (window.location.href.includes('login.html')) {
        return;
    }
    
    // Verificar si el usuario está autenticado
    const usuario = localStorage.getItem('usuario');
    const rol = localStorage.getItem('rol');
    
    if (!usuario || !rol) {
        // Si no hay usuario en localStorage, redirigir al login
        window.location.href = 'login.html';
        return;
    }
    
    // Mostrar nombre del usuario en el sidebar si existe el elemento
    const userNameElement = document.getElementById('nombreUsuario');
    if (userNameElement) {
        userNameElement.textContent = localStorage.getItem('nombre') || 'Usuario';
    }
    
    // Configurar elementos según el rol
    if (rol === 'vendedor') {
        // Ocultar elementos que solo son para administradores
        document.querySelectorAll('.admin-only').forEach(el => {
            el.style.display = 'none';
        });
    }
    
    // Agregar evento al botón de cerrar sesión
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            localStorage.removeItem('usuario');
            localStorage.removeItem('rol');
            localStorage.removeItem('nombre');
            window.location.href = 'login.html';
        });
    }
});