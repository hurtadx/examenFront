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
    
    // Verificar la tabla roles
    $sql = "SHOW TABLES LIKE 'roles'";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $tablaExiste = $stmt->rowCount() > 0;
    
    if ($tablaExiste) {
        // La tabla existe, contar usuarios
        $sql = "SELECT COUNT(*) as total FROM roles";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalUsuarios = $resultado['total'];
        
        // Obtener usuarios como ejemplo (sin mostrar contraseñas)
        $sql = "SELECT id, nombre, usuario FROM roles";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'status' => 'success',
            'database' => 'conectada',
            'tabla_roles' => 'existe',
            'total_usuarios' => $totalUsuarios,
            'usuarios' => $usuarios,
            'mensaje' => 'Tabla roles configurada correctamente',
            'instrucciones' => 'Use admin/admin12345 o vendedor/vende12355 para iniciar sesión'
        ]);
    } else {
        echo json_encode([
            'status' => 'warning',
            'database' => 'conectada',
            'tabla_roles' => 'no existe',
            'message' => 'La tabla "roles" no existe. Necesita importar el archivo inventario.sql'
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