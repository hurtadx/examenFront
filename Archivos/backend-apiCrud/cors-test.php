<?php
// Configuración CORS completa
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400'); // 24 horas

// Responder inmediatamente a las solicitudes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Responder a POST con un usuario de prueba
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Usuario de prueba - solo para verificar que funciona
    echo json_encode([
        'id' => 1,
        'nombre' => 'Usuario de Prueba',
        'usuario' => 'admin',
        'rol' => 'administrador'
    ]);
    exit;
}

// Respuesta predeterminada para GET
echo json_encode([
    'status' => 'success',
    'message' => 'API de prueba CORS funcionando correctamente',
    'timestamp' => date('Y-m-d H:i:s')
]);
?>