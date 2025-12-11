<tbody>
    <?php
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // SOLUCIÓN A "XSS" y "Escaping Output": Limpiar todo antes de imprimir
            $id_safe     = htmlspecialchars((string)$row['id_producto'], ENT_QUOTES, 'UTF-8');
            $nombre_safe = htmlspecialchars($row['nombre_producto'], ENT_QUOTES, 'UTF-8');
            // Casting a int es seguro por definición
            $stock_safe  = (int)$row['stock_actual']; 
            $precio_safe = number_format((float)$row['precio_venta'], 2);

            $row_class = 'teto-row-ok';
            if ($stock_safe === 0) { // Uso de === (Proper Comparison)
                $row_class = 'teto-row-zero';
            } elseif ($stock_safe <= 10) {
                $row_class = 'teto-row-low';
            }

            // Al imprimir la clase, también la escapamos por si acaso
            echo "<tr class='" . htmlspecialchars($row_class, ENT_QUOTES, 'UTF-8') . "'>";
            echo "<td>" . $id_safe . "</td>";
            echo "<td>" . $nombre_safe . "</td>";
            echo "<td>" . $stock_safe . "</td>";
            // number_format devuelve string seguro, pero htmlspecialchars no hace daño
            echo "<td>" . htmlspecialchars($precio_safe, ENT_QUOTES, 'UTF-8') . "</td>";
            
            echo "<td>";
            echo "<a href='Producto_Formulario.php?id=" . $id_safe . "' class='btn btn-sm teto-btn-action me-2'>Editar</a>";
            
            // Formulario de eliminación seguro
            echo "<form method='POST' action='process_product.php' style='display:inline;' onsubmit='return confirm(\"¿Eliminar?\")'>";
            echo "<input type='hidden' name='action' value='delete'>";
            echo "<input type='hidden' name='id_producto' value='" . $id_safe . "'>";
            echo "<button type='submit' class='btn btn-sm btn-danger'>Eliminar</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
    }
    ?>
</tbody>