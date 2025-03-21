// Funciones para gestionar productos

// URL base de la API - IMPORTANTE: verifica esta ruta
const API_URL = 'http://localhost/backend-apiCrud/index.php?url=productos';

// Función para cargar y mostrar todos los productos
function cargarProductos() {
    console.log('Cargando productos desde:', API_URL);
    
    fetch(API_URL)
        .then(response => {
            console.log('Respuesta status:', response.status);
            // Primero convertir a texto para debug
            return response.text().then(text => {
                console.log('Respuesta texto:', text);
                try {
                    // Intentar parsear como JSON
                    return text ? JSON.parse(text) : [];
                } catch (error) {
                    console.error('Error al parsear JSON:', error);
                    console.error('Texto recibido:', text);
                    throw new Error('Respuesta no válida del servidor');
                }
            });
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            const tbody = document.querySelector('#tablaProductos tbody');
            if (!tbody) {
                console.error('Elemento tbody no encontrado');
                return;
            }
            
            tbody.innerHTML = '';
            
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(producto => {
                    const rol = localStorage.getItem('rol');
                    const btnEliminar = rol === 'administrador' ? 
                        `<button class="btn btn-danger btn-sm btn-eliminar" data-id="${producto.id}">Eliminar</button>` : '';
                    
                    tbody.innerHTML += `
                        <tr>
                            <td>${producto.id}</td>
                            <td>${producto.nombre}</td>
                            <td>${producto.descripcion}</td>
                            <td>$${producto.precio}</td>
                            <td>${producto.stock}</td>
                            <td>
                                <button class="btn btn-primary btn-sm btn-editar" data-id="${producto.id}">Editar</button>
                                ${btnEliminar}
                            </td>
                        </tr>
                    `;
                });
                
                // Agregar eventos a los botones
                agregarEventosBotones();
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay productos disponibles</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Error al cargar los productos: ' + error.message, 'error');
        });
}

