<?php
session_start(); 
// La ruta para el script de procesamiento se ajusta a "../login_process.php"
$login_process_path = '../login_process.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Teto Shop</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet" 
        crossorigin="anonymous">
    
    <link rel="stylesheet" href="../CSS/inventario.css"> 
    
    <style>
        /* Estilos específicos */
        .teto-auth-container { 
            max-width: 400px; 
            margin-top: 100px; 
        }
        .teto-auth-card { 
            background-color: var(--teto-dark-card); 
            color: white; 
        }
    </style>
</head>
<body class="bg-dark">

    <div class="container d-flex justify-content-center">
        <div class="teto-auth-container">
            
            <h1 class="text-center text-white mb-4">🔑 Acceso al Sistema</h1>
            
            <div class="teto-auth-card p-4 border rounded shadow">
                
                <h2 class="mb-4 text-center">Iniciar Sesión</h2>

                <?php 
                    // 1. Mostrar mensaje de éxito después del registro
                    if (isset($_SESSION['login_success'])) {
                        echo '<div class="alert alert-success" role="alert">' . $_SESSION['login_success'] . '</div>';
                        unset($_SESSION['login_success']);
                    }
                    // 2. Mostrar mensaje de error por credenciales incorrectas o acceso denegado
                    if (isset($_SESSION['login_error'])) {
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['login_error'] . '</div>';
                        unset($_SESSION['login_error']);
                    }
                ?>

                <form action="<?php echo $login_process_path; ?>" method="POST">
                    
                    <div class="mb-3">
                        <label for="correo_usuario" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control teto-input" id="correo_usuario" name="correo_usuario" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control teto-input" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn teto-btn-primary w-100 fs-5 mt-4">Acceder</button>
                </form>

                <p class="text-center mt-3">
                    ¿No tienes cuenta? 
                    <a href="Register.php" class="teto-nav-link">Regístrate aquí</a>
                </p>
            </div>
        </div>
    </div> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
            crossorigin="anonymous">
    </script>
</body>
</html>