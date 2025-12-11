<?php
/**
 * Ventas.php
 * Reporte seguro: Inputs GET filtrados y Salida escapada.
 */
session_start();
require_once __DIR__ . '/db_connection.php'; 

// 1. Seguridad de Sesión
$role = $_SESSION['user_role'] ?? '';
$allowed = ['ADMINISTRADOR', 'EMPLEADO'];
if (!in_array($role, $allowed, true)) {
    header("Location: forms/Login.php");
    exit(0);
}

// 2. Inputs Seguros (GET)
$f_inicio = filter_input(INPUT_GET, 'inicio', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: date('Y-m-01');
$f_fin    = filter_input(INPUT_GET, 'fin', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: date('Y-m-d');
$f_fin_sql = $f_fin . ' 23:59:59';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/inventario.css"> 
</head>
<body>
    <div class="container-fluid">
        <header class="py-3 mb-4 border-bottom teto-header">
            <h1 class="h3 text-white ms-3">💸 Registro de Ventas</h1>
            <a href="logout.php" class="btn btn-light btn-sm float-end me-3">Salir</a>
        </header>

        <main class="teto-main-content">
            <section class="mb-4 p-3 border rounded teto-card">
                <form method="GET" action="Ventas.php" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Desde:</label>
                        <input type="date" name="inicio" class="form-control" value="<?php echo htmlspecialchars($f_inicio, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Hasta:</label>
                        <input type="date" name="fin" class="form-control" value="<?php echo htmlspecialchars($f_fin, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                    </div>
                </form>
            </section>

            <section class="teto-card p-3 border rounded">
                <div class="table-responsive">
                    <table class="table table-striped teto-table">
                        <thead>
                            <tr>
                                <th>ID</th><th>Fecha</th><th>Cliente</th><th>Total</th><th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT v.id_venta, v.fecha_venta, v.total_venta, u.nombre, u.apellido
                                    FROM ventas v
                                    LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
                                    WHERE v.fecha_venta BETWEEN ? AND ?
                                    ORDER BY v.fecha_venta DESC";
                            
                            $stmt = $conn->prepare($sql);
                            if ($stmt) {
                                $stmt->bind_param("ss", $f_inicio, $f_fin_sql);
                                $stmt->execute();
                                $res = $stmt->get_result();
                                
                                if ($res->num_rows > 0) {
                                    while ($row = $res->fetch_assoc()) {
                                        // SANITIZACIÓN TOTAL
                                        $v_id    = htmlspecialchars((string)$row['id_venta'], ENT_QUOTES, 'UTF-8');
                                        $v_fecha = htmlspecialchars(date('d/m/Y H:i', strtotime($row['fecha_venta'])), ENT_QUOTES, 'UTF-8');
                                        $v_total = number_format((float)$row['total_venta'], 2);
                                        
                                        $nom_cli = $row['nombre'] 
                                            ? htmlspecialchars($row['nombre'] . ' ' . $row['apellido'], ENT_QUOTES, 'UTF-8') 
                                            : 'Anónimo';

                                        echo "<tr>";
                                        echo "<td>#$v_id</td>";
                                        echo "<td>$v_fecha</td>";
                                        echo "<td>$nom_cli</td>";
                                        echo "<td class='text-success fw-bold'>$$v_total</td>";
                                        echo "<td><button class='btn btn-sm teto-btn-action'>Ver</button></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>Sin resultados.</td></tr>";
                                }
                                $stmt->close();
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>