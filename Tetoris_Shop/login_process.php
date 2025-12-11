<?php
session_start(); 
require_once 'db_connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email_ingresado = $conn->real_escape_string($_POST['email'] ?? '');
    $password_ingresada = $_POST['password'] ?? '';
    
    // Consulta optimizada uniendo tablas usuarios y roles
    $sql = "SELECT 
                u.id_usuario, 
                u.nombre, 
                u.password_hash, 
                r.nombre_rol 
            FROM usuarios u
            JOIN roles r ON u.id_rol = r.id_rol
            WHERE u.email = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email_ingresado);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        
        // Verificar contraseña encriptada
        if (password_verify($password_ingresada, $usuario['password_hash'])) {
            
            // Crear variables de sesión
            $_SESSION['user_id'] = $usuario['id_usuario'];
            $_SESSION['user_name'] = $usuario['nombre'];
            $_SESSION['user_role'] = $usuario['nombre_rol']; 
            
            // REDIRECCIÓN SEGÚN ROL
            switch (strtoupper($usuario['nombre_rol'])) { 
                case 'ADMINISTRADOR':
                    header("Location: Dashboard.php"); 
                    break;
                case 'EMPLEADO':
                    header("Location: Productos.php"); 
                    break;
                case 'CLIENTE':
                default:
                    header("Location: ProductosUsuario.php"); 
                    break;
            }
            exit();
            
        } else {
            $_SESSION['login_error'] = "Contraseña incorrecta.";
            header("Location: forms/Login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "El correo no existe.";
        header("Location: forms/Login.php");
        exit();
    }
    
    $stmt->close();
} else {
    header("Location: forms/Login.php");
    exit();
}
$conn->close();
?>