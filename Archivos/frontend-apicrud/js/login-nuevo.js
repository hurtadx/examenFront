document.addEventListener('DOMContentLoaded', function() {
    console.log('Login script v3 loaded (tabla roles)');
    const loginForm = document.getElementById('loginForm');
    
    // Agregar información de login para referencia
    const infoLogin = document.createElement('div');
    infoLogin.className = 'text-center mb-4';
    infoLogin.innerHTML = `
        <p class="small text-muted">Credenciales de demo:
        <br>Admin: admin / admin12345
        <br>Vendedor: vendedor / vende12355</p>
    `;
    
    // Insertar antes del formulario
    const heading = document.querySelector('.text-center h1');
    if (heading) {
        heading.parentNode.insertBefore(infoLogin, heading.nextSibling);
    }
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        
        if (!username || !password) {
            alert('Por favor complete todos los campos');
            return;
        }
        
        const apiUrl = 'http://localhost/backend-apiCrud/login.php';
        console.log('Sending request to:', apiUrl);
        
        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                usuario: username,
                contrasena: password  // Debe coincidir con el campo en la BD
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.text();
        })
        .then(text => {
            console.log('Raw response:', text);
            
            try {
                if (!text.trim()) {
                    throw new Error('Respuesta vacía del servidor');
                }
                
                const data = JSON.parse(text);
                console.log('Parsed data:', data);
                
                if (data.error) {
                    throw new Error(data.mensaje || 'Error en el servidor');
                }
                
                if (data.rol) {
                    // Login exitoso
                    localStorage.setItem('usuario', JSON.stringify(data));
                    localStorage.setItem('rol', data.rol);
                    localStorage.setItem('nombre', data.nombre);
                    
                    alert('¡Inicio de sesión exitoso! Rol: ' + data.rol);
                    
                    setTimeout(() => {
                        window.location.href = 'index.html';
                    }, 1000);
                } else {
                    alert('Las credenciales proporcionadas no son válidas');
                }
            } catch (error) {
                console.error('Error procesando respuesta:', error);
                alert('Error en la respuesta del servidor: ' + error.message);
            }
        })
        .catch(error => {
            console.error('Error de red:', error);
            alert('Error al conectar con el servidor: ' + error.message);
        });
    });
});