<?php
// Mostrar errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuración de CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Para peticiones OPTIONS (preflight de CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Conexión a la base de datos
try {
    $host = "localhost";
    $dbname = "inventario";
    $username = "root";
    $password = "";
    
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener productos (método GET)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $db->query("SELECT * FROM productos");
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($productos);
    }
    
    // Agregar producto (método POST)
    else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $db->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, imagen) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['stock'],
            $data['imagen'] ?? 'img/producto-default.jpg'
        ]);
        
        if ($result) {
            echo json_encode(['message' => 'Producto creado con exito']);
        } else {
            echo json_encode(['error' => 'Error al crear el producto']);
        }
    }
    
    // Actualizar producto (método PUT)
    else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $db->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, imagen = ? WHERE id = ?");
        $result = $stmt->execute([
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['stock'],
            $data['imagen'] ?? 'img/producto-default.jpg',
            $data['id']
        ]);
        
        if ($result) {
            echo json_encode(['message' => 'Producto actualizado con exito']);
        } else {
            echo json_encode(['error' => 'Error al actualizar el producto']);
        }
    }
    
    // Eliminar producto (método DELETE)
    else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $db->prepare("DELETE FROM productos WHERE id = ?");
        $result = $stmt->execute([$data['id']]);
        
        if ($result) {
            echo json_encode(['message' => 'Producto eliminado con exito']);
        } else {
            echo json_encode(['error' => 'Error al eliminar el producto']);
        }
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>