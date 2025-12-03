<?php
session_start(); // 1. Iniciar la sesión al principio de todo
/**
 * login_process.php
 * Script de autenticación segura y redirección basada en roles.
 */
require_once 'db_connection.php'; // Incluir la conexión a la BD

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Capturar y sanear los datos de entrada
    $correo_ingresado = $conn->real_escape_string($_POST['correo_usuario'] ?? '');
    $password_ingresada = $_POST['password'] ?? '';
    
    // 3. Consulta segura: Buscar usuario, contraseña hasheada y nombre del rol.
    // Usamos JOIN para obtener el nombre legible del rol a partir del id_rol.
    // Reemplaza la consulta actual con esta:
$sql = "SELECT u.id, u.nombre_usuario, u.password_hasheada, r.nombre_rol AS rol_nombre 
        FROM usuario u
        JOIN rol r ON u.id = r.id
        WHERE u.correo_usuario = ?";
            
    $stmt = $conn->prepare($sql);
    
    // Verificar si la preparación falló (por un error en el SQL o en la BD)
    // BLOQUE DE DIAGNÓSTICO TEMPORAL
    if ($stmt === false) {
        // DETENEMOS LA EJECUCIÓN y mostramos el error EXACTO de MySQL
        die("Error de MySQL al preparar la consulta: " . $conn->error . 
            "<br>Consulta SQL: " . $sql);
    }
    // FIN DEL BLOQUE DE DIAGNÓSTICO TEMPORAL
    
    $stmt->bind_param("s", $correo_ingresado);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        
        // 4. Verificar la contraseña hasheada
        // Compara la contraseña plana ingresada con el hash almacenado en la BD.
        if (password_verify($password_ingresada, $usuario['password_hasheada'])) {
            
            // 5. ¡Credenciales Correctas! Iniciar la sesión y almacenar datos clave.
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_name'] = $usuario['nombre_usuario'];
            $_SESSION['user_role'] = $usuario['rol_nombre']; // ADMINISTRADOR, EMPLEADO, USUARIO
            
            // 6. Redirección basada en el Rol (Validación de acceso)
            switch (strtoupper($_SESSION['user_role'])) { // Usamos strtoupper para manejar mayúsculas/minúsculas
                case 'ADMINISTRADOR':
                    // Redirigir al panel de control completo
                    header("Location: dashboard.php"); 
                    break;
                case 'EMPLEADO':
                    // Redirigir al inventario y ventas
                    header("Location: productos.php"); 
                    break;
                case 'USUARIO':
                default:
                    // Redirigir al catálogo de compras
                    header("Location: Productos_Usuario_Normal.php");
                    break;
            }
            exit();
            
        } else {
            // Contraseña incorrecta
            $_SESSION['login_error'] = "Correo o contraseña incorrectos.";
            $stmt->close();
            $conn->close();
            header("Location: forms/Login.php");
            exit();
        }
    } else {
        // Correo no encontrado
        $_SESSION['login_error'] = "Correo o contraseña incorrectos.";
        $stmt->close();
        $conn->close();
        header("Location: forms/Login.php");
        exit();
    }
    
    $stmt->close(); // Cerrar el statement si no salió antes
} else {
    // 7. Si alguien intenta acceder directamente sin usar el formulario POST
    header("Location: forms/Login.php");
    exit();
}

$conn->close(); // Cerrar la conexión al final
?>