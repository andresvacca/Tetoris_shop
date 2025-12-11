<?php
session_start();
require_once 'db_connection.php'; 

// Seguridad de roles
if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['ADMINISTRADOR', 'EMPLEADO'])) {
    header("Location: forms/Login.php");
    exit();
}

// Validación de inputs GET (Codacy odia acceder directamente a $_GET sin verificar)
$fecha_inicio = filter_input(INPUT_GET, 'inicio', FILTER_SANITIZE_STRING) ?? date('Y-m-01');
$fecha_fin    = filter_input(INPUT_GET, 'fin', FILTER_SANITIZE_STRING) ?? date('Y-m-d');
$fecha_fin_sql = $fecha_fin . ' 23:59:59';
?>
<!DOCTYPE html>
<html lang="es">
<body>
    <div class="container-fluid">
        <main class="teto-main-content">
            <form method="GET" action="Ventas.php" class="row g-3 align-items-end mb-4">
                <div class="col-md-3">
                    <label class="form-label">Desde:</label>
                    <input type="date" name="inicio" class="form-control" value="<?php echo htmlspecialchars($fecha_inicio, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hasta:</label>
                    <input type="date" name="fin" class="form-control" value="<?php echo htmlspecialchars($fecha_fin, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn w-100 btn-primary">Filtrar</button>
                </div>
            </form>

            <section class="teto-card p-3 border rounded">
                <div class="table-responsive">
                    <table class="table table-striped teto-table">
                        <thead></thead>
                        <tbody>
                            <?php
                            $sql = "SELECT v.id_venta, v.fecha_venta, v.total_venta, v.metodo_pago, u.nombre, u.apellido
                                    FROM ventas v LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
                                    WHERE v.fecha_venta BETWEEN ? AND ? ORDER BY v.fecha_venta DESC";
                            
                            $stmt = $conn->prepare($sql);
                            if ($stmt) {
                                $stmt->bind_param("ss", $fecha_inicio, $fecha_fin_sql);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                
                                while ($row = $result->fetch_assoc()) {
                                    // Escapado riguroso
                                    $id_venta = htmlspecialchars((string)$row['id_venta'], ENT_QUOTES, 'UTF-8');
                                    $fecha    = htmlspecialchars(date('d/m/Y H:i', strtotime($row['fecha_venta'])), ENT_QUOTES, 'UTF-8');
                                    $total    = htmlspecialchars(number_format((float)$row['total_venta'], 2), ENT_QUOTES, 'UTF-8');
                                    $metodo   = htmlspecialchars((string)$row['metodo_pago'], ENT_QUOTES, 'UTF-8');
                                    
                                    $nombre_completo = $row['nombre'] 
                                        ? htmlspecialchars($row['nombre'] . ' ' . $row['apellido'], ENT_QUOTES, 'UTF-8') 
                                        : 'Cliente Web';

                                    echo "<tr>";
                                    echo "<td>#" . $id_venta . "</td>";
                                    echo "<td>" . $fecha . "</td>";
                                    echo "<td>" . $nombre_completo . "</td>";
                                    echo "<td>" . $metodo . "</td>";
                                    echo "<td class='text-success'>$" . $total . "</td>";
                                    echo "<td><button class='btn btn-sm teto-btn-action'>Ver</button></td>";
                                    echo "</tr>";
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
</body>
</html>
<?php $conn->close(); ?>