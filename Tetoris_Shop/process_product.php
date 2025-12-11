<?php
/**
 * process_product.php
 * Backend seguro para CRUD de productos.
 */
session_start();
require_once 'db_connection.php';

// Seguridad: Solo admin/empleado
if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['ADMINISTRADOR', 'EMPLEADO'])) {
    header("Location: forms/Login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Capturar acción y sanitizar
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    // ================= DELETE =================
    if ($action === 'delete') {
        $id = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
        
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM productos WHERE id_producto = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                header("Location: Productos.php?msg=eliminado");
            } else {
                header("Location: Productos.php?error=db_error");
            }
            $stmt->close();
        } else {
            header("Location: Productos.php?error=id_invalido");
        }
        exit();
    }

    // ================= CREATE / UPDATE =================
    if ($action === 'create' || $action === 'update') {
        
        // Validar inputs estrictamente
        $nombre     = filter_input(INPUT_POST, 'nombre_producto', FILTER_SANITIZE_STRING);
        $id_cat     = filter_input(INPUT_POST, 'id_categoria', FILTER_VALIDATE_INT);
        $precio     = filter_input(INPUT_POST, 'precio_venta', FILTER_VALIDATE_FLOAT);
        $costo      = filter_input(INPUT_POST, 'costo_compra', FILTER_VALIDATE_FLOAT);
        $stock      = filter_input(INPUT_POST, 'stock_actual', FILTER_VALIDATE_INT);
        $min_stock  = filter_input(INPUT_POST, 'stock_minimo', FILTER_VALIDATE_INT);

        // Verificación básica
        if (!$nombre || !$id_cat || $precio === false || $stock === false) {
            header("Location: Producto_Formulario.php?error=campos_vacios");
            exit();
        }

        if ($action === 'create') {
            $sql = "INSERT INTO productos (nombre_producto, id_categoria, precio_venta, costo_compra, stock_actual, stock_minimo) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            // Tipos: s (string), i (int), d (double/float)
            $stmt->bind_param("sidddi", $nombre, $id_cat, $precio, $costo, $stock, $min_stock);
            
            if ($stmt->execute()) {
                header("Location: Productos.php?msg=creado");
            } else {
                header("Location: Producto_Formulario.php?error=error_creacion");
            }
            $stmt->close();

        } elseif ($action === 'update') {
            $id = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
            if (!$id) {
                header("Location: Productos.php?error=id_invalido");
                exit();
            }

            $sql = "UPDATE productos SET nombre_producto=?, id_categoria=?, precio_venta=?, costo_compra=?, stock_actual=?, stock_minimo=? WHERE id_producto=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sidddii", $nombre, $id_cat, $precio, $costo, $stock, $min_stock, $id);

            if ($stmt->execute()) {
                header("Location: Productos.php?msg=actualizado");
            } else {
                header("Location: Producto_Formulario.php?error=error_actualizacion&id=".$id);
            }
            $stmt->close();
        }
    }
} else {
    // Si intentan entrar directo sin POST
    header("Location: Productos.php");
    exit();
}
$conn->close();
?>