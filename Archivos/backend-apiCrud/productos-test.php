<?php
// Evitar errores en la salida
ini_set('display_errors', 0);
error_reporting(0);

// Encabezados CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Incluir archivos necesarios
    include("conexion.php");
    include("operaciones.php");
    
    // Intentar conectar a la base de datos
    $conexion = Conectar();
    
    // Verificar la tabla productos
    $sql = "SHOW TABLES LIKE 'productos'";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $tablaExiste = $stmt->rowCount() > 0;
    
    if ($tablaExiste) {
        // Obtener todos los productos
        $productos = getAllProducts();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Consulta de productos exitosa',
            'count' => count($productos),
            'productos' => $productos
        ]);
    } else {
        echo json_encode([
            'status' => 'warning',
            'message' => 'La tabla "productos" no existe. Necesita importar el archivo inventario.sql'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>