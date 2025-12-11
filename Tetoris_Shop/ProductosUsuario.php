<?php
/**
 * ProductosUsuario.php
 * Catálogo para clientes con carrito de compras funcional y conexión a BD.
 */
require_once 'db_connection.php'; 
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo Teto - Con Carrito de Compras</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
        crossorigin="anonymous">
        
    <link rel="stylesheet" href="css/inventario.css">
    
    <style>
        /* Estilo para el carrito (sidebar fijo) */
        .cart-sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: 350px; 
            height: 100%;
            background-color: #343a40; 
            color: white;
            padding: 1rem;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.5);
            z-index: 1030; 
            transition: transform 0.3s ease-in-out;
            transform: translateX(350px); 
        }
        .cart-sidebar.open {
            transform: translateX(0); 
        }
        .cart-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #444; 
            padding-bottom: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .cart-item-info {
            flex-grow: 1;
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        
        <header class="py-3 mb-4 border-bottom teto-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <h1 class="h3 mb-0 text-white teto-title">🍞 Catálogo de Productos - Teto</h1>
                
                <nav class="nav">
                    <a href="ProductosUsuario.php" class="nav-link teto-nav-link active" aria-current="page">Catálogo</a>
                    <button class="btn btn-sm btn-info teto-btn-secondary ms-3" id="toggleCartBtn">
                        🛒 Carrito (<span id="cartItemCount">0</span>)
                    </button>
                </nav>
                
                <a href="logout.php" class="btn teto-btn-primary">Cerrar Sesión</a>
            </div>
        </header>

        <main class="teto-main-content" id="mainContent">

            <section class="teto-card p-3 border rounded">
                <h2 class="mb-3">Productos Disponibles</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-hover teto-table">
                        <thead class="teto-thead">
                            <tr>
                                <th scope="col">SKU</th>
                                <th scope="col">Nombre</th>
                                <th scope="col" class="text-end">Stock Disponible</th>
                                <th scope="col" class="text-end">Precio</th>
                                <th scope="col">Comprar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Lógica PHP: Obtener productos con stock > 0
                            // Se asume que la tabla 'productos' tiene las columnas corregidas en la BD
                            $sql = "SELECT id_producto, nombre_producto, stock_actual, precio_venta FROM productos WHERE stock_actual > 0 ORDER BY nombre_producto ASC";
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0):
                                while($row = $result->fetch_assoc()):
                            ?>
                                <tr>
                                    <td>TETO-<?php echo str_pad($row['id_producto'], 3, '0', STR_PAD_LEFT); ?></td>
                                    
                                    <td><?php echo htmlspecialchars($row['nombre_producto']); ?></td>
                                    
                                    <td class="text-end">
                                        <?php 
                                            // Alerta visual de poco stock
                                            if($row['stock_actual'] < 5) echo '<span class="badge bg-warning text-dark">¡Solo '.$row['stock_actual'].'!</span>';
                                            else echo $row['stock_actual']; 
                                        ?>
                                    </td>
                                    
                                    <td class="text-end" data-price="<?php echo $row['precio_venta']; ?>">
                                        $<?php echo number_format($row['precio_venta'], 2); ?>
                                    </td>
                                    
                                    <td>
                                        <button class="btn btn-sm btn-success add-to-cart-btn" 
                                                data-id="<?php echo $row['id_producto']; ?>" 
                                                data-name="<?php echo htmlspecialchars($row['nombre_producto']); ?>" 
                                                data-price="<?php echo $row['precio_venta']; ?>">
                                            Agregar 🛒
                                        </button>
                                    </td>
                                </tr>
                            <?php 
                                endwhile; 
                            else:
                            ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay productos disponibles en este momento.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <footer class="py-3 mt-4 border-top text-center teto-footer">
            <p class="mb-0 text-muted">&copy; 2025 Sistema de Inventario Teto | UTAU-Powered</p>
        </footer>

    </div> 

    <div class="cart-sidebar" id="cartSidebar">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="m-0">Mi Carrito 🛒</h3>
            <button class="btn btn-sm btn-outline-light" id="closeCartBtn">X</button>
        </div>
        
        <div id="cartItemsContainer" style="overflow-y: auto; max-height: 60vh;">
            <p class="text-muted" id="emptyCartMessage">El carrito está vacío.</p>
        </div>
        
        <hr class="border-light">
        
        <div class="d-flex justify-content-between fw-bold mb-2">
            <span>Total:</span>
            <span id="cartTotal">$0.00</span>
        </div>
        
        <button id="checkoutBtn" class="btn w-100 mt-3" style="background-color: #4CAF50; color: white;">
            Proceder al Pago (Confirmar)
        </button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
            crossorigin="anonymous">
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Referencias al DOM
            const cartSidebar = document.getElementById('cartSidebar');
            const toggleCartBtn = document.getElementById('toggleCartBtn');
            const closeCartBtn = document.getElementById('closeCartBtn');
            const cartItemsContainer = document.getElementById('cartItemsContainer');
            const cartTotal = document.getElementById('cartTotal');
            const cartItemCount = document.getElementById('cartItemCount');
            const emptyCartMessage = document.getElementById('emptyCartMessage');
            const checkoutBtn = document.getElementById('checkoutBtn');
            
            let cart = []; // Estado local del carrito

            // 1. Manejo visual del Sidebar (Abrir/Cerrar)
            toggleCartBtn.addEventListener('click', () => cartSidebar.classList.add('open'));
            closeCartBtn.addEventListener('click', () => cartSidebar.classList.remove('open'));

            // 2. Event Listeners para Añadir Producto (Delegación o directo)
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                button.addEventListener('click', (e) => {
                    const id = e.target.getAttribute('data-id');
                    const name = e.target.getAttribute('data-name');
                    const price = parseFloat(e.target.getAttribute('data-price'));

                    addItemToCart(id, name, price);
                    
                    // Feedback visual pequeño
                    const originalText = e.target.textContent;
                    e.target.textContent = "¡Añadido!";
                    setTimeout(() => e.target.textContent = originalText, 1000);
                    
                    // Abrir carrito automáticamente al agregar
                    cartSidebar.classList.add('open');
                });
            });
            
            // ===============================================
            // LÓGICA DEL CARRITO (Estado Local)
            // ===============================================

            function addItemToCart(id, name, price) {
                const existingItem = cart.find(item => item.id === id);
                if (existingItem) {
                    existingItem.quantity += 1;
                } else {
                    cart.push({ id, name, price, quantity: 1 });
                }
                updateCartUI();
            }

            function removeItemFromCart(id) {
                const itemIndex = cart.findIndex(item => item.id === id);
                if (itemIndex > -1) {
                    const item = cart[itemIndex];
                    if (item.quantity > 1) {
                        item.quantity -= 1;
                    } else {
                        cart.splice(itemIndex, 1);
                    }
                }
                updateCartUI();
            }

            function updateCartUI() {
                cartItemsContainer.innerHTML = ''; // Limpiar
                
                let total = 0;
                let itemCount = 0;

                if (cart.length === 0) {
                    emptyCartMessage.style.display = 'block';
                    cartItemsContainer.appendChild(emptyCartMessage);
                    checkoutBtn.disabled = true;
                } else {
                    emptyCartMessage.style.display = 'none';
                    checkoutBtn.disabled = false;

                    cart.forEach(item => {
                        const itemTotal = item.price * item.quantity;
                        total += itemTotal;
                        itemCount += item.quantity;

                        const itemDiv = document.createElement('div');
                        itemDiv.className = 'cart-item-row';
                        itemDiv.innerHTML = `
                            <div class="cart-item-info">
                                <strong>${item.name}</strong><br>
                                <small>(${item.quantity} x $${item.price.toFixed(2)})</small>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold me-2">$${itemTotal.toFixed(2)}</span>
                                <button class="btn btn-sm btn-danger remove-item-btn" data-id="${item.id}">X</button>
                            </div>
                        `;
                        cartItemsContainer.appendChild(itemDiv);
                    });

                    // Reasignar eventos a los botones de eliminar recién creados
                    document.querySelectorAll('.remove-item-btn').forEach(button => {
                        button.addEventListener('click', (e) => {
                            const id = e.target.getAttribute('data-id');
                            removeItemFromCart(id);
                        });
                    });
                }

                cartTotal.textContent = `$${total.toFixed(2)}`;
                cartItemCount.textContent = itemCount;
            }

            // ===============================================
            // PROCESO DE PAGO (AJAX al Backend)
            // ===============================================
            checkoutBtn.addEventListener('click', () => {
                if (cart.length === 0) return;

                if (!confirm("¿Deseas confirmar la compra por un total de " + cartTotal.textContent + "?")) {
                    return;
                }

                // Estado de carga
                checkoutBtn.disabled = true;
                checkoutBtn.textContent = "Procesando...";

                fetch('checkout_process.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ cart: cart })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("✅ " + data.message);
                        cart = []; // Vaciar carrito local
                        updateCartUI();
                        // Recargar la página para actualizar el stock visualmente
                        location.reload(); 
                    } else {
                        alert("❌ Error: " + data.message);
                        checkoutBtn.disabled = false;
                        checkoutBtn.textContent = "Proceder al Pago (Confirmar)";
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Error de conexión con el servidor.");
                    checkoutBtn.disabled = false;
                    checkoutBtn.textContent = "Proceder al Pago (Confirmar)";
                });
            });
        });
    </script>
</body>
</html>
<?php
// Cerrar conexión
$conn->close();
?>