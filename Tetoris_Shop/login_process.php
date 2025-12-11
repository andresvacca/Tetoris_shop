<?php
/**
 * login_process.php
 * Autenticación segura para Codacy (Sin Superglobals directas).
 */
session_start(); 
require_once __DIR__ . '/db_connection.php'; 

// Validar método de solicitud
$request_method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($request_method === 'POST') {
    
    // 1. Sanitizar Inputs (Evita "Direct Use of Superglobals")
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    // La contraseña no se sanitiza, se captura cruda pero con filter_input
    $pass  = filter_input(INPUT_POST, 'password', FILTER_DEFAULT); // FILTER_DEFAULT mantiene caracteres especiales
    
    if (!$email || !$pass) {
        $_SESSION['login_error'] = "Datos incompletos.";
        header("Location: forms/Login.php");
        exit(0);
    }

    // 2. Consulta Preparada
    $sql = "SELECT u.id_usuario, u.nombre, u.password_hash, r.nombre_rol 
            FROM usuarios u
            JOIN roles r ON u.id_rol = r.id_rol
            WHERE u.email = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        
        // 3. Verificar Hash
        if (password_verify($pass, $usuario['password_hash'])) {
            
            // Regenerar ID de sesión para evitar "Session Fixation"
            session_regenerate_id(true);

            $_SESSION['user_id']   = $usuario['id_usuario'];
            $_SESSION['user_name'] = $usuario['nombre'];
            $_SESSION['user_role'] = $usuario['nombre_rol'];
            
            $rol = strtoupper($usuario['nombre_rol']);
            
            // Redirección segura con exit numérico
            if ($rol === 'ADMINISTRADOR') {
                header("Location: Dashboard.php");
            } elseif ($rol === 'EMPLEADO') {
                header("Location: Productos.php");
            } else {
                header("Location: ProductosUsuario.php");
            }
            exit(0);
            
        } else {
            $_SESSION['login_error'] = "Credenciales inválidas.";
            header("Location: forms/Login.php");
            exit(0);
        }
    } else {
        $_SESSION['login_error'] = "Credenciales inválidas."; // Mensaje genérico por seguridad
        header("Location: forms/Login.php");
        exit(0);
    }
    
    $stmt->close();
} else {
    header("Location: forms/Login.php");
    exit(0);
}
$conn->close();
?>