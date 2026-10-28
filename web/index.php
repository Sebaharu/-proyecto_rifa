<?php
session_start();

if (isset($_SESSION["usuario_web"])) {
    header("Location: panel.php");
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario = trim($_POST["usuario"] ?? "");
    $contrasena = $_POST["contrasena"] ?? "";
    $captcha = $_POST["captcha"] ?? "";

    if (
        $usuario === "userupla" &&
        $contrasena === "upla123" &&
        $captcha !== "" &&
        ctype_digit((string)$captcha) &&
        isset($_SESSION["resultado_captcha"]) &&
        (int)$captcha === $_SESSION["resultado_captcha"]
    ) {
        session_regenerate_id(true);

        $_SESSION["usuario_web"] = $usuario;

        unset($_SESSION["resultado_captcha"]);

        header("Location: panel.php");
        exit;
    } else {
        $mensaje = "Usuario, contraseña o CAPTCHA incorrecto.";
    }
}

$numero1 = rand(1, 10);
$numero2 = rand(1, 10);

$_SESSION["resultado_captcha"] = $numero1 + $numero2;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Sistema de Rifa</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            margin: 0;
            background: #030712;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }

        .background {
            position: fixed;
            inset: 0;
            z-index: 0;
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

        .login-card {
            background: rgba(31, 41, 55, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);

            border: 1px solid rgba(0, 255, 0, 0.25);

            box-shadow:
                0 0 25px rgba(0, 255, 0, 0.08),
                0 10px 40px rgba(0, 0, 0, 0.5);
        }

        .neon-text {
            color: #00ff00;
            text-shadow: 0 0 15px rgba(0, 255, 0, 0.4);
        }

        .input-custom {
            background: rgba(17, 24, 39, 0.85);
            border: 1px solid #374151;
            color: white;
            transition: 0.3s;
        }

        .input-custom:focus {
            outline: none;
            border-color: #00ff00;
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.2);
        }

        .btn-login {
            background: #00ff00;
            color: #030712;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #00cc00;
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.5);
            transform: translateY(-2px);
        }

        .captcha-box {
            background: rgba(0, 255, 0, 0.05);
            border: 1px solid rgba(0, 255, 0, 0.3);
        }

        .login-container {
            position: relative;
            z-index: 10;
            pointer-events: none;
        }

        .login-card {
            pointer-events: auto;
        }
    </style>
</head>

<body>

    <!-- Fondo interactivo -->
    <div id="background" class="background"></div>

    <!-- Contenedor del login -->
    <div class="login-container min-h-screen flex items-center justify-center px-4">

        <div class="login-card w-full max-w-md rounded-2xl p-8">

            <!-- Encabezado -->
            <div class="text-center mb-8">

                <div class="text-5xl mb-4">
                    🎟️
                </div>

                <h1 class="neon-text text-3xl font-bold">
                    SISTEMA DE RIFA
                </h1>

                <p class="text-gray-400 text-sm mt-3">
                    Panel de administración
                </p>

            </div>

            <!-- Mensaje de error -->
            <?php if ($mensaje !== ""): ?>

                <div class="bg-red-900/40 border border-red-500 text-red-300 p-3 rounded-lg mb-5 text-sm text-center">

                    <?php echo htmlspecialchars($mensaje); ?>

                </div>

            <?php endif; ?>

            <!-- Formulario -->
            <form method="POST" action="index.php">

                <!-- Usuario -->
                <div class="mb-5">

                    <label class="block text-gray-300 text-sm mb-2">
                        Usuario
                    </label>

                    <input
                        type="text"
                        name="usuario"
                        placeholder="Ingresa tu usuario"
                        class="input-custom w-full p-3 rounded-lg text-sm"
                        required
                        autocomplete="username"
                    >

                </div>

                <!-- Contraseña -->
                <div class="mb-5">

                    <label class="block text-gray-300 text-sm mb-2">
                        Contraseña
                    </label>

                    <input
                        type="password"
                        name="contrasena"
                        placeholder="Ingresa tu contraseña"
                        class="input-custom w-full p-3 rounded-lg text-sm"
                        required
                        autocomplete="current-password"
                    >

                </div>

                <!-- CAPTCHA -->
                <div class="mb-6">

                    <label class="block text-gray-300 text-sm mb-2">
                        Verificación de seguridad
                    </label>

                    <div class="captcha-box rounded-lg p-4 mb-3 text-center">

                        <p class="text-gray-400 text-xs mb-2">
                            Resuelve la siguiente operación
                        </p>

                        <span class="neon-text text-2xl font-bold">

                            <?php echo $numero1; ?>

                            +

                            <?php echo $numero2; ?>

                            = ?

                        </span>

                    </div>

                    <input
                        type="number"
                        name="captcha"
                        placeholder="Resultado de la suma"
                        class="input-custom w-full p-3 rounded-lg text-sm"
                        required
                    >

                </div>

                <!-- Botón -->
                <button
                    type="submit"
                    class="btn-login w-full py-3 rounded-lg text-sm uppercase tracking-wider"
                >
                    Iniciar sesión
                </button>

            </form>

            <!-- Pie -->
            <div class="text-center mt-8">

                <p class="text-gray-500 text-xs">
                    Proyecto PHP y Servicios Web
                </p>

                <p class="text-gray-600 text-xs mt-1">
                    Universidad de Playa Ancha
                </p>

            </div>

        </div>

    </div>

    <!-- Animación del fondo -->
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

        // Animación aleatoria de cuadrados
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