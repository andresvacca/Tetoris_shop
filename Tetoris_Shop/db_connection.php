<?php
/**
 * db_connection.php
 * Script para establecer la conexión a la base de datos 'tetoris_shop'.
 */

// 1. Configuración de la Base de Datos
$servername = "localhost"; // Generalmente es 'localhost' si usas XAMPP/WAMP
$username = "root";        // Usuario por defecto de XAMPP/WAMP
$password = "";            // Contraseña por defecto de XAMPP/WAMP (vacía)
$dbname = "tetoris_shop";  // El nombre de tu base de datos SQL

// 2. Crear la Conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// 3. Verificar la Conexión
if ($conn->connect_error !== null) {
    // Si hay un error, el script muere y muestra el error.
    die("❌ Error de Conexión a la Base de Datos: " . $conn->connect_error);
}

// Opcional: Establecer el conjunto de caracteres a UTF-8 para evitar problemas con tildes y ñ
$conn->set_charset("utf8");

// La variable $conn ya contiene el objeto de conexión exitosa.
// Este archivo no produce ninguna salida HTML, solo establece $conn.

// echo "✅ Conexión exitosa a la base de datos 'tetoris_shop'."; // Línea solo para probar la conexión
?>