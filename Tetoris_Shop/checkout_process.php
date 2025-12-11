<?php
/**
 * checkout_process.php
 * Procesamiento seguro de pagos AJAX.
 */
session_start();
require_once __DIR__ . '/db_connection.php';

header('Content-Type: application/json');

// Obtener input crudo
$raw_input = file_get_contents('php://input');
// Decodificar con flag para array asociativo
$input = json_decode($raw_input, true);

// Validar estructura básica
if (!is_array($input) || empty($input['cart']) || !is_array($input['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
    exit(0);
}

$cart = $input['cart'];
$conn->begin_transaction();

try {
    $total = 0.0;
    
    // 1. Validar Stock (Prepared Statements)
    $stmt_chk = $conn->prepare("SELECT precio_venta, stock_actual, nombre_producto FROM productos WHERE id_producto = ?");
    
    foreach ($cart as $item) {
        $id  = (int)($item['id'] ?? 0);
        $qty = (int)($item['quantity'] ?? 0);

        if ($id <= 0 || $qty <= 0) continue;

        $stmt_chk->bind_param("i", $id);
        $stmt_chk->execute();
        $res = $stmt_chk->get_result();
        $prod = $res->fetch_assoc();
        
        if (!$prod) throw new Exception("Producto $id no existe.");
        if ($prod['stock_actual'] < $qty) throw new Exception("Stock insuficiente: " . $prod['nombre_producto']);
        
        $total += ($prod['precio_venta'] * $qty);
    }
    $stmt_chk->close();

    // 2. Crear Venta
    $uid = $_SESSION['user_id'] ?? null; // Nullable
    $stmt_v = $conn->prepare("INSERT INTO ventas (id_usuario, total_venta, metodo_pago) VALUES (?, ?, 'Web')");
    $stmt_v->bind_param("id", $uid, $total);
    $stmt_v->execute();
    $vid = $conn->insert_id;
    $stmt_v->close();

    // 3. Detalles y Resta
    $stmt_d = $conn->prepare("INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt_u = $conn->prepare("UPDATE productos SET stock_actual = stock_actual - ? WHERE id_producto = ?");

    foreach ($cart as $item) {
        $id  = (int)$item['id'];
        $qty = (int)$item['quantity'];
        
        // Consultar precio nuevamente para seguridad
        $q = $conn->query("SELECT precio_venta FROM productos WHERE id_producto=$id");
        $p = $q->fetch_assoc();
        $pr = (float)$p['precio_venta'];
        $sub = $pr * $qty;

        $stmt_d->bind_param("iiidd", $vid, $id, $qty, $pr, $sub);
        $stmt_d->execute();

        $stmt_u->bind_param("ii", $qty, $id);
        $stmt_u->execute();
    }
    
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Éxito. ID: #' . $vid]);

} catch (Exception $e) {
    $conn->rollback();
    // Mensaje seguro (limpiando posibles caracteres raros)
    $msg = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo json_encode(['success' => false, 'message' => $msg]);
}

$conn->close();
?>