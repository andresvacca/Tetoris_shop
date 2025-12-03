<?php
session_start();
// Nota: Las rutas están ajustadas con '../' (subir un nivel)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - Teto Shop</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
        crossorigin="anonymous">
    
    <link rel="stylesheet" href="../css/inventario.css">4
    
    <style>
        /* Estilos específicos para la página de login/registro */
        .teto-auth-container {
            max-width: 450px;
            margin-top: 50px;
        }
        .teto-auth-card {
            background-color: var(--teto-dark-card); /* Ajustar según tu CSS */
            color: white;
        }
    </style>
</head>
<body class="bg-dark">

    <div class="container d-flex justify-content-center">
        <div class="teto-auth-container">
            
            <h1 class="text-center text-white mb-4">🚪 Registro de Teto Shop</h1>
            
            <div class="teto-auth-card p-4 border rounded shadow">
                <h2 class="mb-4 text-center">Crear Cuenta</h2>

                <?php 
                    // Mostrar mensaje de error si existe y limpiarlo de la sesión
                    if (isset($_SESSION['register_error'])) {
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['register_error'] . '</div>';
                        unset($_SESSION['register_error']);
                    }
                ?>

                <form action="../register_process.php" method="POST">
                    
                    <div class="mb-3">
                        <label for="nombre_usuario" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control teto-input" id="nombre_usuario" name="nombre_usuario" required>
                    </div>

                    <div class="mb-3">
                        <label for="correo_usuario" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control teto-input" id="correo_usuario" name="correo_usuario" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control teto-input" id="password" name="password" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono_usuario" class="form-label">Teléfono</label>
                            <input type="number" class="form-control teto-input" id="telefono_usuario" name="telefono_usuario" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tipo_doc" class="form-label">Tipo Doc</label>
                            <select class="form-select teto-input" id="tipo_doc" name="tipo_doc" required>
                                <option value="CC" selected>CC (Cédula)</option>
                                <option value="TI">TI (Tarjeta Identidad)</option>
                                <option value="CE">CE (Cédula Extranjería)</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn teto-btn-secondary w-100 fs-5 mt-2">Registrarse</button>
                </form>

                <p class="text-center mt-3">
                    ¿Ya tienes cuenta? 
                    <a href="../Login.php" class="teto-nav-link">Iniciar Sesión</a>
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