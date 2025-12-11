<?php
session_start();
require_once 'db_connection.php';

// Validación de sesión segura
if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['ADMINISTRADOR', 'EMPLEADO'])) {
    header("Location: forms/Login.php?error=acceso_denegado");
    exit();
}

// Consulta usando solo las columnas necesarias
$sql = "SELECT id_producto, nombre_producto, stock_actual, precio_venta FROM productos ORDER BY nombre_producto ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Teto - Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/inventario.css">
</head>
<body>
    <div class="container-fluid">
        <main class="teto-main-content">
            <section class="teto-card p-3 border rounded">
                <h2 class="mb-3">Lista de Productos</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-hover teto-table">
                        <thead class="teto-thead">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Stock</th>
                                <th scope="col">Precio ($)</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    // Sanitización de TODAS las variables antes de usarlas
                                    $id_safe     = htmlspecialchars((string)$row['id_producto'], ENT_QUOTES, 'UTF-8');
                                    $nombre_safe = htmlspecialchars((string)$row['nombre_producto'], ENT_QUOTES, 'UTF-8');
                                    $stock_safe  = htmlspecialchars((string)$row['stock_actual'], ENT_QUOTES, 'UTF-8');
                                    $precio_safe = htmlspecialchars(number_format((float)$row['precio_venta'], 2), ENT_QUOTES, 'UTF-8');

                                    // Lógica de colores (segura porque $row['stock_actual'] es int, pero validamos igual)
                                    $stock_val = (int)$row['stock_actual'];
                                    $row_class = 'teto-row-ok';
                                    if ($stock_val === 0) {
                                        $row_class = 'teto-row-zero';
                                    } elseif ($stock_val <= 10) {
                                        $row_class = 'teto-row-low';
                                    }
                                    
                                    // Salida HTML Segura
                                    echo "<tr class='" . htmlspecialchars($row_class, ENT_QUOTES, 'UTF-8') . "'>";
                                    echo "<td>" . $id_safe . "</td>";
                                    echo "<td>" . $nombre_safe . "</td>";
                                    echo "<td>" . $stock_safe . "</td>";
                                    echo "<td>" . $precio_safe . "</td>";
                                    echo "<td>";
                                    echo "<a href='Producto_Formulario.php?id=" . $id_safe . "' class='btn btn-sm teto-btn-action me-2'>Editar</a>";
                                    
                                    // Formulario de borrado seguro
                                    echo "<form method='POST' action='process_product.php' style='display:inline;' onsubmit='return confirm(\"¿Eliminar: " . $nombre_safe . "?\")'>";
                                    echo "<input type='hidden' name='action' value='delete'>";
                                    echo "<input type='hidden' name='id_producto' value='" . $id_safe . "'>";
                                    echo "<button type='submit' class='btn btn-sm btn-danger'>Eliminar</button>";
                                    echo "</form>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>Sin productos.</td></tr>";
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