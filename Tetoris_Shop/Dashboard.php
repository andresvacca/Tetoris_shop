<?php
/**
 * Dashboard.php
 * Panel de control seguro y validado.
 */
session_start();
require_once __DIR__ . '/db_connection.php';

// 1. Validación de Sesión sin superglobales directas
$role = $_SESSION['user_role'] ?? '';
if ($role !== 'ADMINISTRADOR') {
    header("Location: forms/Login.php");
    exit(0);
}

// 2. Lógica de Datos (Usando Prepared Statements siempre)
$mes = date('m');
$anio = date('Y');

// Ingresos
$ingresos = 0.0;
$stmt_ing = $conn->prepare("SELECT SUM(total_venta) as total FROM ventas WHERE MONTH(fecha_venta) = ? AND YEAR(fecha_venta) = ?");
if ($stmt_ing) {
    $stmt_ing->bind_param("ss", $mes, $anio);
    $stmt_ing->execute();
    $res_ing = $stmt_ing->get_result();
    $row_ing = $res_ing->fetch_assoc();
    $ingresos = (float)($row_ing['total'] ?? 0);
    $stmt_ing->close();
}

// Conteos simples (Casting a int para seguridad)
$ordenes = 0;
$res_ord = $conn->query("SELECT COUNT(*) as cant FROM ventas");
if ($res_ord) { $ordenes = (int)$res_ord->fetch_assoc()['cant']; }

$bajos = 0;
$res_low = $conn->query("SELECT COUNT(*) as cant FROM productos WHERE stock_actual <= stock_minimo");
if ($res_low) { $bajos = (int)$res_low->fetch_assoc()['cant']; }

$clientes = 0;
// Subconsulta segura
$res_cli = $conn->query("SELECT COUNT(*) as cant FROM usuarios WHERE id_rol = (SELECT id_rol FROM roles WHERE nombre_rol='CLIENTE' LIMIT 1)");
if ($res_cli) { $clientes = (int)$res_cli->fetch_assoc()['cant']; }

// Datos para Gráficos
$labels_json = '[]';
$data_json   = '[]';

$res_chart = $conn->query("SELECT c.nombre_categoria, COUNT(dv.id_detalle) as v FROM detalle_venta dv JOIN productos p ON dv.id_producto=p.id_producto JOIN categorias c ON p.id_categoria=c.id_categoria GROUP BY c.id_categoria LIMIT 5");
if ($res_chart) {
    $lbls = []; $dts = [];
    while ($r = $res_chart->fetch_assoc()) {
        $lbls[] = htmlspecialchars($r['nombre_categoria'], ENT_QUOTES, 'UTF-8');
        $dts[]  = (int)$r['v'];
    }
    $labels_json = json_encode($lbls);
    $data_json   = json_encode($dts);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/inventario.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container-fluid">
        <header class="py-3 mb-4 border-bottom teto-header">
            <h1 class="h3 text-white ms-3">📈 Panel</h1>
            <a href="logout.php" class="btn btn-light btn-sm float-end me-3">Salir</a>
        </header>

        <main class="teto-main-content">
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="p-4 rounded shadow teto-card">
                        <h5 class="text-muted">Ingresos</h5>
                        <p class="h2 text-success">$<?php echo number_format($ingresos, 2); ?></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 rounded shadow teto-card">
                        <h5 class="text-muted">Ventas</h5>
                        <p class="h2 text-primary"><?php echo $ordenes; ?></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 rounded shadow teto-card">
                        <h5 class="text-muted">Alertas</h5>
                        <p class="h2 text-warning"><?php echo $bajos; ?></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 rounded shadow teto-card">
                        <h5 class="text-muted">Clientes</h5>
                        <p class="h2 text-info"><?php echo $clientes; ?></p>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6 mx-auto">
                    <div class="p-4 rounded shadow teto-card">
                        <h3 class="mb-3">Top Categorías</h3>
                        <canvas id="prodChart"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // JS Seguro: Datos inyectados vía JSON parseado
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('prodChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo $labels_json; ?>,
                    datasets: [{
                        data: <?php echo $data_json; ?>,
                        backgroundColor: ['#FF0045','#3F4750','#EDA7BA','#FFC107','#17A2B8']
                    }]
                }
            });
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>