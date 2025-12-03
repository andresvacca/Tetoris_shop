<?php
// 1. Incluir la conexión a la BD
require_once 'db_connection.php'; 

// Lógica para determinar el modo (Nuevo o Editar)
// Verifica si existe el parámetro 'id' y si es numérico
$is_editing = isset($_GET['id']) && is_numeric($_GET['id']);
$form_title = $is_editing ? 'Modificar Producto Existente' : 'Ingresar Nuevo Producto';
$submit_label = $is_editing ? 'Guardar Cambios' : 'Crear Producto';

// Valores iniciales por defecto (vacíos)
$producto_id = $is_editing ? $_GET['id'] : '';
$nombre_value = '';
$stock_value = '';
$valor_unitario_value = '';
$valor_compra_value = '';

// Si estamos en modo EDICIÓN, cargamos los datos del producto
if ($is_editing) {
    // Consulta segura para obtener el producto usando prepared statements
    $sql_fetch = "SELECT nombre_producto, stock, valor_unitario, valor_compra FROM productos WHERE id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    $stmt_fetch->bind_param("i", $producto_id); // 'i' para integer (el id)
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result();

    if ($result_fetch->num_rows === 1) {
        $producto = $result_fetch->fetch_assoc();
        
        // Asignar los datos del producto a las variables del formulario
        $nombre_value = $producto['nombre_producto'];
        $stock_value = $producto['stock'];
        $valor_unitario_value = $producto['valor_unitario'];
        $valor_compra_value = $producto['valor_compra'];
    } else {
        // Redirigir si el ID no existe
        header("Location: productos.php?error=producto_no_encontrado");
        exit();
    }
    $stmt_fetch->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $form_title; ?> - Teto</title> 
    
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
                <h1 class="h3 mb-0 text-white teto-title">🛠️ Gestión de Producto</h1>
                
                <nav class="nav">
                    <a href="productos.php" class="nav-link teto-nav-link">Inventario</a>
                    <a href="dashboard.php" class="nav-link teto-nav-link">Dashboard</a>
                    <a href="ventas.php" class="nav-link teto-nav-link">Ventas</a>
                </nav>
                
                <button type="button" class="btn teto-btn-primary">Cerrar Sesión</button>
            </div>
        </header>

        <main class="teto-main-content">

            <section class="teto-card p-4 border rounded shadow mx-auto" style="max-width: 700px;">
                
                <h2 class="mb-4"><?php echo $form_title; ?></h2>

                <form action="process_product.php" method="POST">
                    
                    <?php if ($is_editing): ?>
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($producto_id); ?>">
                    <?php else: ?>
                        <input type="hidden" name="action" value="create">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Producto:</label>
                        <input type="text" class="form-control teto-input" id="nombre" name="nombre_producto" 
                                value="<?php echo htmlspecialchars($nombre_value); ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="precio_venta" class="form-label">Precio de Venta ($ valor_unitario):</label>
                            <input type="number" step="0.01" class="form-control teto-input" id="precio_venta" name="valor_unitario" min="0" 
                                    value="<?php echo htmlspecialchars($valor_unitario_value); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="precio_compra" class="form-label">Precio de Compra ($ valor_compra):</label>
                            <input type="number" step="0.01" class="form-control teto-input" id="precio_compra" name="valor_compra" min="0" 
                                    value="<?php echo htmlspecialchars($valor_compra_value); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock Actual:</label>
                        <input type="number" class="form-control teto-input" id="stock" name="stock" min="0" 
                                value="<?php echo htmlspecialchars($stock_value); ?>" required>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn teto-btn-secondary fs-5" style="width: 60%; font-weight: bold;">
                            <?php echo $submit_label; ?>
                        </button>
                        
                        <a href="productos.php" class="btn btn-secondary fs-5" style="width: 35%;">
                            Cancelar
                        </a>
                    </div>
                </form>
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
// Cerrar la conexión al final del script
$conn->close();
?>
