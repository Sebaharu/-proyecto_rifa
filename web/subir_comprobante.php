<?php
session_start();

if (!isset($_SESSION["usuario_web"])) {
    header("Location: index.php");
    exit;
}

$carpeta = __DIR__ . "/../comprobantes/";
$mensaje = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!isset($_FILES["comprobante"])) {
        $error = "Debes seleccionar un archivo.";
    } else {

        $archivo = $_FILES["comprobante"];

        if ($archivo["error"] !== UPLOAD_ERR_OK) {
            $error = "Ocurrió un error al subir el archivo.";
        } elseif ($archivo["size"] > 5 * 1024 * 1024) {
            $error = "El archivo no puede superar los 5 MB.";
        } else {

            $tiposPermitidos = [
                "application/pdf" => "pdf",
                "image/jpeg" => "jpg",
                "image/png" => "png"
            ];

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $tipo = $finfo->file($archivo["tmp_name"]);

            if (!isset($tiposPermitidos[$tipo])) {
                $error = "Solo se permiten archivos PDF, JPG y PNG.";
            } else {

                if (!is_dir($carpeta)) {
                    mkdir($carpeta, 0755, true);
                }

                $nombreNuevo = bin2hex(random_bytes(12)) . "." . $tiposPermitidos[$tipo];
                $destino = $carpeta . $nombreNuevo;

                if (move_uploaded_file($archivo["tmp_name"], $destino)) {
                    $mensaje = "Comprobante cargado correctamente.";
                } else {
                    $error = "No se pudo guardar el comprobante.";
                }
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

    <title>Subir comprobante | Sistema de Rifa</title>

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
            background: #111827;
            border: 1px dashed #00ff00;
            color: white;
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            cursor: pointer;
        }

        .btn-subir {
            background: #00ff00;
            color: #030712;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-subir:hover {
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
                Carga de comprobantes
            </p>
        </div>

        <a href="panel.php"
           class="border border-green-500 text-green-400 px-4 py-2 rounded-lg hover:bg-green-900 transition">
            ← Volver al panel
        </a>

    </div>

</header>

<main class="relative z-10 max-w-2xl mx-auto px-6 py-12">

    <div class="text-center mb-10">

        <div class="text-5xl mb-5">📤</div>

        <h2 class="text-3xl font-bold mb-3">
            Subir <span class="neon">comprobante</span>
        </h2>

        <p class="text-gray-400">
            Adjunta un comprobante de pago en formato PDF, JPG o PNG.
        </p>

    </div>

    <div class="glass rounded-2xl p-8">

        <?php if ($mensaje !== ""): ?>

            <div class="bg-green-900 border border-green-500 text-green-300 p-4 rounded-lg mb-6 text-center">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>

        <?php endif; ?>

        <?php if ($error !== ""): ?>

            <div class="bg-red-900 border border-red-500 text-red-300 p-4 rounded-lg mb-6 text-center">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <label class="block text-gray-300 text-sm mb-4">
                Selecciona tu comprobante
            </label>

            <input
                type="file"
                name="comprobante"
                accept=".pdf,.jpg,.jpeg,.png"
                class="input-custom"
                required
            >

            <p class="text-gray-500 text-xs mt-4">
                Formatos permitidos: PDF, JPG y PNG. Tamaño máximo: 5 MB.
            </p>

            <button
                type="submit"
                class="btn-subir w-full py-3 rounded-lg mt-8"
            >
                📤 Subir comprobante
            </button>

        </form>

    </div>

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