<?php
session_start();

if (!isset($_SESSION["usuario_web"])) {
    header("Location: index.php");
    exit;
}

$carpeta = __DIR__ . "/../comprobantes/";
$archivos = [];

if (is_dir($carpeta)) {

    foreach (scandir($carpeta) as $archivo) {

        if ($archivo === "." || $archivo === "..") {
            continue;
        }

        $ruta = $carpeta . $archivo;

        if (is_file($ruta)) {

            $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

            if (in_array($extension, ["pdf", "jpg", "jpeg", "png"], true)) {

                $archivos[] = [
                    "nombre" => $archivo,
                    "tamano" => round(filesize($ruta) / 1024, 2),
                    "fecha" => date("d/m/Y H:i", filemtime($ruta)),
                    "extension" => $extension
                ];
            }
        }
    }
}

$cantidadArchivos = count($archivos);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Comprobantes | Sistema de Rifa</title>

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

        .archivo-card {
            transition: 0.3s;
        }

        .archivo-card:hover {
            transform: translateY(-5px);
            border-color: #00ff00;
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.2);
        }

        .btn-ver {
            background: #00ff00;
            color: #030712;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-ver:hover {
            background: #00cc00;
            box-shadow: 0 0 15px rgba(0, 255, 0, 0.4);
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
                Administración de comprobantes
            </p>
        </div>

        <a href="panel.php"
           class="border border-green-500 text-green-400 px-4 py-2 rounded-lg hover:bg-green-900 transition">
            ← Volver al panel
        </a>

    </div>

</header>

<main class="relative z-10 max-w-6xl mx-auto px-6 py-12">

    <div class="text-center mb-10">

        <div class="text-5xl mb-5">📁</div>

        <h2 class="text-3xl font-bold mb-3">
            Archivos <span class="neon">almacenados</span>
        </h2>

        <p class="text-gray-400">
            Visualiza los comprobantes que han sido cargados al sistema.
        </p>

    </div>

    <!-- Estadísticas -->
    <div class="glass rounded-xl p-6 mb-8 text-center max-w-sm mx-auto">

        <p class="text-gray-400 mb-2">
            Total de comprobantes
        </p>

        <h3 class="neon text-4xl font-bold">
            <?php echo $cantidadArchivos; ?>
        </h3>

    </div>

    <?php if ($cantidadArchivos === 0): ?>

        <div class="glass rounded-2xl p-10 text-center">

            <div class="text-5xl mb-5">📂</div>

            <h3 class="text-xl font-bold mb-3">
                No hay comprobantes disponibles
            </h3>

            <p class="text-gray-400 mb-6">
                Todavía no se han subido archivos al sistema.
            </p>

            <a href="subir_comprobante.php"
               class="btn-ver inline-block px-6 py-3 rounded-lg">
                Subir comprobante
            </a>

        </div>

    <?php else: ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <?php foreach ($archivos as $archivo): ?>

                <div class="glass archivo-card rounded-2xl p-6">

                    <div class="text-5xl mb-5">

                        <?php if ($archivo["extension"] === "pdf"): ?>
                            📕
                        <?php else: ?>
                            🖼️
                        <?php endif; ?>

                    </div>

                    <h3 class="neon font-bold mb-4 break-all">

                        <?php
                        echo htmlspecialchars(
                            $archivo["nombre"],
                            ENT_QUOTES,
                            "UTF-8"
                        );
                        ?>

                    </h3>

                    <div class="text-gray-400 text-sm space-y-2 mb-6">

                        <p>
                            📦 Tamaño:
                            <?php echo $archivo["tamano"]; ?> KB
                        </p>

                        <p>
                            📅 Fecha:
                            <?php echo $archivo["fecha"]; ?>
                        </p>

                        <p>
                            📄 Formato:
                            <?php echo strtoupper($archivo["extension"]); ?>
                        </p>

                    </div>

                    <a
                        href="<?php
                            echo htmlspecialchars(
                                "ver_archivo.php?archivo=" .
                                urlencode($archivo["nombre"]),
                                ENT_QUOTES,
                                "UTF-8"
                            );
                        ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn-ver block text-center py-3 rounded-lg"
                    >
                        👁️ Visualizar archivo
                    </a>

                </div>

            <?php endforeach; ?>

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