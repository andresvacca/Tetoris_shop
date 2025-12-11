<?php
session_start(); 
// La ruta para el script de procesamiento debe ser correcta
$login_process_path = '../login_process.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Seguro | Tetoris Shop</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet" 
        crossorigin="anonymous">
    <link rel="stylesheet" href="../CSS/inventario.css"> 
    
    <style>
        /* Estilos centrados para el contenedor de autenticación */
        .teto-auth-container { 
            max-width: 420px; 
            margin-top: 5vh; 
        }
        .teto-auth-card { 
            background-color: var(--teto-dark-card, #2c3e50); /* Dark Grey */
            color: white; 
            border: 1px solid var(--teto-red, #c0392b); /* Red accent */
            border-radius: 8px;
        }
        .teto-btn-primary {
            background-color: var(--teto-pink, #ff69b4); /* Pink/Red Button */
            border: none;
            transition: background-color 0.3s;
        }
        .teto-btn-primary:hover {
            background-color: var(--teto-red, #e74c3c); 
        }
    </style>
</head>
<body class="bg-dark d-flex justify-content-center align-items-center min-vh-100">

    <div class="teto-auth-container">
        
        <h1 class="text-center text-white mb-4">🔐 Sistema de Inventario</h1>
        
        <div class="teto-auth-card p-5 shadow-lg">
            
            <h2 class="mb-4 text-center">Iniciar Sesión</h2>

            <?php 
                // Mensaje de éxito (ej. después de un registro exitoso)
                if (isset($_SESSION['login_success'])) {
                    echo '<div class="alert alert-success fw-bold" role="alert">¡' . htmlspecialchars($_SESSION['login_success']) . '!</div>';
                    unset($_SESSION['login_success']);
                }
                // Mensaje de error (credenciales incorrectas o acceso denegado)
                if (isset($_SESSION['login_error'])) {
                    echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                    unset($_SESSION['login_error']);
                }
            ?>

            <form action="<?php echo $login_process_path; ?>" method="POST" novalidate>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico (Email)</label>
                    <input type="email" class="form-control" id="email" name="email" required autocomplete="email" placeholder="ejemplo@tetoris.shop">
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password" placeholder="Mínimo 8 caracteres">
                </div>
                
                <button type="submit" class="btn teto-btn-primary w-100 fs-5 mt-3">Acceder al Sistema</button>
            </form>

            <p class="text-center mt-4 text-muted">
                ¿Problemas para acceder? <a href="#" class="text-white text-decoration-none border-bottom">Recuperar Contraseña</a>
            </p>
            <p class="text-center">
                ¿Nuevo usuario? <a href="Register.php" class="teto-nav-link text-white fw-bold">Regístrate aquí</a>
            </p>
        </div>
    </div> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            crossorigin="anonymous">
    </script>
</body>
</html>