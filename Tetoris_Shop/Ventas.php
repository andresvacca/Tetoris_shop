<?php
// Asegúrate de que la ruta sea correcta (ej: si está en la raíz, solo es 'db_connection.php')
require_once 'db_connection.php'; 


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas Teto - Registro de Transacciones</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
        crossorigin="anonymous">
        
    <link rel="stylesheet" href="css/inventario.css"> 
</head>
<body>

    <div class="container-fluid">
        
        <header class="py-3 mb-4 border-bottom teto-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <h1 class="h3 mb-0 text-white teto-title">💸 Registro de Ventas - Teto</h1>
                
                <nav class="nav">
                    <a href="productos.php" class="nav-link teto-nav-link">Inventario</a>
                    <a href="dashboard.php" class="nav-link teto-nav-link">Dashboard</a>
                    <a href="ventas.php" class="nav-link teto-nav-link active" aria-current="page">Ventas</a>
                </nav>
                
                <button type="button" class="btn teto-btn-primary">Cerrar Sesión</button>
            </div>
        </header>

        <main class="teto-main-content">

            <section class="mb-4 p-3 border rounded teto-card">
                <h2>Filtros y Acciones</h2>
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="fecha-inicio" class="form-label">Desde:</label>
                        <input type="date" class="form-control teto-input" id="fecha-inicio">
                    </div>
                    <div class="col-md-3">
                        <label for="fecha-fin" class="form-label">Hasta:</label>
                        <input type="date" class="form-control teto-input" id="fecha-fin">
                    </div>
                    <div class="col-md-3">
                        <button class="btn w-100 btn-primary">Aplicar Filtros</button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn w-100 teto-btn-secondary">Exportar a CSV</button>
                    </div>
                </div>
            </section>

            <section class="teto-card p-3 border rounded">
                <h2 class="mb-3">Transacciones del Periodo</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-hover teto-table">
                        <thead class="teto-thead">
                            <tr>
                                <th scope="col">ID Venta</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Total</th>
                                <th scope="col">Método</th>
                                <th scope="col">Cajero</th>
                                <th scope="col">Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#87541</td>
                                <td>29/11/2025 14:30</td>
                                <td class="fw-bold text-success">$25.50</td>
                                <td>Tarjeta</td>
                                <td>Ana P.</td>
                                <td>
                                    <button class="btn btn-sm teto-btn-action">Ver</button>
                                </td>
                            </tr>
                             <tr>
                                <td>#87540</td>
                                <td>29/11/2025 13:15</td>
                                <td class="fw-bold text-success">$10.25</td>
                                <td>Efectivo</td>
                                <td>Juan G.</td>
                                <td>
                                    <button class="btn btn-sm teto-btn-action">Ver</button>
                                </td>
                            </tr>
                            <tr>
                                <td>#87539</td>
                                <td>29/11/2025 12:45</td>
                                <td class="fw-bold text-success">$5.75</td>
                                <td>Efectivo</td>
                                <td>Ana P.</td>
                                <td>
                                    <button class="btn btn-sm teto-btn-action">Ver</button>
                                </td>
                            </tr>
                            <?php
                                // Aquí iría la lógica PHP para listar las ventas
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <footer class="py-3 mt-4 border-top text-center teto-footer">
            <p class="mb-0 text-muted">&copy; 2025 Sistema de Inventario Teto | UTAU-Powered</p>
        </footer>

    </div> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
            crossorigin="anonymous">
    </script>
</body>
</html>