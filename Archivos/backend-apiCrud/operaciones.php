<?php
// Evitar mostrar errores en la salida
ini_set('display_errors', 0);
error_reporting(0);

include("conexion.php");

// Conexión a la base de datos
try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Función login (mantenla si ya funciona)
function login($usuario, $contrasena) {
    require_once 'conexion.php';
    $conexion = Conectar();
    
    $sql = "SELECT * FROM roles WHERE usuario = :usuario";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($contrasena === $row['contrasena']) {
            return [
                'id' => $row['id'],
                'nombre' => $row['nombre'],
                'usuario' => $row['usuario'],
                'rol' => $row['nombre']
            ];
        }
    }
    
    return false;
}

// Obtener todos los productos
function getAllProducts() {
    try {
        $conexion = Conectar();
        $stmt = $conexion->prepare("SELECT * FROM productos ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error al obtener productos: " . $e->getMessage());
        return [];
    }
}

// Obtener producto por ID
function getProductById($id) {
    try {
        $conexion = Conectar();
        $stmt = $conexion->prepare("SELECT * FROM productos WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error al obtener producto por ID: " . $e->getMessage());
        return null;
    }
}

// Buscar productos
function searchProducts($termino) {
    try {
        $conexion = Conectar();
        $termino = "%$termino%";
        $stmt = $conexion->prepare("SELECT * FROM productos WHERE nombre LIKE :termino OR descripcion LIKE :termino");
        $stmt->bindParam(':termino', $termino);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error al buscar productos: " . $e->getMessage());
        return [];
    }
}

// Crear nuevo producto
function createProduct($nombre, $descripcion, $precio, $stock, $imagen) {
    try {
        $conexion = Conectar();
        $stmt = $conexion->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, imagen) 
                              VALUES (:nombre, :descripcion, :precio, :stock, :imagen)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':imagen', $imagen);
        return $stmt->execute();
    } catch(PDOException $e) {
        error_log("Error al crear producto: " . $e->getMessage());
        return false;
    }
}

// Actualizar producto
function updateProduct($id, $nombre, $descripcion, $precio, $stock, $imagen) {
    try {
        $conexion = Conectar();
        $stmt = $conexion->prepare("UPDATE productos SET 
                              nombre = :nombre, 
                              descripcion = :descripcion, 
                              precio = :precio, 
                              stock = :stock, 
                              imagen = :imagen 
                              WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':imagen', $imagen);
        return $stmt->execute();
    } catch(PDOException $e) {
        error_log("Error al actualizar producto: " . $e->getMessage());
        return false;
    }
}

// Eliminar producto
function deleteProduct($id) {
    try {
        $conexion = Conectar();
        $stmt = $conexion->prepare("DELETE FROM productos WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    } catch(PDOException $e) {
        error_log("Error al eliminar producto: " . $e->getMessage());
        return false;
    }
}
?>