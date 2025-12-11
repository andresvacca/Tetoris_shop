<?php
/**
 * Ventas.php
 * Historial de transacciones y reporte de ingresos.
 */
session_start();
require_once 'db_connection.php'; 

// 1. VERIFICACIÓN DE SEGURIDAD (Roles permitidos)
$allowed_roles = ['ADMINISTRADOR', 'EMPLEADO'];
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowed_roles)) {
    // Si no tiene permiso, lo mandamos al login
    header("Location: forms/Login.php");
    exit();
}

// 2. LÓGICA DE FILTROS (Fechas)
// Por defecto: Desde el día 1 del mes actual hasta hoy
$fecha_inicio = isset($_GET['inicio']) ? $_GET['inicio'] : date('Y-m-01');
$fecha_fin    = isset($_GET['fin']) ? $_GET['fin'] : date('Y-m-d');

// Ajustar fecha fin para incluir todo el día (hasta las 23:59:59)
$fecha_fin_sql = $fecha_fin . ' 23:59:59';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas Teto - Registro de Transacciones</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
        crossorigin="anonymous">
        
    <link rel="stylesheet" href="css/inventario.css"> 
</head>
<body>

    <div class="container-fluid">
        
        <header class="py-3 mb-4 border-bottom teto-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <h1 class="h3 mb-0 text-white teto-title">💸 Registro de Ventas - Teto</h1>
                
                <nav class="nav">
                    <a href="Productos.php" class="nav-link teto-nav-link">Inventario</a>
                    <?php if($_SESSION['user_role'] === 'ADMINISTRADOR'): ?>
                        <a href="Dashboard.php" class="nav-link teto-nav-link">Dashboard</a>
                    <?php endif; ?>
                    <a href="Ventas.php" class="nav-link teto-nav-link active" aria-current="page">Ventas</a>
                </nav>
                
                <a href="logout.php" class="btn teto-btn-primary">Cerrar Sesión</a>
            </div>
        </header>

        <main class="teto-main-content">

            <section class="mb-4 p-3 border rounded teto-card">
                <h2>Filtros y Reportes</h2>
                
                <form method="GET" action="Ventas.php" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="fecha-inicio" class="form-label">Desde:</label>
                        <input type="date" class="form-control teto-input" id="fecha-inicio" name="inicio" 
                               value="<?php echo htmlspecialchars($fecha_inicio); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="fecha-fin" class="form-label">Hasta:</label>
                        <input type="date" class="form-control teto-input" id="fecha-fin" name="fin" 
                               value="<?php echo htmlspecialchars($fecha_fin); ?>">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn w-100 btn-primary">Aplicar Filtros</button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn w-100 teto-btn-secondary" onclick="window.print()">
                            Imprimir Reporte
                        </button>
                    </div>
                </form>
            </section>

            <section class="teto-card p-3 border rounded">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="mb-0">Transacciones del Periodo</h2>
                    <?php
                        // Consulta para sumar el total del periodo seleccionado
                        $sql_sum = "SELECT SUM(total_venta) as total_periodo FROM ventas WHERE fecha_venta BETWEEN ? AND ?";
                        $stmt_sum = $conn->prepare($sql_sum);
                        $stmt_sum->bind_param("ss", $fecha_inicio, $fecha_fin_sql);
                        $stmt_sum->execute();
                        $res_sum = $stmt_sum->get_result();
                        $row_sum = $res_sum->fetch_assoc();
                        $total_periodo = $row_sum['total_periodo'] ?? 0;
                        $stmt_sum->close();
                    ?>
                    <h4 class="text-success">Total Generado: $<?php echo number_format($total_periodo, 2); ?></h4>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover teto-table">
                        <thead class="teto-thead">
                            <tr>
                                <th scope="col">ID Venta</th>
                                <th scope="col">Fecha y Hora</th>
                                <th scope="col">Cliente / Usuario</th>
                                <th scope="col">Método</th>
                                <th scope="col">Total</th>
                                <th scope="col">Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // 3. CONSULTA SQL DINÁMICA
                            // Unimos con la tabla usuarios para saber quién compró (si está registrado)
                            $sql = "SELECT v.id_venta, v.fecha_venta, v.total_venta, v.metodo_pago, u.nombre, u.apellido
                                    FROM ventas v
                                    LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
                                    WHERE v.fecha_venta BETWEEN ? AND ?
                                    ORDER BY v.fecha_venta DESC";
                            
                            $stmt = $conn->prepare($sql);
                            // "ss" significa que pasamos dos strings (fechas)
                            $stmt->bind_param("ss", $fecha_inicio, $fecha_fin_sql);
                            
                            if ($stmt->execute()) {
                                $result = $stmt->get_result();
                                
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        // Formatear fecha
                                        $fecha_fmt = date('d/m/Y H:i', strtotime($row['fecha_venta']));
                                        
                                        // Determinar nombre del cliente
                                        $cliente = $row['nombre'] ? htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) : 'Cliente Anónimo (Web)';
                                        
                                        echo "<tr>";
                                        echo "<td>#" . $row['id_venta'] . "</td>";
                                        echo "<td>" . $fecha_fmt . "</td>";
                                        echo "<td>" . $cliente . "</td>";
                                        echo "<td>" . htmlspecialchars($row['metodo_pago']) . "</td>";
                                        echo "<td class='fw-bold text-success'>$" . number_format($row['total_venta'], 2) . "</td>";
                                        echo "<td>
                                                <button class='btn btn-sm teto-btn-action' 
                                                    onclick='alert(\"Funcionalidad de detalle pendiente de implementar (ID: " . $row['id_venta'] . ")\")'>
                                                    Ver Productos
                                                </button>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center py-4'>No se encontraron ventas en el rango de fechas seleccionado.</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-danger'>Error en la consulta: " . $conn->error . "</td></tr>";
                            }
                            $stmt->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <footer class="py-3 mt-4 border-top text-center teto-footer">
            <p class="mb-0 text-muted">&copy; 2025 Sistema de Inventario Teto | UTAU-Powered</p>
        </footer>

    </div> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
            crossorigin="anonymous">
    </script>
</body>
</html>
<?php
$conn->close();
?>