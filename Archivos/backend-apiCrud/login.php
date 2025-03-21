<?php
// Evitar que PHP muestre errores en la salida
ini_set('display_errors', 0);
error_reporting(0);

// Encabezados CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar solicitudes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Para login
try {
    // Incluir conexión
    include("conexion.php");
    $conexion = Conectar();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener y validar datos
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);
        
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new Exception("JSON inválido recibido");
        }
        
        if (empty($data['usuario']) || empty($data['contrasena'])) {
            throw new Exception("Datos incompletos");
        }
        
        // IMPORTANTE: La tabla se llama "roles", no "usuarios"
        $sql = "SELECT * FROM roles WHERE usuario = :usuario";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':usuario', $data['usuario']);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // IMPORTANTE: El campo se llama "contrasena", no "password"
            if ($data['contrasena'] === $row['contrasena']) {
                // Devolver datos del usuario
                echo json_encode([
                    'id' => $row['id'],
                    'nombre' => $row['nombre'], // Este es el rol (administrador/vendedor)
                    'usuario' => $row['usuario'],
                    'rol' => $row['nombre'] // Usamos nombre como rol
                ]);
            } else {
                throw new Exception("Contraseña incorrecta");
            }
        } else {
            throw new Exception("Usuario no encontrado");
        }
    } else {
        throw new Exception("Método no permitido");
    }
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'error' => true,
        'mensaje' => $e->getMessage()
    ]);
}
?>