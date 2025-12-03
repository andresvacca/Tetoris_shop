<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Teto - Gestión</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">

    <link rel="stylesheet" href="css    /inventario.css">
</head>

<body>

    <?php

    session_start();

    // Definir los roles que SÍ tienen acceso
    $allowed_roles = ['ADMINISTRADOR', 'EMPLEADO'];

    // Verificar que la sesión esté iniciada Y que el rol sea uno de los permitidos
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowed_roles)) {
        // Si no está autorizado, redirigir al login
        header("Location: Login.php?error=acceso_denegado");
        exit();
    }
    // 1. Incluir la conexión a la BD
    // Asegúrate de que este archivo esté en el mismo directorio raíz que Productos.php
    require_once 'db_connection.php';

    // 2. Definir la consulta SQL para obtener todos los productos
    // Nota: Solo se seleccionan los campos relevantes para la tabla.
    $sql = "SELECT id, nombre_producto, valor_unitario, stock FROM productos ORDER BY nombre_producto ASC";

    // 3. Ejecutar la consulta
    $result = $conn->query($sql);
    ?>

    <div class="container-fluid">

        <header class="py-3 mb-4 border-bottom teto-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <h1 class="h3 mb-0 text-white teto-title">🥖 Control de Inventario - Teto</h1>

                <nav class="nav">
                    <a href="productos.php" class="nav-link teto-nav-link active" aria-current="page">Inventario</a>
                    <a href="dashboard.php" class="nav-link teto-nav-link">Dashboard</a>
                    <a href="ventas.php" class="nav-link teto-nav-link">Ventas</a>
                </nav>

                <button type="button" class="btn teto-btn-primary">Cerrar Sesión</button>
            </div>
        </header>

        <main class="teto-main-content">

            <section class="mb-4 p-3 border rounded teto-card">
                <h2>Búsqueda y Filtros</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="search" class="form-control teto-input" placeholder="Buscar producto o código..." aria-label="Buscar">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select teto-input" aria-label="Filtro de Stock">
                            <option selected>Mostrar Todos</option>
                            <option value="low">Stock Bajo</option>
                            <option value="zero">Sin Stock</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <a href="Producto_Formulario.php" class="btn w-100 teto-btn-secondary">Añadir Nuevo Producto</a>
                    </div>
                </div>
            </section>

            <section class="teto-card p-3 border rounded">
                <h2 class="mb-3">Lista de Productos</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-hover teto-table">
                        <thead class="teto-thead">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Stock</th>
                                <th scope="col">Precio ($)</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            // Lógica para llenar la tabla
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {

                                    // Determinar la clase de fila según el stock
                                    $row_class = 'teto-row-ok'; // Por defecto
                                    $stock = $row['stock'];

                                    if ($stock == 0) {
                                        $row_class = 'teto-row-zero';
                                    } elseif ($stock <= 10) { // Usamos 10 como umbral de Stock Bajo
                                        $row_class = 'teto-row-low';
                                    }

                                    // Iniciar la fila con su clase de estilo
                                    echo "<tr class='$row_class'>";

                                    // Mostrar datos
                                    echo "<td>" . $row['id'] . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nombre_producto']) . "</td>";
                                    echo "<td>" . $stock . "</td>";
                                    echo "<td>" . number_format($row['valor_unitario'], 2) . "</td>";

                                    // Columna de Acciones
                                    echo "<td>";
                                    // Enlace de edición (pasando el ID)
                                    echo "<a href='Producto_Formulario.php?id=" . $row['id'] . "' class='btn btn-sm teto-btn-action me-2'>Editar</a>";

                                    // Formulario de Eliminación (¡NUEVO CÓDIGO!)
                                    echo "<form method='POST' action='process_product.php' style='display:inline;' 
                                                onsubmit='return confirm(\"¿Estás seguro de eliminar: " . htmlspecialchars($row['nombre_producto']) . "?\")'>";

                                    // Campos ocultos
                                    echo "<input type='hidden' name='action' value='delete'>";
                                    echo "<input type='hidden' name='product_id' value='" . $row['id'] . "'>";

                                    // Botón de envío
                                    echo "<button type='submit' class='btn btn-sm btn-danger'>Eliminar</button>";

                                    echo "</form>";

                                    echo "</td>";

                                    echo "</tr>";
                                }
                            } else {
                                // Si no hay resultados
                                echo "<tr><td colspan='5' class='text-center'>No se encontraron productos en la base de datos.</td></tr>";
                            }
                            ?>

                        </tbody>

                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <footer class="py-3 mt-4 border-top text-center teto-footer">
            <p class="mb-0 text-muted">&copy; 2025 Sistema de Inventario Teto | UTAU-Powered</p>
        </footer>

    </div>

    <?php
    // 5. Cerrar la conexión (buena práctica)
    $conn->close();
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous">
    </script>
    <script src="app.js"></script>
</body>

</html>