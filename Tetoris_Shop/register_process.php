<?php
/**
 * register_process.php
 * Registro blindado para Codacy.
 */
session_start();
require_once __DIR__ . '/db_connection.php'; 

$request_method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($request_method === 'POST') {
    
    // 1. Obtener Rol Cliente de forma segura
    $rol_nombre = 'CLIENTE';
    $stmt_rol = $conn->prepare("SELECT id_rol FROM roles WHERE nombre_rol = ?");
    $stmt_rol->bind_param("s", $rol_nombre);
    $stmt_rol->execute();
    $stmt_rol->bind_result($id_rol);
    $stmt_rol->fetch();
    $stmt_rol->close();

    if (!$id_rol) { $id_rol = 3; } // Fallback seguro

    // 2. Capturar Inputs con filter_input
    $nombre   = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $apellido = filter_input(INPUT_POST, 'apellido', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email    = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $pass     = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
    
    if (!$nombre || !$email || !$pass) {
        $_SESSION['register_error'] = "Campos obligatorios vacíos.";
        header("Location: forms/Register.php");
        exit(0);
    }

    // 3. Validar Duplicados
    $stmt_check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?"); 
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $_SESSION['register_error'] = "Correo ya registrado.";
        $stmt_check->close();
        header("Location: forms/Register.php");
        exit(0);
    }
    $stmt_check->close();
    
    // 4. Insertar
    $pass_hash = password_hash($pass, PASSWORD_DEFAULT); 
    $stmt_ins = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, password_hash, id_rol) VALUES (?, ?, ?, ?, ?)");
    $stmt_ins->bind_param("ssssi", $nombre, $apellido, $email, $pass_hash, $id_rol);

    if ($stmt_ins->execute()) {
        $_SESSION['login_success'] = "Registro exitoso.";
        header("Location: forms/Login.php");
    } else {
        $_SESSION['register_error'] = "Error en el sistema.";
        header("Location: forms/Register.php");
    }
    
    $stmt_ins->close();
} else {
    header("Location: forms/Register.php");
    exit(0);
}
$conn->close();
?>