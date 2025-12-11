<?php
require_once 'db_connection.php'; 
session_start();

// Verificación de seguridad (Solo admin/empleado)
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['ADMINISTRADOR', 'EMPLEADO'])) {
    header("Location: Login.php");
    exit();
}

$is_editing = isset($_GET['id']) && is_numeric($_GET['id']);
$form_title = $is_editing ? 'Modificar Producto' : 'Ingresar Nuevo Producto';
$submit_label = $is_editing ? 'Guardar Cambios' : 'Crear Producto';

// Inicializar variables
$producto_id = $is_editing ? $_GET['id'] : '';
$nombre = ''; $id_cat = ''; $stock = ''; $min_stock = 5; $p_venta = ''; $p_compra = '';

if ($is_editing) {
    // Usamos id_producto según la nueva BD
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($prod = $res->fetch_assoc()) {
        $nombre = $prod['nombre_producto'];
        $id_cat = $prod['id_categoria'];
        $stock = $prod['stock_actual'];
        $min_stock = $prod['stock_minimo'];
        $p_venta = $prod['precio_venta'];
        $p_compra = $prod['costo_compra'];
    }
}

// Obtener categorías para el select
$cats = $conn->query("SELECT * FROM categorias");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $form_title; ?> - Teto</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/inventario.css">
</head>
<body>
<div class="container mt-5">
    <div class="teto-card p-4 border rounded shadow mx-auto" style="max-width: 800px;">
        <h2 class="mb-4 text-center"><?php echo $form_title; ?></h2>
        
        <form action="process_product.php" method="POST">
            <input type="hidden" name="action" value="<?php echo $is_editing ? 'update' : 'create'; ?>">
            <?php if ($is_editing): ?>
                <input type="hidden" name="id_producto" value="<?php echo $producto_id; ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Nombre del Producto</label>
                    <input type="text" class="form-control" name="nombre_producto" value="<?php echo htmlspecialchars($nombre); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Categoría</label>
                    <select class="form-select" name="id_categoria" required>
                        <option value="">Seleccionar...</option>
                        <?php while($c = $cats->fetch_assoc()): ?>
                            <option value="<?php echo $c['id_categoria']; ?>" <?php echo ($c['id_categoria'] == $id_cat) ? 'selected' : ''; ?>>
                                <?php echo $c['nombre_categoria']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Precio Venta ($)</label>
                    <input type="number" step="0.01" class="form-control" name="precio_venta" value="<?php echo htmlspecialchars($p_venta); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Costo Compra ($)</label>
                    <input type="number" step="0.01" class="form-control" name="costo_compra" value="<?php echo htmlspecialchars($p_compra); ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Stock Actual</label>
                    <input type="number" class="form-control" name="stock_actual" value="<?php echo htmlspecialchars($stock); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Stock Mínimo (Alerta)</label>
                    <input type="number" class="form-control" name="stock_minimo" value="<?php echo htmlspecialchars($min_stock); ?>" required>
                </div>
            </div>
            
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn teto-btn-secondary btn-lg"><?php echo $submit_label; ?></button>
                <a href="Productos.php" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>