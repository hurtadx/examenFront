<?php
// Evitar errores en la salida
ini_set('display_errors', 0);
error_reporting(0);

// Encabezados CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Incluir archivo de conexión
    include("conexion.php");
    
    // Intentar conectar a la base de datos
    $conexion = Conectar();
    
    // Verificar la tabla usuarios
    $sql = "SHOW TABLES LIKE 'usuarios'";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $tablaExiste = $stmt->rowCount() > 0;
    
    if ($tablaExiste) {
        // La tabla existe, contar usuarios
        $sql = "SELECT COUNT(*) as total FROM usuarios";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalUsuarios = $resultado['total'];
        
        // Obtener primer usuario como ejemplo
        $sql = "SELECT id, nombre, usuario, rol FROM usuarios LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $primerUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'status' => 'success',
            'database' => 'conectada',
            'tabla_usuarios' => 'existe',
            'total_usuarios' => $totalUsuarios,
            'ejemplo_usuario' => $primerUsuario,
            'mensage' => 'Base de datos configurada correctamente'
        ]);
    } else {
        echo json_encode([
            'status' => 'warning',
            'database' => 'conectada',
            'tabla_usuarios' => 'no existe',
            'message' => 'La tabla "usuarios" no existe. Necesita importar el archivo inventario.sql'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'database' => 'error',
        'message' => 'Error conectando a la base de datos: ' . $e->getMessage()
    ]);
}
?>