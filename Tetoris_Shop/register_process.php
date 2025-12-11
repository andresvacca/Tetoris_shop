<?php
session_start();
require_once 'db_connection.php'; 

// Rutas
$register_page = 'forms/Register.php'; 
$login_page = 'forms/Login.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Obtener ID del rol 'CLIENTE'
    $rol_defecto = 'CLIENTE';
    $sql_rol = "SELECT id_rol FROM roles WHERE nombre_rol = ?";
    $stmt_rol = $conn->prepare($sql_rol);
    $stmt_rol->bind_param("s", $rol_defecto);
    $stmt_rol->execute();
    $stmt_rol->bind_result($id_rol); 
    $stmt_rol->fetch();
    $stmt_rol->close();

    // Fallback de seguridad si no encuentra el rol
    if (!$id_rol) $id_rol = 3; 

    // 2. Capturar datos del formulario
    $nombre = $conn->real_escape_string($_POST['nombre'] ?? '');
    $apellido = $conn->real_escape_string($_POST['apellido'] ?? '');
    $correo = $conn->real_escape_string($_POST['email'] ?? ''); 
    $password_plana = $_POST['password'] ?? '';
    
    if (empty($nombre) || empty($correo) || empty($password_plana)) {
        $_SESSION['register_error'] = "Todos los campos son obligatorios.";
        header("Location: " . $register_page);
        exit();
    }

    // 3. Verificar si el correo ya existe
    $stmt_check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?"); 
    $stmt_check->bind_param("s", $correo);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $_SESSION['register_error'] = "Este correo ya está registrado.";
        $stmt_check->close();
        header("Location: " . $register_page);
        exit();
    }
    $stmt_check->close();
    
    // 4. Crear usuario
    $password_hash = password_hash($password_plana, PASSWORD_DEFAULT); 
    $stmt_insert = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, password_hash, id_rol) VALUES (?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("ssssi", $nombre, $apellido, $correo, $password_hash, $id_rol);

    if ($stmt_insert->execute()) {
        $_SESSION['login_success'] = "¡Cuenta creada! Inicia sesión.";
        header("Location: " . $login_page);
    } else {
        $_SESSION['register_error'] = "Error al registrar. Inténtalo de nuevo.";
        header("Location: " . $register_page);
    }
    
    $stmt_insert->close();
    $conn->close();
} else {
    header("Location: " . $register_page);
    exit();
}
?>