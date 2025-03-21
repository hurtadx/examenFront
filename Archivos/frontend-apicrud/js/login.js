document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        
        // Validación básica
        if (!username || !password) {
            mostrarAlerta('Por favor complete todos los campos', 'error');
            return;
        }
        
        // CORRECCIÓN: URL correcta según la estructura de carpetas
        fetch('http://localhost/backend-apiCrud/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                usuario: username,
                contrasena: password  // El backend espera "contrasena", no "password"
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error de servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.rol) {
                // Guardar información del usuario en localStorage
                localStorage.setItem('usuario', JSON.stringify(data));
                localStorage.setItem('rol', data.rol);
                localStorage.setItem('nombre', data.nombre);
                
                mostrarAlerta('Inicio de sesión exitoso', 'success');
                
                // Redireccionar al dashboard después de 1 segundo
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 1000);
            } else {
                mostrarAlerta('Usuario o contraseña incorrectos', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Error al conectar con el servidor', 'error');
        });
    });
    
    // CORRECCIÓN: Función de alerta mejorada para evitar errores DOM
    function mostrarAlerta(mensaje, tipo) {
        const alertDiv = document.createElement('div');
        alertDiv.className = tipo === 'error' ? 'alert alert-danger' : 'alert alert-success';
        alertDiv.textContent = mensaje;
        
        // Seleccionar el contenedor correcto para la alerta
        const container = document.querySelector('.p-5');
        const heading = document.querySelector('.text-center');
        
        // Insertar después del encabezado y antes del formulario
        if (container && heading) {
            container.insertBefore(alertDiv, heading.nextSibling);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }
    }
});