<?php
session_start();

// El Dashboard es solo para el rol más alto
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMINISTRADOR') {
    // Si no es Administrador, redirigir
    header("Location: Login.php?error=acceso_denegado");
    exit();
}

// Si llega aquí, el usuario es ADMINISTRADOR.
require_once 'db_connection.php';
// ... el resto de la lógica del dashboard ...
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Teto - Resumen del Negocio</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">

    <link rel="stylesheet" href="css/inventario.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <div class="container-fluid">

        <header class="py-3 mb-4 border-bottom teto-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <h1 class="h3 mb-0 text-white teto-title">📈 Panel de Control - Teto</h1>

                <nav class="nav">
                    <a href="productos.php" class="nav-link teto-nav-link">Inventario</a>
                    <a href="dashboard.php" class="nav-link teto-nav-link active" aria-current="page">Dashboard</a>
                    <a href="ventas.php" class="nav-link teto-nav-link">Ventas</a>
                </nav>

                <button type="button" class="btn teto-btn-primary">Cerrar Sesión</button>
            </div>
        </header>

        <main class="teto-main-content">
            <h2 class="mb-4 text-white">Resumen General</h2>

            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="p-4 rounded shadow teto-card">
                        <h5 class="text-muted">Ingresos del Mes</h5>
                        <p class="h2 text-success">$<?php echo number_format(4520.50, 2); ?></p>
                        <small class="text-muted">▲ 12% respecto al mes anterior</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 rounded shadow teto-card">
                        <h5 class="text-muted">Total Órdenes</h5>
                        <p class="h2 text-primary">859</p>
                        <small class="text-muted">▲ 5% respecto al mes anterior</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 rounded shadow teto-card teto-row-low">
                        <h5 class="text-muted">Stock Bajo</h5>
                        <p class="h2 text-warning">18</p>
                        <small class="text-muted">¡Revisar inventario urgente!</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 rounded shadow teto-card">
                        <h5 class="text-muted">Clientes Nuevos</h5>
                        <p class="h2 text-info">95</p>
                        <small class="text-muted">Meta: 100 clientes nuevos</small>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="p-4 rounded shadow teto-card">
                        <h3 class="mb-3">Ventas Últimos 6 Meses</h3>
                        <canvas id="ventasChart" height="150"></canvas>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="p-4 rounded shadow teto-card">
                        <h3 class="mb-3">Productos Top 5</h3>
                        <canvas id="productosChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-3 mt-4 border-top text-center teto-footer">
            <p class="mb-0 text-muted">&copy; 2025 Sistema de Inventario Teto | UTAU-Powered</p>
        </footer>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous">
    </script>
    <script>
        // Lógica de Gráficos con Chart.js
        document.addEventListener('DOMContentLoaded', function() {
            const tetoRed = '#FF0045'; // Usamos el color principal de Teto
            const tetoPink = '#EDA7BA'; // Usamos el color secundario de Teto

            // Gráfico de Ventas (Barra)
            const ventasCtx = document.getElementById('ventasChart').getContext('2d');
            new Chart(ventasCtx, {
                type: 'bar',
                data: {
                    labels: ['Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov'],
                    datasets: [{
                        label: 'Ingresos ($)',
                        data: [2800, 3200, 4100, 3800, 4200, 4520],
                        backgroundColor: tetoRed,
                        borderColor: tetoRed,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Gráfico de Productos (Dona)
            const productosCtx = document.getElementById('productosChart').getContext('2d');
            new Chart(productosCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pan Francés', 'Cruasanes', 'Baguettes', 'Mantecadas', 'Galletas', 'Leche', 'Huevos'],
                    datasets: [{
                        label: 'Unidades Vendidas',
                        data: [450, 300, 180, 150, 90, 89, 14],
                        backgroundColor: [tetoRed, tetoPink, '#3F4750', '#8B0000', '#FF8C00', '#09B4ED', '#596AED'],
                    }]
                },
                options: {
                    responsive: true,
                }
            });
        });
    </script>
</body>

</html>