<?php
/**
 * Producto_Formulario.php
 * Formulario estricto.
 */
session_start();
require_once __DIR__ . '/db_connection.php'; 

$role = $_SESSION['user_role'] ?? '';
if (!in_array($role, ['ADMINISTRADOR', 'EMPLEADO'], true)) {
    header("Location: forms/Login.php");
    exit(0);
}

// Inicializar variables seguras
$is_edit = false;
$title   = 'Nuevo Producto';
$btn_txt = 'Crear';

$id_prod = ''; 
$nom     = ''; 
$id_cat  = 0; 
$pv      = ''; 
$pc      = ''; 
$stk     = ''; 
$min     = 5;

// Validar GET ID con filter_input
$get_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($get_id) {
    $is_edit = true;
    $title   = 'Editar Producto';
    $btn_txt = 'Guardar';
    $id_prod = $get_id;

    $stmt = $conn->prepare("SELECT * FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $id_prod);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($p = $res->fetch_assoc()) {
        // Sanitizar datos de BD para HTML
        $nom    = htmlspecialchars($p['nombre_producto'], ENT_QUOTES, 'UTF-8');
        $id_cat = (int)$p['id_categoria'];
        $pv     = (float)$p['precio_venta'];
        $pc     = (float)$p['costo_compra'];
        $stk    = (int)$p['stock_actual'];
        $min    = (int)$p['stock_minimo'];
    }
    $stmt->close();
}

// Cargar categorías
$cats = [];
$c_res = $conn->query("SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC");
if ($c_res) {
    while($r = $c_res->fetch_assoc()){
        $cats[] = [
            'id' => (int)$r['id_categoria'],
            'nm' => htmlspecialchars($r['nombre_categoria'], ENT_QUOTES, 'UTF-8')
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/inventario.css">
</head>
<body>
    <div class="container mt-5">
        <main class="teto-card p-4 border rounded shadow mx-auto" style="max-width: 800px;">
            <h2 class="mb-4 text-center"><?php echo $title; ?></h2>

            <form action="process_product.php" method="POST">
                <input type="hidden" name="action" value="<?php echo $is_edit ? 'update' : 'create'; ?>">
                <?php if ($is_edit): ?>
                    <input type="hidden" name="id_producto" value="<?php echo $id_prod; ?>">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-7 mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre_producto" value="<?php echo $nom; ?>" required>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" name="id_categoria" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($cats as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] === $id_cat) ? 'selected' : ''; ?>>
                                    <?php echo $c['nm']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Precio Venta</label>
                        <input type="number" step="0.01" class="form-control" name="precio_venta" value="<?php echo $pv; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Costo Compra</label>
                        <input type="number" step="0.01" class="form-control" name="costo_compra" value="<?php echo $pc; ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" class="form-control" name="stock_actual" value="<?php echo $stk; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mínimo</label>
                        <input type="number" class="form-control" name="stock_minimo" value="<?php echo $min; ?>" required>
                    </div>
                </div>
                
                <div class="d-grid gap-2 mt-4 d-md-flex justify-content-md-end">
                    <a href="Productos.php" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn teto-btn-secondary px-5"><?php echo $btn_txt; ?></button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
<?php $conn->close(); ?>