<?php
/**
 * process_product.php
 * Script centralizado para manejar las operaciones CRUD (Crear, Modificar, Eliminar)
 */

// 1. Incluir la conexión a la base de datos
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Capturar la acción para dirigir la lógica
    $action = $_POST['action'] ?? '';

    // ==========================================
    // LÓGICA DE ELIMINACIÓN (DELETE)
    // Se procesa primero ya que solo necesita el ID
    // ==========================================
    if ($action === 'delete') {
        
        // El ID es OBLIGATORIO para eliminar
        $producto_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0; 
        
        if ($producto_id > 0) {
            
            $sql = "DELETE FROM productos WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $producto_id); // 'i' para integer (el id)

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
    // Se requieren todos los campos para estas acciones
    // ==========================================
    elseif ($action === 'create' || $action === 'update') {
        
        // Capturar y sanear los datos comunes de forma segura
        $nombre_producto = $conn->real_escape_string($_POST['nombre_producto']);
        $valor_unitario = floatval($_POST['valor_unitario']);
        $valor_compra = floatval($_POST['valor_compra']);
        $stock = intval($_POST['stock']);
        
        if ($action === 'create') {
            // INSERT
            $sql = "INSERT INTO productos (nombre_producto, valor_unitario, valor_compra, stock) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sddi", $nombre_producto, $valor_unitario, $valor_compra, $stock);

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
            $producto_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0; 
            
            if ($producto_id > 0) {
                $sql = "UPDATE productos SET nombre_producto = ?, valor_unitario = ?, valor_compra = ?, stock = ? WHERE id = ?";
                
                $stmt = $conn->prepare($sql);
                // 'sddii' -> string, double, double, integer, integer(id)
                $stmt->bind_param("sddii", $nombre_producto, $valor_unitario, $valor_compra, $stock, $producto_id);

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