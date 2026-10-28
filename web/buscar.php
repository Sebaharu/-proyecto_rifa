<?php
session_start();

if (!isset($_SESSION["usuario_web"])) {
    header("Location: index.php");
    exit;
}

$participantes = [
    [
        "nombre" => "Universidad",
        "apellido" => "Playa Ancha",
        "ciudad" => "Valparaiso",
        "categoria" => "Grupo A"
    ],
    [
        "nombre" => "Sebastian",
        "apellido" => "Reyes",
        "ciudad" => "Valparaiso",
        "categoria" => "Grupo B"
    ],
    [
        "nombre" => "Camila",
        "apellido" => "Gonzalez",
        "ciudad" => "Santiago",
        "categoria" => "Grupo A"
    ],
    [
        "nombre" => "Felipe",
        "apellido" => "Carrasco",
        "ciudad" => "Viña del Mar",
        "categoria" => "Grupo C"
    ]
];

$resultado = "";
$busquedaRealizada = false;

$nombre = trim($_GET["nombre"] ?? "");
$apellido = trim($_GET["apellido"] ?? "");
$ciudad = trim($_GET["ciudad"] ?? "");
$categoria = trim($_GET["categoria"] ?? "");

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["buscar"])) {

    $busquedaRealizada = true;
    $resultado = "Participante no encontrado";

    if ($nombre !== "" && $apellido !== "" &&
        $ciudad !== "" && $categoria !== "") {

        foreach ($participantes as $participante) {

            if (
                strcasecmp($participante["nombre"], $nombre) === 0 &&
                strcasecmp($participante["apellido"], $apellido) === 0 &&
                strcasecmp($participante["ciudad"], $ciudad) === 0 &&
                strcasecmp($participante["categoria"], $categoria) === 0
            ) {
                $resultado = "Participante encontrado";
                break;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Buscar participantes | Sistema de Rifa</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background: #030712;
            color: white;
            font-family: Arial, sans-serif;
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
            transition: background-color 1.5s;
        }

        .square:hover {
            background: #00ff00;
            transition-duration: 0s;
        }

        .glass {
            background: rgba(31, 41, 55, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(0, 255, 0, 0.2);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.4);
        }

        .neon {
            color: #00ff00;
            text-shadow: 0 0 12px rgba(0, 255, 0, 0.4);
        }

        .input-custom {
            width: 100%;
            background: #111827;
            border: 1px solid #374151;
            color: white;
            padding: 12px;
            border-radius: 8px;
            box-sizing: border-box;
            transition: 0.3s;
        }

        .input-custom:focus {
            outline: none;
            border-color: #00ff00;
            box-shadow: 0 0 12px rgba(0, 255, 0, 0.2);
        }

        .btn-buscar {
            background: #00ff00;
            color: #030712;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-buscar:hover {
            background: #00cc00;
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.4);
        }
    </style>
</head>

<body>

<div id="background" class="background"></div>

<header class="glass relative z-10 px-6 py-5">

    <div class="max-w-6xl mx-auto flex flex-wrap justify-between items-center gap-4">

        <div>
            <h1 class="neon text-2xl font-bold">
                🎟️ SISTEMA DE RIFA
            </h1>

            <p class="text-gray-400 text-sm">
                Búsqueda de participantes
            </p>
        </div>

        <a href="panel.php"
           class="border border-green-500 text-green-400 px-4 py-2 rounded-lg hover:bg-green-900 transition">
            ← Volver al panel
        </a>

    </div>

</header>

<main class="relative z-10 max-w-3xl mx-auto px-6 py-12">

    <div class="text-center mb-10">

        <h2 class="text-3xl font-bold mb-3">
            Buscar <span class="neon">participantes</span>
        </h2>

        <p class="text-gray-400">
            Ingresa los cuatro datos del participante.
        </p>

    </div>

    <div class="glass rounded-2xl p-8">

        <form method="GET" action="buscar.php">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="block text-gray-300 text-sm mb-2">
                        Nombre
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        class="input-custom"
                        placeholder="Ingresa el nombre"
                        value="<?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">
                        Apellido
                    </label>

                    <input
                        type="text"
                        name="apellido"
                        class="input-custom"
                        placeholder="Ingresa el apellido"
                        value="<?php echo htmlspecialchars($apellido, ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">
                        Ciudad
                    </label>

                    <input
                        type="text"
                        name="ciudad"
                        class="input-custom"
                        placeholder="Ingresa la ciudad"
                        value="<?php echo htmlspecialchars($ciudad, ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">
                        Categoría
                    </label>

                    <input
                        type="text"
                        name="categoria"
                        class="input-custom"
                        placeholder="Ejemplo: Grupo A"
                        value="<?php echo htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

            </div>

            <button
                type="submit"
                name="buscar"
                value="1"
                class="btn-buscar w-full py-3 rounded-lg mt-8"
            >
                🔎 Buscar participante
            </button>

        </form>

    </div>

    <?php if ($busquedaRealizada): ?>

        <div class="glass rounded-xl p-6 mt-8 text-center">

            <?php if ($resultado === "Participante encontrado"): ?>

                <div class="text-4xl mb-3">✅</div>

                <h3 class="text-green-400 text-xl font-bold">
                    Participante encontrado
                </h3>

                <p class="text-gray-400 text-sm mt-3">
                    Los cuatro datos coinciden con un participante registrado.
                </p>

            <?php else: ?>

                <div class="text-4xl mb-3">❌</div>

                <h3 class="text-red-400 text-xl font-bold">
                    Participante no encontrado
                </h3>

                <p class="text-gray-400 text-sm mt-3">
                    No existe un participante que coincida con los cuatro datos.
                </p>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</main>

<script>
    const background = document.getElementById("background");

    function crearFondo() {
        background.innerHTML = "";

        const columnas = Math.ceil(window.innerWidth / 45);
        const filas = Math.ceil(window.innerHeight / 45);

        for (let i = 0; i < columnas * filas; i++) {
            const cuadrado = document.createElement("div");
            cuadrado.className = "square";
            background.appendChild(cuadrado);
        }
    }

    crearFondo();

    window.addEventListener("resize", crearFondo);
</script>

</body>
</html>