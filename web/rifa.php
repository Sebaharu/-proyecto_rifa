<?php
session_start();

if (!isset($_SESSION["usuario_web"])) {
    header("Location: index.php");
    exit;
}

$archivo = __DIR__ . "/../datos/reservas.json";
$totalNumeros = 100;
$precioNumero = 1000;
$mensaje = $_SESSION["mensaje_rifa"] ?? "";
$error = $_SESSION["error_rifa"] ?? "";

unset($_SESSION["mensaje_rifa"]);
unset($_SESSION["error_rifa"]);
if (!file_exists($archivo)) {
    file_put_contents($archivo, "[]", LOCK_EX);
}

function leerReservas($archivo) {
    $contenido = file_get_contents($archivo);
    $reservas = json_decode($contenido, true);

    return is_array($reservas) ? $reservas : [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $accion = $_POST["accion"] ?? "";
    $numero = filter_input(INPUT_POST, "numero", FILTER_VALIDATE_INT);

    if ($numero === false || $numero === null ||
        $numero < 1 || $numero > $totalNumeros) {

        $error = "Número de rifa inválido.";

    } elseif (!in_array($accion, ["reservar", "quitar"], true)) {

        $error = "Acción inválida.";

    } else {

        $fp = fopen($archivo, "c+");

        if ($fp === false || !flock($fp, LOCK_EX)) {
            $error = "No se pudo acceder a las reservas.";

            if ($fp !== false) {
                fclose($fp);
            }

        } else {

            rewind($fp);
            $contenido = stream_get_contents($fp);
            $reservas = json_decode($contenido, true);

            if (!is_array($reservas)) {
                $error = "El archivo de reservas no es válido.";

            } else {

                $indice = null;

                foreach ($reservas as $i => $reserva) {
                    if ((int)$reserva["numero"] === $numero) {
                        $indice = $i;
                        break;
                    }
                }

                if ($accion === "reservar") {

                    if ($indice !== null) {
                        $error = "Este número ya está reservado.";
                    } else {
                        $reservas[] = [
                            "numero" => $numero,
                            "usuario" => $_SESSION["usuario_web"],
                            "fecha" => date("Y-m-d H:i:s")
                        ];

                        $mensaje = "Número $numero reservado correctamente.";
                    }

                } elseif ($accion === "quitar") {

                    if ($indice === null) {
                        $error = "Este número no tiene una reserva.";
                    } else {
                        array_splice($reservas, $indice, 1);
                        $mensaje = "Reserva del número $numero eliminada.";
                    }
                }

                if ($error === "" && $mensaje !== "") {

                    $json = json_encode(
                        $reservas,
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                    );

                    rewind($fp);

                    $guardado = $json !== false &&
                        fwrite($fp, $json) === strlen($json);

                    if ($guardado) {
                        $guardado = ftruncate($fp, strlen($json));
                    }

                    fflush($fp);

                    if (!$guardado) {
                        $error = "No se pudieron guardar los cambios.";
                        $mensaje = "";
                    }
                }
            }

            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $_SESSION["mensaje_rifa"] = $mensaje;
    $_SESSION["error_rifa"] = $error;

    header("Location: rifa.php", true, 303);
    exit;
}

$reservas = leerReservas($archivo);

$numerosReservados = [];

foreach ($reservas as $reserva) {
    $numerosReservados[] = (int)$reserva["numero"];
}

$cantidadReservados = count($numerosReservados);
$cantidadDisponibles = $totalNumeros - $cantidadReservados;
$totalRecaudado = $cantidadReservados * $precioNumero;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reservar números | Sistema de Rifa</title>

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

        .numero {
            padding: 12px 0;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.2s;
            cursor: pointer;
            border: 1px solid #374151;
            background: #1f2937;
            color: white;
        }

        .numero:hover {
            transform: scale(1.08);
            border-color: #00ff00;
        }

        .numero.disponible {
            background: #064e3b;
            border-color: #059669;
            color: #6ee7b7;
        }

        .numero.reservado {
            background: #7f1d1d;
            border-color: #dc2626;
            color: #fca5a5;
        }

        .numero.seleccionado {
            outline: 3px solid #00ff00;
            box-shadow: 0 0 15px rgba(0, 255, 0, 0.5);
        }

        .boton {
            background: #00ff00;
            color: #030712;
            font-weight: bold;
            transition: 0.3s;
        }

        .boton:hover {
            background: #00cc00;
            box-shadow: 0 0 15px rgba(0, 255, 0, 0.4);
        }

        .boton-rojo {
            background: #dc2626;
            color: white;
            transition: 0.3s;
        }

        .boton-rojo:hover {
            background: #b91c1c;
        }
    </style>
</head>

<body>

<div id="background" class="background"></div>

<header class="glass relative z-10 px-6 py-5">

    <div class="max-w-6xl mx-auto flex justify-between items-center gap-4">

        <div>
            <h1 class="neon text-2xl font-bold">
                🎟️ SISTEMA DE RIFA
            </h1>

            <p class="text-gray-400 text-sm">
                Administración de reservas
            </p>
        </div>

        <a href="panel.php"
           class="border border-green-500 text-green-400 px-4 py-2 rounded-lg hover:bg-green-900">
            ← Volver al panel
        </a>

    </div>

</header>

<main class="relative z-10 max-w-6xl mx-auto px-6 py-10">

    <div class="text-center mb-10">

        <h2 class="text-3xl font-bold mb-3">
            Reserva de <span class="neon">números</span>
        </h2>

        <p class="text-gray-400">
            Selecciona un número para reservarlo o quitar su reserva.
        </p>

    </div>

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

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        <div class="glass rounded-xl p-6 text-center">
            <p class="text-gray-400 mb-2">Disponibles</p>
            <h3 class="text-3xl font-bold text-green-400">
                <?php echo $cantidadDisponibles; ?>
            </h3>
        </div>

        <div class="glass rounded-xl p-6 text-center">
            <p class="text-gray-400 mb-2">Reservados</p>
            <h3 class="text-3xl font-bold text-red-400">
                <?php echo $cantidadReservados; ?>
            </h3>
        </div>

        <div class="glass rounded-xl p-6 text-center">
            <p class="text-gray-400 mb-2">Valor de las reservas</p>
            <h3 class="text-3xl font-bold neon">
                $<?php echo number_format($totalRecaudado, 0, ",", "."); ?>
            </h3>
        </div>

    </div>

    <!-- Cuadrícula -->
    <div class="glass rounded-2xl p-6 md:p-8">

        <div class="flex flex-wrap gap-5 mb-7 text-sm">

            <span class="text-green-400">● Disponible</span>
            <span class="text-red-400">● Reservado</span>
            <span class="neon">● Seleccionado</span>

        </div>

        <div class="grid grid-cols-5 sm:grid-cols-8 md:grid-cols-10 gap-3">

            <?php for ($i = 1; $i <= $totalNumeros; $i++): ?>

                <?php
                $reservado = in_array($i, $numerosReservados);
                ?>

                <button
                    type="button"
                    class="numero <?php echo $reservado ? 'reservado' : 'disponible'; ?>"
                    onclick="seleccionarNumero(this, <?php echo $i; ?>, <?php echo $reservado ? 'true' : 'false'; ?>)"
                >
                    <?php echo $i; ?>
                </button>

            <?php endfor; ?>

        </div>

    </div>

    <!-- Formulario -->
    <div class="glass rounded-2xl p-8 mt-8 max-w-xl mx-auto">

        <h3 class="neon text-xl font-bold mb-5 text-center">
            Gestionar reserva
        </h3>

        <form method="POST" id="formReserva">

            <label class="block text-gray-300 text-sm mb-2">
                Número seleccionado
            </label>

            <input
                type="number"
                name="numero"
                id="numeroSeleccionado"
                min="1"
                max="100"
                readonly
                required
                placeholder="Selecciona un número de la cuadrícula"
                class="w-full p-3 rounded-lg bg-gray-900 border border-gray-600 text-white mb-5"
            >

            <p id="estadoNumero" class="text-center text-gray-400 mb-5">
                Selecciona un número para continuar.
            </p>

            <div class="grid grid-cols-2 gap-4">

                <button
                    type="submit"
                    name="accion"
                    value="reservar"
                    id="btnReservar"
                    disabled
                    class="boton py-3 rounded-lg disabled:opacity-40"
                >
                    Reservar
                </button>

                <button
                    type="submit"
                    name="accion"
                    value="quitar"
                    id="btnQuitar"
                    disabled
                    class="boton-rojo py-3 rounded-lg disabled:opacity-40"
                >
                    Quitar reserva
                </button>

            </div>

        </form>

    </div>

    <p class="text-center text-gray-500 text-sm mt-8">
        Precio por número:
        $<?php echo number_format($precioNumero, 0, ",", "."); ?>
    </p>

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

    let elementoAnterior = null;

    function seleccionarNumero(elemento, numero, reservado) {

        if (elementoAnterior !== null) {
            elementoAnterior.classList.remove("seleccionado");
        }

        elemento.classList.add("seleccionado");
        elementoAnterior = elemento;

        document.getElementById("numeroSeleccionado").value = numero;

        const btnReservar = document.getElementById("btnReservar");
        const btnQuitar = document.getElementById("btnQuitar");
        const estado = document.getElementById("estadoNumero");

        if (reservado) {
            estado.textContent = "El número " + numero + " está reservado.";
            estado.className = "text-center text-red-400 mb-5";

            btnReservar.disabled = true;
            btnQuitar.disabled = false;
        } else {
            estado.textContent = "El número " + numero + " está disponible.";
            estado.className = "text-center text-green-400 mb-5";

            btnReservar.disabled = false;
            btnQuitar.disabled = true;
        }
    }
</script>

</body>
</html>