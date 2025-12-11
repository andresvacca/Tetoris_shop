<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

// Leer el JSON enviado por JS
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['cart']) || empty($input['cart'])) {
    echo json_encode(['success' => false, 'message' => 'El carrito está vacío']);
    exit;
}

$cart = $input['cart'];
$total = 0;

// Calcular total real desde el backend (por seguridad)
// y verificar stock
foreach ($cart as $item) {
    $stmt = $conn->prepare("SELECT precio_venta, stock_actual FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $item['id']);
    $stmt->execute();
    $res = $stmt->get_result();
    $prod = $res->fetch_assoc();
    
    if (!$prod || $prod['stock_actual'] < $item['quantity']) {
        echo json_encode(['success' => false, 'message' => 'Stock insuficiente para: ' . $item['name']]);
        exit;
    }
    $total += $prod['precio_venta'] * $item['quantity'];
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // 1. Crear Venta
    $id_usuario = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
    $stmt = $conn->prepare("INSERT INTO ventas (id_usuario, total_venta, metodo_pago) VALUES (?, ?, 'Web')");
    $stmt->bind_param("id", $id_usuario, $total);
    $stmt->execute();
    $id_venta = $conn->insert_id;

    // 2. Insertar Detalles y Restar Stock
    $stmt_detalle = $conn->prepare("INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt_stock = $conn->prepare("UPDATE productos SET stock_actual = stock_actual - ? WHERE id_producto = ?");

    foreach ($cart as $item) {
        // Obtener precio actual de nuevo
        $q = $conn->query("SELECT precio_venta FROM productos WHERE id_producto = " . intval($item['id']));
        $p = $q->fetch_assoc();
        $precio = $p['precio_venta'];
        $subtotal = $precio * $item['quantity'];

        // Guardar detalle
        $stmt_detalle->bind_param("iiidd", $id_venta, $item['id'], $item['quantity'], $precio, $subtotal);
        $stmt_detalle->execute();

        // Restar stock
        $stmt_stock->bind_param("ii", $item['quantity'], $item['id']);
        $stmt_stock->execute();
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => '¡Compra realizada con éxito!']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>