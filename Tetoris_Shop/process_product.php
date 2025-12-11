<?php
session_start();
/**
 * process_product.php
 * Script centralizado para manejar las operaciones CRUD (Crear, Modificar, Eliminar)
 * CORREGIDO para la estructura de la BD de Tetoris Shop.
 */

// 1. Incluir la conexión a la base de datos
require_once 'db_connection.php';

// OPCIONAL: Verificación de seguridad básica (se puede expandir con roles)
if (!isset($_SESSION['user_id'])) {
    // header("Location: forms/Login.php");
    // exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Capturar la acción para dirigir la lógica
    $action = $_POST['action'] ?? '';

    // ==========================================
    // LÓGICA DE ELIMINACIÓN (DELETE)
    // ==========================================
    if ($action === 'delete') {
        
        // CORRECCIÓN: Usamos 'id_producto'
        $producto_id = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : 0; 
        
        if ($producto_id > 0) {
            
            // CORRECCIÓN: La columna de la clave primaria es 'id_producto'
            $sql = "DELETE FROM productos WHERE id_producto = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $producto_id); 

            if ($stmt->execute()) {
                header("Location: productos.php?success=producto_eliminado");
                exit();
            } else {
                error_log("Error al eliminar el producto: " . $stmt->error);
                header("Location: productos.php?error=error_eliminacion");
                exit();
            }
            $stmt->close();
        } else {
             header("Location: productos.php?error=id_invalido");
             exit();
        }
        
    } 
    
    // ==========================================
    // LÓGICA DE CREACIÓN (CREATE) y MODIFICACIÓN (UPDATE)
    // ==========================================
    elseif ($action === 'create' || $action === 'update') {
        
        // 2. Capturar y sanear los datos (AJUSTADOS A LA NUEVA BD)
        $nombre_producto = $conn->real_escape_string($_POST['nombre_producto'] ?? '');
        $id_categoria    = intval($_POST['id_categoria'] ?? 0);      // NUEVO CAMPO
        $precio_venta    = floatval($_POST['precio_venta'] ?? 0.00); // Antes valor_unitario
        $costo_compra    = floatval($_POST['costo_compra'] ?? 0.00);  // Antes valor_compra
        $stock_actual    = intval($_POST['stock_actual'] ?? 0);      // Antes stock
        $stock_minimo    = intval($_POST['stock_minimo'] ?? 10);     // NUEVO CAMPO
        
        // Validación básica
        if (empty($nombre_producto) || $precio_venta <= 0 || $id_categoria <= 0) {
             header("Location: productos.php?error=campos_requeridos_faltantes");
             exit();
        }

        if ($action === 'create') {
            // INSERT (CREATE)
            $sql = "INSERT INTO productos 
                    (nombre_producto, id_categoria, precio_venta, costo_compra, stock_actual, stock_minimo) 
                    VALUES (?, ?, ?, ?, ?, ?)";
                    
            $stmt = $conn->prepare($sql);
            // Tipos: string, integer, double, double, integer, integer (sidddi)
            $stmt->bind_param("sidddi", $nombre_producto, $id_categoria, $precio_venta, $costo_compra, $stock_actual, $stock_minimo);

            if ($stmt->execute()) {
                header("Location: productos.php?success=producto_creado");
                exit();
            } else {
                error_log("Error al crear el producto: " . $stmt->error);
                header("Location: productos.php?error=error_creacion");
                exit();
            }
            $stmt->close();
            
        } elseif ($action === 'update') {
            // UPDATE
            $producto_id = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : 0; 
            
            if ($producto_id > 0) {
                // CORRECCIÓN: Se incluyen todos los nuevos campos y la condición WHERE usa 'id_producto'
                $sql = "UPDATE productos SET 
                            nombre_producto = ?, 
                            id_categoria = ?, 
                            precio_venta = ?, 
                            costo_compra = ?, 
                            stock_actual = ?,
                            stock_minimo = ?
                        WHERE id_producto = ?";
                
                $stmt = $conn->prepare($sql);
                // Tipos: string, integer, double, double, integer, integer, integer (sidddii)
                $stmt->bind_param("sidddii", $nombre_producto, $id_categoria, $precio_venta, $costo_compra, $stock_actual, $stock_minimo, $producto_id);

                if ($stmt->execute()) {
                    header("Location: productos.php?success=producto_modificado");
                    exit();
                } else {
                    error_log("Error al modificar el producto: " . $stmt->error);
                    header("Location: productos.php?error=error_modificacion");
                    exit();
                }
                $stmt->close();
            } else {
                header("Location: productos.php?error=id_invalido");
                exit();
            }
        }
    }
    // ==========================================
    // MANEJO DE ACCIÓN NO RECONOCIDA
    // ==========================================
    else {
        header("Location: productos.php?error=accion_no_valida");
        exit();
    }
} else {
    // Si se accede sin POST
    header("Location: productos.php?error=acceso_no_autorizado");
    exit();
}

$conn->close();
?>