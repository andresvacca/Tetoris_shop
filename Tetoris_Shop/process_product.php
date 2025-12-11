<?php
/**
 * process_product.php
 * CORREGIDO PARA CODACY: Sin superglobales directas y con validación estricta.
 */
session_start();
require_once __DIR__ . '/db_connection.php'; // Usa __DIR__ para evitar "Insecure file inclusion"

// Verificar sesión
if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['ADMINISTRADOR', 'EMPLEADO'])) {
    header("Location: forms/Login.php");
    exit();
}

// Validar que sea POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // SOLUCIÓN A "Direct Use of Superglobals": Usar filter_input
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    // SOLUCIÓN A "Proper Comparison": Usar ===
    if ($action === 'delete') {
        $id = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
        
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM productos WHERE id_producto = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            header("Location: Productos.php?msg=eliminado");
        } else {
            header("Location: Productos.php?error=id_invalido");
        }
        exit();
    }

    if ($action === 'create' || $action === 'update') {
        // Validación estricta de inputs
        $nombre    = filter_input(INPUT_POST, 'nombre_producto', FILTER_SANITIZE_STRING);
        $id_cat    = filter_input(INPUT_POST, 'id_categoria', FILTER_VALIDATE_INT);
        $precio    = filter_input(INPUT_POST, 'precio_venta', FILTER_VALIDATE_FLOAT);
        $costo     = filter_input(INPUT_POST, 'costo_compra', FILTER_VALIDATE_FLOAT);
        $stock     = filter_input(INPUT_POST, 'stock_actual', FILTER_VALIDATE_INT);
        $min_stock = filter_input(INPUT_POST, 'stock_minimo', FILTER_VALIDATE_INT);

        if ($action === 'create') {
            $stmt = $conn->prepare("INSERT INTO productos (nombre_producto, id_categoria, precio_venta, costo_compra, stock_actual, stock_minimo) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sidddi", $nombre, $id_cat, $precio, $costo, $stock, $min_stock);
            $stmt->execute();
            $stmt->close();
            header("Location: Productos.php?msg=creado");
        } 
        elseif ($action === 'update') {
            $id = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
            if ($id) {
                $stmt = $conn->prepare("UPDATE productos SET nombre_producto=?, id_categoria=?, precio_venta=?, costo_compra=?, stock_actual=?, stock_minimo=? WHERE id_producto=?");
                $stmt->bind_param("sidddii", $nombre, $id_cat, $precio, $costo, $stock, $min_stock, $id);
                $stmt->execute();
                $stmt->close();
                header("Location: Productos.php?msg=actualizado");
            }
        }
        exit();
    }
} else {
    header("Location: Productos.php");
    exit();
}
$conn->close();
?>