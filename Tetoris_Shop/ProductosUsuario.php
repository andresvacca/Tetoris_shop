<?php
// Asegúrate de que la ruta sea correcta (ej: si está en la raíz, solo es 'db_connection.php')
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
            border-bottom: 1px solid #444; /* Borde más sutil */
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
                
                <button type="button" class="btn teto-btn-primary">Cerrar Sesión</button>
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
                                <th scope="col" class="text-end">Stock</th>
                                <th scope="col" class="text-end">Precio</th>
                                <th scope="col">Comprar</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <tr class="teto-row-ok">
                                <td>TETO-001</td>
                                <td>Pan Francés Cruasán</td>
                                <td class="text-end">150</td>
                                <td class="text-end" data-price="2.50">$2.50</td>
                                <td>
                                    <button class="btn btn-sm btn-success add-to-cart-btn" 
                                            data-id="1" 
                                            data-name="Pan Francés Cruasán" 
                                            data-price="2.50">
                                        Agregar
                                    </button>
                                </td>
                            </tr>
                            
                            <tr class="teto-row-low">
                                <td>TETO-002</td>
                                <td>Margarina Batida</td>
                                <td class="text-end">8</td>
                                <td class="text-end" data-price="1.99">$1.99</td>
                                <td>
                                    <button class="btn btn-sm btn-success add-to-cart-btn" 
                                            data-id="2" 
                                            data-name="Margarina Batida" 
                                            data-price="1.99">
                                        Agregar
                                    </button>
                                </td>
                            </tr>
                            
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
        <h3 class="mb-4">Mi Carrito 🛒</h3>
        
        <div id="cartItemsContainer" style="overflow-y: auto; max-height: 70vh;">
            <p class="text-muted" id="emptyCartMessage">El carrito está vacío.</p>
        </div>
        
        <hr class="border-light">
        
        <div class="d-flex justify-content-between fw-bold mb-2">
            <span>Total:</span>
            <span id="cartTotal">$0.00</span>
        </div>
        
        <button class="btn w-100 mt-3" style="background-color: #4CAF50; color: white;">
            Proceder al Pago (PHP)
        </button>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
            crossorigin="anonymous">
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cartSidebar = document.getElementById('cartSidebar');
            const toggleCartBtn = document.getElementById('toggleCartBtn');
            const cartItemsContainer = document.getElementById('cartItemsContainer');
            const cartTotal = document.getElementById('cartTotal');
            const cartItemCount = document.getElementById('cartItemCount');
            const emptyCartMessage = document.getElementById('emptyCartMessage');
            
            let cart = []; // Array para almacenar productos en el carrito

            // 1. Manejo visual del Sidebar
            toggleCartBtn.addEventListener('click', () => {
                cartSidebar.classList.toggle('open');
            });

            // 2. Event Listeners para Añadir Producto
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                button.addEventListener('click', (e) => {
                    const id = e.target.getAttribute('data-id');
                    const name = e.target.getAttribute('data-name');
                    const price = parseFloat(e.target.getAttribute('data-price'));

                    addItemToCart(id, name, price);
                });
            });
            
            // ===============================================
            // FUNCIONES DE LÓGICA DEL CARRITO
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

            // FUNCIÓN NUEVA: Eliminar o reducir la cantidad de un producto
            function removeItemFromCart(id) {
                const itemIndex = cart.findIndex(item => item.id === id);

                if (itemIndex > -1) {
                    const item = cart[itemIndex];
                    
                    // Si la cantidad es mayor a 1, solo la reducimos
                    if (item.quantity > 1) {
                        item.quantity -= 1;
                    } 
                    // Si la cantidad es 1, eliminamos el objeto del array
                    else {
                        cart.splice(itemIndex, 1);
                    }
                }

                updateCartUI();
            }


            function updateCartUI() {
                // Limpiar el contenedor
                cartItemsContainer.innerHTML = ''; 
                
                let total = 0;
                let itemCount = 0;

                if (cart.length === 0) {
                    emptyCartMessage.style.display = 'block';
                    cartItemsContainer.appendChild(emptyCartMessage);
                } else {
                    emptyCartMessage.style.display = 'none';

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
                                <button class="btn btn-sm btn-danger remove-item-btn" 
                                        data-id="${item.id}">
                                    X
                                </button>
                            </div>
                        `;
                        cartItemsContainer.appendChild(itemDiv);
                    });

                    // 3. Asignar Event Listeners a los NUEVOS botones de Eliminar
                    document.querySelectorAll('.remove-item-btn').forEach(button => {
                        button.addEventListener('click', (e) => {
                            const id = e.target.getAttribute('data-id');
                            removeItemFromCart(id);
                        });
                    });
                }

                // Actualizar totales y contador
                cartTotal.textContent = `$${total.toFixed(2)}`;
                cartItemCount.textContent = itemCount;
            }
        });
    </script>
</body>
</html>