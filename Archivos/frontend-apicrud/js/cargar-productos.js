document.addEventListener('DOMContentLoaded', function() {
    // Cargar los productos desde la API
    fetch('http://localhost/apiCrud/productos')
        .then(response => response.json())
        .then(data => {
            const productosContainer = document.querySelector('#productosContainer');
            
            if (productosContainer && data.length > 0) {
                productosContainer.innerHTML = '';
                
                data.forEach(producto => {
                    // Usar la imagen del producto si existe, sino una imagen por defecto
                    const imagenSrc = producto.imagen || 'img/producto-default.jpg';
                    
                    productosContainer.innerHTML += `
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100">
                                <img class="card-img-top" src="${imagenSrc}" alt="${producto.nombre}" onerror="this.src='img/producto-default.jpg'">
                                <div class="card-body">
                                    <h4 class="card-title">${producto.nombre}</h4>
                                    <h5>$${producto.precio}</h5>
                                    <p class="card-text">${producto.descripcion}</p>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-primary btn-agregar-carrito" data-id="${producto.id}">
                                        Añadir al carrito
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                // Agregar evento a los botones
                document.querySelectorAll('.btn-agregar-carrito').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        agregarAlCarrito(id);
                    });
                });
            } else if (productosContainer) {
                productosContainer.innerHTML = '<div class="col-12 text-center"><p>No hay productos disponibles en este momento.</p></div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const productosContainer = document.querySelector('#productosContainer');
            if (productosContainer) {
                productosContainer.innerHTML = '<div class="col-12 text-center"><p>Error al cargar productos. Intente más tarde.</p></div>';
            }
        });
    
    // Función para agregar al carrito
    function agregarAlCarrito(id) {
        // Obtener el carrito actual o inicializar uno nuevo
        let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
        
        // Buscar producto por ID
        fetch(`http://localhost/apiCrud/productos/${id}`)
            .then(response => response.json())
            .then(producto => {
                // Verificar si el producto ya está en el carrito
                const index = carrito.findIndex(item => item.id == id);
                
                if (index !== -1) {
                    // Si ya existe, aumentar cantidad
                    carrito[index].cantidad++;
                } else {
                    // Agregar nuevo producto
                    carrito.push({
                        id: producto.id,
                        nombre: producto.nombre,
                        precio: producto.precio,
                        cantidad: 1
                    });
                }
                
                // Guardar carrito actualizado
                localStorage.setItem('carrito', JSON.stringify(carrito));
                
                // Mostrar mensaje al usuario
                mostrarAlerta(`${producto.nombre} añadido al carrito`, 'success');
                
                // Actualizar contador del carrito si existe
                actualizarContadorCarrito();
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarAlerta('Error al añadir al carrito', 'error');
            });
    }
    
    // Función para mostrar alertas
    function mostrarAlerta(mensaje, tipo) {
        const alertaDiv = document.createElement('div');
        alertaDiv.className = tipo === 'error' ? 'alert alert-danger' : 'alert alert-success';
        alertaDiv.style.position = 'fixed';
        alertaDiv.style.top = '20px';
        alertaDiv.style.right = '20px';
        alertaDiv.style.zIndex = '9999';
        alertaDiv.textContent = mensaje;
        
        document.body.appendChild(alertaDiv);
        
        setTimeout(() => {
            alertaDiv.remove();
        }, 3000);
    }
    
    // Función para actualizar el contador del carrito
    function actualizarContadorCarrito() {
        const carrito = JSON.parse(localStorage.getItem('carrito')) || [];
        const cantidadTotal = carrito.reduce((total, item) => total + item.cantidad, 0);
        
        const contadorCarrito = document.getElementById('contador-carrito');
        if (contadorCarrito) {
            contadorCarrito.textContent = cantidadTotal;
        }
    }
    
    // Inicializar contador al cargar la página
    actualizarContadorCarrito();
});