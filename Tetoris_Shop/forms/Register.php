<?php
session_start();
// Nota: Las rutas están ajustadas con '../' (subir un nivel)
$register_process_path = '../register_process.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - Teto Shop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        crossorigin="anonymous">

    <link rel="stylesheet" href="../css/inventario.css">

    <style>
        /* Estilos específicos para la página de login/registro */
        .teto-auth-container {
            max-width: 450px;
            margin-top: 5vh;
        }

        .teto-auth-card {
            background-color: var(--teto-dark-card, #2c3e50);
            color: white;
            border-radius: 8px;
        }

        .teto-btn-secondary {
            background-color: var(--teto-pink, #ff69b4);
            border: none;
            transition: background-color 0.3s;
        }

        .teto-btn-secondary:hover {
            background-color: var(--teto-red, #e74c3c);
        }
    </style>
</head>

<body class="bg-dark d-flex justify-content-center align-items-center min-vh-100">

    <div class="teto-auth-container">

        <h1 class="text-center text-white mb-4">🚪 Registro de Teto Shop</h1>

        <div class="teto-auth-card p-4 border rounded shadow">
            <h2 class="mb-4 text-center">Crear Cuenta</h2>

            <?php
            // Mostrar mensaje de error si existe y limpiarlo de la sesión
            if (isset($_SESSION['register_error'])) {
                // Mejora de seguridad: usa htmlspecialchars
                echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['register_error']) . '</div>';
                unset($_SESSION['register_error']);
            }
            ?>

            <form action="<?php echo $register_process_path; ?>" method="POST">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn teto-btn-secondary w-100 fs-5 mt-4">Registrarse</button>
            </form>

            <p class="text-center mt-3">
                ¿Ya tienes cuenta?
                <a href="Login.php" class="teto-nav-link text-white fw-bold">Iniciar Sesión</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous">
    </script>
</body>

</html>