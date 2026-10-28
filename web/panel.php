<?php
session_start();

if (!isset($_SESSION["usuario_web"])) {
    header("Location: index.php");
    exit;
}

$usuario = htmlspecialchars($_SESSION["usuario_web"], ENT_QUOTES, "UTF-8");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel Principal | Sistema de Rifa</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            margin: 0;
            background: #030712;
            color: white;
            font-family: Arial, sans-serif;
            min-height: 100vh;
        }

        .background {
            position: fixed;
            inset: 0;
            z-index: -1;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(45px, 1fr));
            grid-auto-rows: 45px;
            overflow: hidden;
        }

        .square {
            background: #111827;
            border: 1px solid #030712;
            transition: background-color 1.5s ease;
        }

        .square:hover {
            background: #00ff00;
            transition-duration: 0s;
        }

        .glass {
            background: rgba(31, 41, 55, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(0, 255, 0, 0.2);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.4);
        }

        .neon-text {
            color: #00ff00;
            text-shadow: 0 0 15px rgba(0, 255, 0, 0.4);
        }

        .menu-card {
            transition: all 0.3s ease;
        }

        .menu-card:hover {
            transform: translateY(-8px);
            border-color: #00ff00;
            box-shadow: 0 0 25px rgba(0, 255, 0, 0.2);
        }

        .btn-salir {
            border: 1px solid #ef4444;
            color: #f87171;
            transition: 0.3s;
        }

        .btn-salir:hover {
            background: #ef4444;
            color: white;
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.4);
        }
    </style>
</head>

<body>

    <!-- Fondo interactivo -->
    <div id="background" class="background"></div>

    <!-- Encabezado -->
    <header class="glass relative z-10 px-6 py-5">

        <div class="max-w-6xl mx-auto flex flex-wrap justify-between items-center gap-4">

            <div>
                <h1 class="neon-text text-2xl font-bold">
                    🎟️ SISTEMA DE RIFA
                </h1>

                <p class="text-gray-400 text-sm mt-1">
                    Panel de administración
                </p>
            </div>

            <div class="flex items-center gap-5">

                <span class="text-gray-300 text-sm">
                    👤 <?php echo $usuario; ?>
                </span>

                <a href="salir.php"
                   class="btn-salir px-4 py-2 rounded-lg text-sm">
                    Cerrar sesión
                </a>

            </div>

        </div>

    </header>

    <!-- Contenido -->
    <main class="relative z-10 max-w-6xl mx-auto px-6 py-12">

        <div class="text-center mb-12">

            <h2 class="text-3xl font-bold mb-3">
                Bienvenido al
                <span class="neon-text">Panel Principal</span>
            </h2>

            <p class="text-gray-400">
                Selecciona una opción para administrar tu rifa.
            </p>

        </div>

        <!-- Tarjetas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Reservar números -->
            <a href="rifa.php" class="glass menu-card rounded-2xl p-8 block">

                <div class="text-5xl mb-5">🎟️</div>

                <h3 class="neon-text text-xl font-bold mb-3">
                    Reservar números
                </h3>

                <p class="text-gray-400 text-sm">
                    Administra los números disponibles y realiza reservas.
                </p>

                <p class="text-green-400 text-sm mt-6">
                    Acceder →
                </p>

            </a>

            <!-- Buscar participantes -->
            <a href="buscar.php" class="glass menu-card rounded-2xl p-8 block">

                <div class="text-5xl mb-5">🔎</div>

                <h3 class="neon-text text-xl font-bold mb-3">
                    Buscar participantes
                </h3>

                <p class="text-gray-400 text-sm">
                    Consulta los participantes registrados en la rifa.
                </p>

                <p class="text-green-400 text-sm mt-6">
                    Acceder →
                </p>

            </a>

            <!-- Subir comprobante -->
            <a href="subir_comprobante.php" class="glass menu-card rounded-2xl p-8 block">

                <div class="text-5xl mb-5">📤</div>

                <h3 class="neon-text text-xl font-bold mb-3">
                    Subir comprobante
                </h3>

                <p class="text-gray-400 text-sm">
                    Adjunta comprobantes de pago de los participantes.
                </p>

                <p class="text-green-400 text-sm mt-6">
                    Acceder →
                </p>

            </a>

            <!-- Ver comprobantes -->
            <a href="archivos.php" class="glass menu-card rounded-2xl p-8 block">

                <div class="text-5xl mb-5">📁</div>

                <h3 class="neon-text text-xl font-bold mb-3">
                    Ver comprobantes
                </h3>

                <p class="text-gray-400 text-sm">
                    Visualiza los archivos y comprobantes almacenados.
                </p>

                <p class="text-green-400 text-sm mt-6">
                    Acceder →
                </p>

            </a>

        </div>

    </main>

    <!-- Pie de página -->
    <footer class="relative z-10 text-center text-gray-500 text-xs py-8">

        Proyecto PHP y Servicios Web |
        Universidad de Playa Ancha

    </footer>

    <!-- Fondo animado -->
    <script>
        const background = document.getElementById("background");

        function createSquares() {
            background.innerHTML = "";

            const squareSize = 45;

            const columns = Math.ceil(window.innerWidth / squareSize);
            const rows = Math.ceil(window.innerHeight / squareSize);

            const total = columns * rows;

            for (let i = 0; i < total; i++) {
                const square = document.createElement("div");
                square.classList.add("square");
                background.appendChild(square);
            }
        }

        createSquares();

        window.addEventListener("resize", createSquares);

        setInterval(() => {
            const squares = document.querySelectorAll(".square");

            if (squares.length === 0) return;

            const randomIndex = Math.floor(Math.random() * squares.length);
            const square = squares[randomIndex];

            square.style.backgroundColor = "#00ff00";

            setTimeout(() => {
                square.style.backgroundColor = "";
            }, 1000);

        }, 150);
    </script>

</body>
</html>