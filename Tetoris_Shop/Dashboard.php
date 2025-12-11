<?php
session_start();
require_once 'db_connection.php';

// Seguridad: Solo Administrador
if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMINISTRADOR') {
    header("Location: forms/Login.php");
    exit();
}

// ================= CÁLCULOS REALES =================
// 1. Ingresos del mes actual
$mes_actual = date('m');
$anio_actual = date('Y');
$sql_ingresos = "SELECT SUM(total_venta) as total FROM ventas WHERE MONTH(fecha_venta) = ? AND YEAR(fecha_venta) = ?";
$stmt = $conn->prepare($sql_ingresos);
$stmt->bind_param("ss", $mes_actual, $anio_actual);
$stmt->execute();
$ingresos = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();

// 2. Total Órdenes (Histórico)
$ordenes = $conn->query("SELECT COUNT(*) as cant FROM ventas")->fetch_assoc()['cant'];

// 3. Stock Bajo (Productos con stock <= stock_minimo)
$bajos = $conn->query("SELECT COUNT(*) as cant FROM productos WHERE stock_actual <= stock_minimo")->fetch_assoc()['cant'];

// 4. Clientes Totales
$clientes = $conn->query("SELECT COUNT(*) as cant FROM usuarios WHERE id_rol = (SELECT id_rol FROM roles WHERE nombre_rol='CLIENTE')")->fetch_assoc()['cant'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Teto - Resumen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/inventario.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <div class="container-fluid">
        <header class="py-3 mb-4 border-bottom teto-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-white teto-title">📈 Panel de Control</h1>
                <nav class="nav">
                    <a href="Productos.php" class="nav-link teto-nav-link">Inventario</a>
                    <a href="#" class="nav-link teto-nav-link active">Dashboard</a>
                    <a href="Ventas.php" class="nav-link teto-nav-link">Ventas</a>
                </nav>
                <a href="logout.php" class="btn teto-btn-primary">Salir</a>
            </div>
        </header>

        <main class="teto-main-content">
            <h2 class="mb-4 text-white">Resumen de Negocio</h2>

            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="p-4 rounded shadow teto-card">
                        <h5 class="text-muted">Ingresos (Este Mes)</h5>
                        <p class="h2 text-success">$<?php echo number_format($ingresos, 2); ?></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 rounded shadow teto-card">
                        <h5 class="text-muted">Total Ventas</h5>
                        <p class="h2 text-primary"><?php echo $ordenes; ?></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 rounded shadow teto-card <?php echo ($bajos > 0) ? 'teto-row-low' : ''; ?>">
                        <h5 class="text-muted">Alertas Stock</h5>
                        <p class="h2 text-warning"><?php echo $bajos; ?></p>
                        <?php if($bajos > 0): ?>
                            <small class="text-danger fw-bold">¡Revisar Inventario!</small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 rounded shadow teto-card">
                        <h5 class="text-muted">Clientes Registrados</h5>
                        <p class="h2 text-info"><?php echo $clientes; ?></p>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="p-4 rounded shadow teto-card">
                        <h3 class="mb-3">Tendencia de Ventas (Demo)</h3>
                        <canvas id="ventasChart" height="150"></canvas>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="p-4 rounded shadow teto-card">
                        <h3 class="mb-3">Top Productos (Demo)</h3>
                        <canvas id="productosChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Configuración de Chart.js (Estética Teto)
        document.addEventListener('DOMContentLoaded', function() {
            const ctx1 = document.getElementById('ventasChart').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'],
                    datasets: [{
                        label: 'Ventas ($)',
                        data: [1200, 1900, 3000, 500], // Datos ejemplo
                        borderColor: '#FF0045',
                        backgroundColor: 'rgba(255, 0, 69, 0.1)',
                        fill: true
                    }]
                }
            });

            const ctx2 = document.getElementById('productosChart').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['Pan', 'Leche', 'Huevos'],
                    datasets: [{
                        data: [50, 20, 30], // Datos ejemplo
                        backgroundColor: ['#FF0045', '#3F4750', '#EDA7BA']
                    }]
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>