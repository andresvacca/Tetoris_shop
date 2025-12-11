<?php
/**
 * logout.php
 * Cierre de sesión seguro.
 */
session_start();

// Destruir variables
$_SESSION = array();

// Borrar cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir sesión
session_destroy();

// Redirección final
header("Location: forms/Login.php");
exit(0);
?>