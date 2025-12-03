<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Tetoris Shop</title>
    
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          crossorigin="anonymous">
          
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        /* Estilos específicos para la página de bienvenida */
        .welcome-hero {
            background-color: var(--teto-dark); /* Fondo oscuro */
            color: var(--teto-light);
            padding: 5rem 0;
            text-align: center;
        }
        .welcome-title {
            color: var(--teto-pink);
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .illustration-card {
            background-color: white;
            padding: 1rem;
            border: 3px solid var(--teto-red);
            border-radius: 10px;
            box-shadow: 5px 5px 0 var(--teto-pink);
            transition: transform 0.3s;
            height: 100%; /* Asegura que todas las tarjetas tengan la misma altura */
        }
        .illustration-card:hover {
            transform: translateY(-5px);
        }
        .illustration-card img {
            border-radius: 8px;
            object-fit: cover; /* Para que la imagen cubra el espacio sin distorsión */
            width: 100%;
            height: 250px; /* Altura fija para la imagen */
            margin-bottom: 1rem;
        }
        .teto-btn-welcome {
            font-size: 1.25rem;
            padding: 0.75rem 2rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body class="bg-light">

    <header class="welcome-hero">
        <div class="container">
            <h1 class="welcome-title">🥖 ¡Bienvenido a Tetoris Shop! 🥐</h1>
            <p class="lead">El sistema de inventario más divertido y eficiente, traído a ti por Teto Kasane.</p>
            
            <a href="forms/Login.php" class="btn teto-btn-primary teto-btn-welcome">
                ACCEDER A TU CUENTA 🥖
            </a>
        </div>
    </header>

    <main class="container py-5">
        
        <h2 class="text-center mb-4" style="color: var(--teto-red);">¡No es solo un sistema, es una diva!</h2>

        <div class="row row-cols-1 row-cols-md-3 g-4">

            <div class="col">
                <div class="illustration-card">
                     
                    <h3 class="h5" style="color: var(--teto-red);">Estándar de Calidad</h3>
                    <p class="text-muted">Gestión de inventario con precisión UTAU. La seriedad de la diva garantiza cero errores.</p>
                </div>
            </div>

            <div class="col">
                <div class="illustration-card">
                    
                    <h3 class="h5" style="color: var(--teto-red);">La Leyenda del Croissant</h3>
                    <p class="text-muted">Nuestros niveles de stock son tan confiables como sus créditos. ¡Siempre hay lo que necesiten para su hogar
                        !</p>
                </div>
            </div>

            <div class="col">
                <div class="illustration-card">
                    
                    <h3 class="h5" style="color: var(--teto-red);">Energía de Trabajo</h3>
                    <p class="text-muted">Interfaz rápida y dinámica, diseñada para la velocidad del UTAUloader.</p>
                </div>
            </div>

        </div>

    </main>

    <footer class="py-3 mt-5 teto-footer">
        <div class="container text-center">
            <p class="mb-0 text-muted">&copy; 2025 Tetoris Shop | Powered by UTAU & Croissants.</p>
            <p class="mb-0 text-muted small"><a href="register.html" style="color: var(--teto-pink);">¿Eres nuevo? Regístrate aquí.</a></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>