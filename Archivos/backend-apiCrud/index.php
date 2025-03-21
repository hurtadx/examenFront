<?php
// Evitar mostrar errores en la salida
ini_set('display_errors', 0);
error_reporting(0);

// Configuración de CORS para permitir peticiones desde el frontend
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Para peticiones OPTIONS (preflight de CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Incluir archivo de conexión y operaciones
require_once("conexion.php");
require_once("operaciones.php");

try {
    // Obtener la URL solicitada
    $requestUri = isset($_GET['url']) ? $_GET['url'] : '';
    $uri = explode('/', $requestUri);

    // Endpoint base para productos
    if ($uri[0] === 'productos') {
        $id = isset($uri[1]) ? $uri[1] : null;
        
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                // Si hay ID, obtener un producto específico
                if ($id) {
                    $producto = getProductById($id);
                    if ($producto) {
                        echo json_encode($producto);
                    } else {
                        http_response_code(404);
                        echo json_encode(['error' => 'Producto no encontrado']);
                    }
                } 
                // Si hay parámetro de búsqueda
                else if (isset($_GET['buscar'])) {
                    $termino = $_GET['buscar'];
                    $productos = searchProducts($termino);
                    echo json_encode($productos);
                }
                // Si no hay ID, obtener todos los productos
                else {
                    $productos = getAllProducts();
                    echo json_encode($productos);
                }
                break;
                
            case 'POST':
                // Recibir datos del cliente
                $input = file_get_contents('php://input');
                $data = json_decode($input, true);
                
                // Validar datos
                if (!isset($data['nombre']) || !isset($data['precio']) || !isset($data['stock'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Datos incompletos']);
                    exit;
                }
                
                // Agregar nuevo producto
                $result = createProduct(
                    $data['nombre'], 
                    isset($data['descripcion']) ? $data['descripcion'] : '', 
                    $data['precio'], 
                    $data['stock'], 
                    isset($data['imagen']) ? $data['imagen'] : 'img/producto-default.jpg'
                );
                
                if ($result) {
                    echo json_encode(['message' => 'Producto creado con exito']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Error al crear el producto']);
                }
                break;
                
            case 'PUT':
                // Actualizar producto existente
                $input = file_get_contents('php://input');
                $data = json_decode($input, true);
                
                if (!isset($data['id'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID no proporcionado']);
                    exit;
                }
                
                $result = updateProduct(
                    $data['id'],
                    isset($data['nombre']) ? $data['nombre'] : '',
                    isset($data['descripcion']) ? $data['descripcion'] : '', 
                    isset($data['precio']) ? $data['precio'] : 0, 
                    isset($data['stock']) ? $data['stock'] : 0, 
                    isset($data['imagen']) ? $data['imagen'] : 'img/producto-default.jpg'
                );
                
                if ($result) {
                    echo json_encode(['message' => 'Producto actualizado con exito']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Error al actualizar el producto']);
                }
                break;
                
            case 'DELETE':
                // Eliminar producto
                $input = file_get_contents('php://input');
                $data = json_decode($input, true);
                
                if (!isset($data['id'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID no proporcionado']);
                    exit;
                }
                
                $result = deleteProduct($data['id']);
                
                if ($result) {
                    echo json_encode(['message' => 'Producto eliminado con exito']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Error al eliminar el producto']);
                }
                break;
                
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
                break;
        }
    }
    // Endpoint para login
    else if ($uri[0] === 'login') {
        // Procesar login (ya implementado en login.php)
        include_once("login.php");
        exit;
    }
    // Ruta no encontrada
    else {
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint no encontrado']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor: ' . $e->getMessage()]);
}
?>