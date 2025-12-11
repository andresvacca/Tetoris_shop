<?php
/**
 * index.php
 * Página de inicio segura.
 */
session_start();

// EVITAR SUPERGLOBALS DIRECTAS EN EL HTML
// Codacy prefiere que asignes esto a variables locales primero
$user_id   = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['user_name'] ?? '';
$user_role = $_SESSION['user_role'] ?? '';

// Sanitizar salida por si acaso (aunque venga de sesión interna)
$safe_name = htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido a Tetoris Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .welcome-hero {
            background-color: #343a40; color: white; padding: 5rem 0;
            text-align: center; border-bottom: 5px solid #FF0045;
        }
        .teto-btn-welcome {
            background-color: #FF0045; color: white; font-size: 1.25rem;
            padding: 0.75rem 2rem; border: none; transition: transform 0.2s;
            text-decoration: none; display: inline-block;
        }
        .teto-btn-welcome:hover { transform: scale(1.05); background-color: #D6003A; color: white; }
    </style>
</head>
<body class="bg-light">

    <header class="welcome-hero">
        <div class="container">
            <h1 class="display-4 fw-bold">🥖 Tetoris Shop 🥐</h1>
            <p class="lead mb-4">Sistema de inventario Teto Kasane.</p>
            
            <?php if ($user_id): ?>
                <p class="mb-3">Hola, <strong><?php echo $safe_name; ?></strong></p>
                
                <?php if ($user_role === 'ADMINISTRADOR'): ?>
                    <a href="Dashboard.php" class="teto-btn-welcome">Dashboard</a>
                <?php elseif ($user_role === 'CLIENTE'): ?>
                    <a href="ProductosUsuario.php" class="teto-btn-welcome">Comprar 🛒</a>
                <?php else: ?>
                    <a href="Productos.php" class="teto-btn-welcome">Inventario</a>
                <?php endif; ?>
                
                <a href="logout.php" class="btn btn-outline-light ms-2">Salir</a>

            <?php else: ?>
                <a href="forms/Login.php" class="teto-btn-welcome">ACCEDER</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="container py-5 text-center">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="p-4 bg-white shadow-sm rounded border-top border-3 border-danger">
                    <h3>Calidad</h3><p class="text-muted">Gestión precisa.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white shadow-sm rounded border-top border-3 border-danger">
                    <h3>Stock</h3><p class="text-muted">Alertas automáticas.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white shadow-sm rounded border-top border-3 border-danger">
                    <h3>Velocidad</h3><p class="text-muted">Interfaz optimizada.</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="py-4 text-center text-muted border-top">
        <p class="mb-1">&copy; 2025 Tetoris Shop</p>
        <p class="small">
            <a href="forms/Register.php" style="color: #FF0045; text-decoration: none; font-weight: bold;">
                Regístrate aquí.
            </a>
        </p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>