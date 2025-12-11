<?php
/**
 * Producto_Formulario.php
 * Formulario seguro para Crear/Editar productos con categorías y alertas de stock.
 */
session_start();
require_once 'db_connection.php'; 

// 1. SEGURIDAD DE ACCESO
if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['ADMINISTRADOR', 'EMPLEADO'])) {
    header("Location: forms/Login.php");
    exit();
}

// 2. INICIALIZACIÓN DE VARIABLES (Valores por defecto vacíos/seguros)
$is_editing = false;
$form_title = 'Ingresar Nuevo Producto';
$submit_label = 'Crear Producto';

// Variables para el HTML (inicializadas vacías)
$id_producto = '';
$nombre      = '';
$id_cat      = '';
$p_venta     = '';
$p_compra    = '';
$stock       = '';
$min_stock   = '5'; // Valor por defecto sugerido

// 3. LÓGICA DE EDICIÓN
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $is_editing = true;
    $form_title = 'Modificar Producto';
    $submit_label = 'Guardar Cambios';
    $id_producto = (int)$_GET['id']; // Casting seguro a entero

    // Consulta Segura
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id_producto = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($prod = $result->fetch_assoc()) {
            // ASIGNAR Y SANITIZAR DATOS PARA EL ATRIBUTO VALUE DEL HTML
            // Usamos ENT_QUOTES para escapar comillas simples y dobles
            $nombre    = htmlspecialchars($prod['nombre_producto'], ENT_QUOTES, 'UTF-8');
            $id_cat    = (int)$prod['id_categoria'];
            $p_venta   = htmlspecialchars($prod['precio_venta'], ENT_QUOTES, 'UTF-8');
            $p_compra  = htmlspecialchars($prod['costo_compra'], ENT_QUOTES, 'UTF-8');
            $stock     = (int)$prod['stock_actual'];
            $min_stock = (int)$prod['stock_minimo'];
        } else {
            // Si el ID no existe, redirigir
            header("Location: Productos.php?error=no_encontrado");
            exit();
        }
        $stmt->close();
    }
}

// 4. OBTENER CATEGORÍAS (Para el selector)
$categorias = [];
$cat_query = $conn->query("SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC");
if ($cat_query) {
    while ($cat = $cat_query->fetch_assoc()) {
        $categorias[] = $cat;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($form_title, ENT_QUOTES, 'UTF-8'); ?> - Teto Shop</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/inventario.css">
</head>
<body>

    <div class="container mt-5 mb-5">
        
        <header class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h3 teto-title">🛠️ Gestión de Producto</h1>
            <a href="Productos.php" class="btn btn-outline-dark">Volver al Inventario</a>
        </header>

        <main class="teto-card p-4 border rounded shadow mx-auto" style="max-width: 800px;">
            
            <h2 class="mb-4 text-center"><?php echo htmlspecialchars($form_title, ENT_QUOTES, 'UTF-8'); ?></h2>

            <form action="process_product.php" method="POST">
                
                <input type="hidden" name="action" value="<?php echo $is_editing ? 'update' : 'create'; ?>">
                <?php if ($is_editing): ?>
                    <input type="hidden" name="id_producto" value="<?php echo $id_producto; ?>">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-7 mb-3">
                        <label for="nombre" class="form-label">Nombre del Producto</label>
                        <input type="text" class="form-control" id="nombre" name="nombre_producto" 
                               value="<?php echo $nombre; ?>" required>
                    </div>
                    
                    <div class="col-md-5 mb-3">
                        <label for="categoria" class="form-label">Categoría</label>
                        <select class="form-select" id="categoria" name="id_categoria" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($categorias as $c): ?>
                                <?php 
                                    // Marcar como 'selected' si coincide con el producto editado
                                    $selected = ($c['id_categoria'] == $id_cat) ? 'selected' : ''; 
                                    $cat_name = htmlspecialchars($c['nombre_categoria'], ENT_QUOTES, 'UTF-8');
                                ?>
                                <option value="<?php echo $c['id_categoria']; ?>" <?php echo $selected; ?>>
                                    <?php echo $cat_name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="precio_venta" class="form-label">Precio de Venta ($)</label>
                        <input type="number" step="0.01" class="form-control" id="precio_venta" name="precio_venta" min="0" 
                               value="<?php echo $p_venta; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="costo_compra" class="form-label">Costo de Compra ($)</label>
                        <input type="number" step="0.01" class="form-control" id="costo_compra" name="costo_compra" min="0" 
                               value="<?php echo $p_compra; ?>" required>
                        <div class="form-text">Costo para calcular ganancias (Privado).</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="stock" class="form-label">Stock Actual</label>
                        <input type="number" class="form-control" id="stock" name="stock_actual" min="0" 
                               value="<?php echo $stock; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="stock_minimo" class="form-label">Stock Mínimo (Alerta)</label>
                        <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" min="1" 
                               value="<?php echo $min_stock; ?>" required>
                        <div class="form-text">Si baja de esto, saldrá en amarillo/rojo.</div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 mt-4 d-md-flex justify-content-md-end">
                    <a href="Productos.php" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn teto-btn-secondary btn-lg px-5">
                        <?php echo htmlspecialchars($submit_label, ENT_QUOTES, 'UTF-8'); ?>
                    </button>
                </div>

            </form>
        </main>

        <footer class="mt-5 text-center text-muted small">
            &copy; 2025 Sistema Teto Shop
        </footer>

    </div> 
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>