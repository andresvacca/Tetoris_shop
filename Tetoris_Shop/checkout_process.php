<?php
/**
 * checkout_process.php
 * Procesa la venta del carrito vía AJAX (JSON).
 */
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

// Recibir JSON crudo
$json = file_get_contents('php://input');
$input = json_decode($json, true);

if (empty($input['cart'])) {
    echo json_encode(['success' => false, 'message' => 'El carrito está vacío.']);
    exit;
}

$cart = $input['cart'];
$total_calculado = 0;

// INICIAR TRANSACCIÓN (Todo o nada)
$conn->begin_transaction();

try {
    // 1. Validar Stock y Calcular Total Real (Backend)
    // No confiamos en el precio que envía el JS, lo buscamos de nuevo en la BD
    foreach ($cart as $item) {
        $id = (int)$item['id'];
        $qty = (int)$item['quantity'];

        $stmt = $conn->prepare("SELECT precio_venta, stock_actual, nombre_producto FROM productos WHERE id_producto = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $prod = $res->fetch_assoc();
        $stmt->close();

        if (!$prod) {
            throw new Exception("Producto ID $id no existe.");
        }
        if ($prod['stock_actual'] < $qty) {
            throw new Exception("Stock insuficiente para: " . $prod['nombre_producto']);
        }

        $total_calculado += ($prod['precio_venta'] * $qty);
    }

    // 2. Insertar Venta
    $id_usuario = !empty($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
    // Si no hay usuario logueado, se guarda como NULL (Venta anónima)
    
    $stmt_venta = $conn->prepare("INSERT INTO ventas (id_usuario, total_venta, metodo_pago) VALUES (?, ?, 'Web')");
    $stmt_venta->bind_param("id", $id_usuario, $total_calculado);
    $stmt_venta->execute();
    $id_venta = $conn->insert_id;
    $stmt_venta->close();

    // 3. Insertar Detalles y Restar Stock
    $stmt_det = $conn->prepare("INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt_upd = $conn->prepare("UPDATE productos SET stock_actual = stock_actual - ? WHERE id_producto = ?");

    foreach ($cart as $item) {
        $id = (int)$item['id'];
        $qty = (int)$item['quantity'];
        
        // Obtener precio de nuevo para el detalle
        // (Podríamos optimizar array anterior, pero por seguridad consultamos rápido)
        $q = $conn->query("SELECT precio_venta FROM productos WHERE id_producto = $id");
        $p = $q->fetch_assoc();
        $precio_unit = $p['precio_venta'];
        $subtotal = $precio_unit * $qty;

        // Insertar detalle
        $stmt_det->bind_param("iiidd", $id_venta, $id, $qty, $precio_unit, $subtotal);
        $stmt_det->execute();

        // Actualizar stock
        $stmt_upd->bind_param("ii", $qty, $id);
        $stmt_upd->execute();
    }
    $stmt_det->close();
    $stmt_upd->close();

    // CONFIRMAR TRANSACCIÓN
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Compra realizada con éxito. ID Venta: #' . $id_venta]);

} catch (Exception $e) {
    // Si algo falla, revertir todo
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>