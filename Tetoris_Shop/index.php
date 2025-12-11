<?php
// index.php - Página de aterrizaje
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Tetoris Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .welcome-hero {
            background-color: #343a40; 
            color: white;
            padding: 5rem 0;
            text-align: center;
            border-bottom: 5px solid #FF0045; /* Rojo Teto */
        }
        .teto-btn-welcome {
            background-color: #FF0045;
            color: white;
            font-size: 1.25rem;
            padding: 0.75rem 2rem;
            border: none;
            transition: transform 0.2s;
        }
        .teto-btn-welcome:hover {
            transform: scale(1.05);
            background-color: #D6003A;
            color: white;
        }
    </style>
</head>
<body class="bg-light">

    <header class="welcome-hero">
        <div class="container">
            <h1 class="display-4 fw-bold">🥖 ¡Bienvenido a Tetoris Shop! 🥐</h1>
            <p class="lead mb-4">El sistema de inventario más divertido y eficiente, traído a ti por Teto Kasane.</p>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <p class="mb-3">Hola, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></p>
                <?php if($_SESSION['user_role'] == 'ADMINISTRADOR'): ?>
                    <a href="Dashboard.php" class="btn teto-btn-welcome">Ir al Dashboard</a>
                <?php elseif($_SESSION['user_role'] == 'CLIENTE'): ?>
                    <a href="ProductosUsuario.php" class="btn teto-btn-welcome">Ir a Comprar 🛒</a>
                <?php else: ?>
                    <a href="Productos.php" class="btn teto-btn-welcome">Ir al Inventario</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-outline-light ms-2">Cerrar Sesión</a>
            <?php else: ?>
                <a href="forms/Login.php" class="btn teto-btn-welcome">ACCEDER A TU CUENTA</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="container py-5 text-center">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="p-4 bg-white shadow-sm rounded border-top border-3 border-danger">
                    <h3>🥐 Calidad Quimérica</h3>
                    <p class="text-muted">Gestión de inventario precisa y sin errores.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white shadow-sm rounded border-top border-3 border-danger">
                    <h3>🥖 Stock Siempre Listo</h3>
                    <p class="text-muted">Control de stock mínimo y alertas automáticas.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white shadow-sm rounded border-top border-3 border-danger">
                    <h3>⚡ Velocidad UTAU</h3>
                    <p class="text-muted">Interfaz rápida optimizada para tus ventas.</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="py-4 text-center text-muted border-top">
        <p class="mb-1">&copy; 2025 Tetoris Shop | Powered by UTAU & Croissants.</p>
        <p class="small">
            <a href="forms/Register.php" style="color: #FF0045; text-decoration: none; font-weight: bold;">
                ¿Eres nuevo? Regístrate aquí.
            </a>
        </p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>