// Función para agregar un nuevo producto
function agregarProducto(event) {
    event.preventDefault();
    
    const nombre = document.getElementById('nombre').value;
    const descripcion = document.getElementById('descripcion').value;
    const precio = document.getElementById('precio').value;
    const stock = document.getElementById('stock').value;
    const imagen = document.getElementById('imagen').value || 'img/producto-default.jpg';
    
    if (!nombre || nombre === 'Seleccionar un producto') {
        mostrarAlerta('Por favor seleccione un producto', 'error');
        return;
    }
    
    if (!precio || !stock) {
        mostrarAlerta('Por favor complete todos los campos', 'error');
        return;
    }
    
    console.log('Enviando datos:', { nombre, descripcion, precio, stock, imagen });
    
    fetch(API_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            nombre: nombre,
            descripcion: descripcion,
            precio: precio,
            stock: stock,
            imagen: imagen
        })
    })
    .then(response => {
        console.log('Respuesta API:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Respuesta:', data);
        if (data.message && data.message.includes("exito")) {
            mostrarAlerta('Producto agregado correctamente', 'success');
            document.getElementById('formProducto').reset();
            
            // Redirigir a la lista de productos
            setTimeout(() => {
                window.location.href = 'listado-pro.html';
            }, 1500);
        } else {
            mostrarAlerta('Error al agregar el producto', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarAlerta('Error de conexión', 'error');
    });
}

// Función para editar un producto
function editarProducto(id) {
    fetch(`${API_URL}/${id}`)
        .then(response => response.json())
        .then(data => {
            // Redireccionar a la página de edición con los datos del producto
            localStorage.setItem('productoEditar', JSON.stringify(data));
            window.location.href = 'crear-pro.html?editar=' + id;
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Error al obtener datos del producto', 'error');
        });
}

// Función para eliminar un producto
function eliminarProducto(id) {
    if (confirm('¿Está seguro de eliminar este producto?')) {
        fetch(API_URL, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message && data.message.includes("exito")) {
                mostrarAlerta('Producto eliminado correctamente', 'success');
                cargarProductos();
            } else {
                mostrarAlerta('Error al eliminar el producto', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Error de conexión', 'error');
        });
    }
}

// Función para buscar productos
function buscarProductos(termino) {
    fetch(`${API_URL}?buscar=${termino}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#tablaProductos tbody');
            tbody.innerHTML = '';
            
            if (data.length > 0) {
                data.forEach(producto => {
                    const rol = localStorage.getItem('rol');
                    const btnEliminar = rol === 'administrador' ? 
                        `<button class="btn btn-danger btn-sm btn-eliminar" data-id="${producto.id}">Eliminar</button>` : '';
                    
                    tbody.innerHTML += `
                        <tr>
                            <td>${producto.id}</td>
                            <td>${producto.nombre}</td>
                            <td>${producto.descripcion}</td>
                            <td>$${producto.precio}</td>
                            <td>${producto.stock}</td>
                            <td>
                                <button class="btn btn-primary btn-sm btn-editar" data-id="${producto.id}">Editar</button>
                                ${btnEliminar}
                            </td>
                        </tr>
                    `;
                });
                
                // Agregar eventos a los botones
                agregarEventosBotones();
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No se encontraron productos</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Error al buscar productos', 'error');
        });
}

// Función para agregar eventos a los botones
function agregarEventosBotones() {
    // Botones editar
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            editarProducto(id);
        });
    });
    
    // Botones eliminar
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            eliminarProducto(id);
        });
    });
}

// Función para mostrar alertas
function mostrarAlerta(mensaje, tipo) {
    const alertaDiv = document.createElement('div');
    alertaDiv.className = tipo === 'error' ? 'alert alert-danger' : 'alert alert-success';
    alertaDiv.textContent = mensaje;
    
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertaDiv, container.firstChild);
    
    setTimeout(() => {
        alertaDiv.remove();
    }, 3000);
}

// Inicialización de páginas
document.addEventListener('DOMContentLoaded', function() {
    // Verificar el rol del usuario
    const rol = localStorage.getItem('rol');
    if (rol !== 'administrador' && rol !== 'vendedor') {
        // Si no hay usuario autenticado, redirigir al login
        window.location.href = 'login.html';
        return;
    }
    
    // Si es vendedor, ocultar los botones de eliminar
    if (rol === 'vendedor') {
        // Esto se puede hacer con CSS o manualmente
        const styleElement = document.createElement('style');
        styleElement.textContent = `
            .btn-eliminar {
                display: none !important;
            }
        `;
        document.head.appendChild(styleElement);
    }

    // Comprobar si estamos en la página de listado de productos
    if (document.querySelector('#tablaProductos')) {
        cargarProductos();
        
        // Agregar evento al buscador
        const inputBuscar = document.getElementById('buscarProducto');
        if (inputBuscar) {
            inputBuscar.addEventListener('keyup', function() {
                const termino = this.value.trim();
                if (termino.length > 2) {
                    buscarProductos(termino);
                } else if (termino.length === 0) {
                    cargarProductos();
                }
            });
        }
    }
    
    // Comprobar si estamos en la página de crear/editar producto
    const formProducto = document.getElementById('formProducto');
    if (formProducto) {
        // Verificar si estamos editando o creando
        const urlParams = new URLSearchParams(window.location.search);
        const idEditar = urlParams.get('editar');
        
        if (idEditar) {
            // Estamos editando, cargar datos del producto
            const productoEditar = JSON.parse(localStorage.getItem('productoEditar'));
            if (productoEditar) {
                document.getElementById('titulo').textContent = 'Editar Producto';
                
                // Seleccionar la opción en el select que coincida con el nombre del producto
                const selectNombre = document.getElementById('nombre');
                const options = Array.from(selectNombre.options);
                const optionToSelect = options.find(item => item.value === productoEditar.nombre);
                if (optionToSelect) {
                    optionToSelect.selected = true;
                    
                    // Disparar el evento change para actualizar la imagen
                    const event = new Event('change');
                    selectNombre.dispatchEvent(event);
                } else {
                    // Si no existe la opción, añadirla
                    const newOption = new Option(productoEditar.nombre, productoEditar.nombre);
                    selectNombre.add(newOption);
                    newOption.selected = true;
                }
                
                document.getElementById('precio').value = productoEditar.precio;
                document.getElementById('stock').value = productoEditar.stock;
                document.getElementById('descripcion').value = productoEditar.descripcion;
                document.getElementById('imagen').value = productoEditar.imagen;
                
                // Mostrar la imagen del producto
                document.getElementById('imagen-pro').src = productoEditar.imagen;
                
                // Modificar el evento del formulario para actualizar
                formProducto.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    fetch(`${API_URL}/${idEditar}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id: idEditar,
                            nombre: document.getElementById('nombre').value,
                            descripcion: document.getElementById('descripcion').value,
                            precio: document.getElementById('precio').value,
                            stock: document.getElementById('stock').value,
                            imagen: document.getElementById('imagen').value
                        })
                    })
                    .then(response => {
                        console.log('Respuesta API:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Respuesta:', data);
                        if (data.message && data.message.includes("exito")) {
                            mostrarAlerta('Producto actualizado correctamente', 'success');
                            
                            // Limpiar datos de edición
                            localStorage.removeItem('productoEditar');
                            
                            // Redirigir a la lista de productos
                            setTimeout(() => {
                                window.location.href = 'listado-pro.html';
                            }, 1500);
                        } else {
                            mostrarAlerta('Error al actualizar el producto', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        mostrarAlerta('Error de conexión', 'error');
                    });
                });
            }
        } else {
            // Estamos creando un nuevo producto
            formProducto.addEventListener('submit', agregarProducto);
        }
    }
});