<?php
session_start();
/**
 * register_process.php
 * Script para registrar nuevos usuarios en la tabla 'usuario'.
 */
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Capturar y sanear los datos
    $nombre = $conn->real_escape_string($_POST['nombre_usuario'] ?? '');
    $correo = $conn->real_escape_string($_POST['correo_usuario'] ?? '');
    $telefono = intval($_POST['telefono_usuario'] ?? 0);
    $tipo_doc = $conn->real_escape_string($_POST['tipo_doc'] ?? ''); 
    $password_plana = $_POST['password'] ?? '';

    // Asignación de Rol por defecto: USUARIO (Ajusta el ID según tu tabla 'rol')
    $id_rol = 3; 

    // 2. Validación de Correo Duplicado (Control de Errores)
    $sql_check = "SELECT id FROM usuario WHERE correo_usuario = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $correo);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $_SESSION['register_error'] = "El correo electrónico ya está registrado. Intenta iniciar sesión.";
        $stmt_check->close();
        $conn->close();
        header("Location: Register.php");
        exit();
    }
    $stmt_check->close();
    
    // 3. Hashing de la Contraseña (¡CRUCIAL para la seguridad!)
    $password_hasheada = password_hash($password_plana, PASSWORD_DEFAULT);
    
    // 4. Preparar la sentencia de INSERCIÓN (CREATE)
    // Asumimos que tienes la columna 'password_hasheada'
    $sql_insert = "INSERT INTO usuario (nombre_usuario, correo_usuario, telefono_usuario, `tipo doc`, password_hasheada, id_rol) 
                   VALUES (?, ?, ?, ?, ?, ?)";
    
    // 5. Preparar y enlazar los parámetros
    // 'ssisii' -> string, string, integer, string, integer, integer (siendo 's' para el hash)
    // Nota: El tipo de la columna 'password_hasheada' debe ser VARCHAR(255) en la BD.
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ssissi", 
    $nombre, 
    $correo, 
    $telefono, 
    $tipo_doc, 
    $password_hasheada, 
    $id_rol
);

    // 6. Ejecutar la sentencia
    if ($stmt_insert->execute()) {
        // Registro exitoso: Redirigir al Login para que el usuario inicie sesión
        $_SESSION['login_success'] = "¡Registro exitoso! Por favor, inicia sesión.";
        $stmt_insert->close();
        $conn->close();
        header("Location: forms/Login.php");
        exit();
    } else {
        // Error de BD
        error_log("Error al registrar usuario: " . $stmt_insert->error);
        $_SESSION['register_error'] = "Error al registrar. Intenta de nuevo más tarde.";
        $stmt_insert->close();
        $conn->close();
        header("Location: Register.php");
        exit();
    }
} else {
    // Si se accede sin POST
    $conn->close();
    header("Location: Register.php");
    exit();
}
?>