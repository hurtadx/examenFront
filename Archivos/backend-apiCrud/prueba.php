<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Incluir conexión para probar
include("conexion.php");

// Intentar conectar
$conexion = Conectar();

// Verificar conexión
if ($conexion) {
    echo json_encode([
        "status" => "success",
        "message" => "Conexión a la base de datos exitosa",
        "timestamp" => date('Y-m-d H:i:s')
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Error al conectar a la base de datos",
        "timestamp" => date('Y-m-d H:i:s')
    ]);
}
?>