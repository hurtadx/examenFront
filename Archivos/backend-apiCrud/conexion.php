<?php
// Configuración de la conexión a la base de datos
function Conectar() {
    $host = "localhost";
    $dbname = "inventario";
    $username = "root";
    $password = ""; // Normalmente vacío en XAMPP
    
    try {
        $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conexion;
    } catch(PDOException $e) {
        // Registrar error pero no mostrarlo
        error_log("Error de conexión: " . $e->getMessage());
        throw new Exception("Error de conexión a la base de datos");
    }
}
?>