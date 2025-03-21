<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Mostrar todos los errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Prueba de PHP</h1>";
echo "<p>PHP version: " . phpversion() . "</p>";

// Probar conexión a la base de datos
echo "<h2>Probando conexión a la base de datos</h2>";
try {
    // Datos de conexión - ajusta según tu configuración
    $host = "localhost";
    $dbname = "inventario";
    $username = "root";
    $password = "";
    
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>Conexión a la base de datos exitosa</p>";
    
    // Probar consulta
    $stmt = $db->query("SHOW TABLES");
    echo "<p>Tablas en la base de datos:</p>";
    echo "<ul>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>" . $row[array_key_first($row)] . "</li>";
    }
    echo "</ul>";
} catch(PDOException $e) {
    echo "<p style='color:red'>Error de conexión: " . $e->getMessage() . "</p>";
}

echo json_encode([
    "status" => "success",
    "message" => "API funcionando correctamente",
    "timestamp" => date('Y-m-d H:i:s')
]);
?>