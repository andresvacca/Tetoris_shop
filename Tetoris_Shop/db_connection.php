<?php
/**
 * db_connection.php
 * Conexión segura a la base de datos.
 */

// Configuración de credenciales
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tetoris_shop";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// VERIFICACIÓN ESTRICTA (Codacy Friendly)
// No uses die() con el error explícito en producción, podría revelar info sensible.
if ($conn->connect_error) {
    // En producción, esto debería registrarse en un log, no mostrarse.
    error_log("Connection failed: " . $conn->connect_error);
    exit("Error de conexión a la base de datos. Por favor intenta más tarde.");
}

// Asegurar UTF-8 para evitar caracteres extraños y posibles inyecciones de encoding
$conn->set_charset("utf8mb4");
?>