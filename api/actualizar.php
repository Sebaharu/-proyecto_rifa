<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    echo json_encode(["mensaje" => "Metodo no permitido"]);
    exit;
}

if (!isset($_SESSION["usuario"])) {
    http_response_code(401);
    echo json_encode(["mensaje" => "Acceso denegado. Inicie sesion"]);
    exit;
}

$id = $_POST["id"] ?? "";
$nombre = trim($_POST["nombre"] ?? "");
$apellido = trim($_POST["apellido"] ?? "");
$numero_rifa = $_POST["numero_rifa"] ?? "";

if ($id === "" || $nombre === "" || $apellido === "" || $numero_rifa === "") {
    http_response_code(400);
    echo json_encode(["mensaje" => "Todos los campos son obligatorios"]);
    exit;
}

if (!ctype_digit((string)$id) || (int)$id <= 0 ||
    !ctype_digit((string)$numero_rifa) || (int)$numero_rifa <= 0) {

    http_response_code(400);
    echo json_encode(["mensaje" => "ID y numero de rifa deben ser enteros positivos"]);
    exit;
}

$id = (int)$id;
$numero_rifa = (int)$numero_rifa;

$archivo = __DIR__ . "/../datos/participantes.json";

$fp = fopen($archivo, "c+");

if ($fp === false) {
    http_response_code(500);
    echo json_encode(["mensaje" => "Error al abrir el archivo"]);
    exit;
}

if (!flock($fp, LOCK_EX)) {
    fclose($fp);
    http_response_code(500);
    echo json_encode(["mensaje" => "Error al bloquear el archivo"]);
    exit;
}

rewind($fp);
$contenido = stream_get_contents($fp);
$participantes = json_decode($contenido, true);

if (!is_array($participantes)) {
    flock($fp, LOCK_UN);
    fclose($fp);

    http_response_code(500);
    echo json_encode(["mensaje" => "Archivo JSON invalido"]);
    exit;
}

$encontrado = false;
$numero_ocupado = false;

foreach ($participantes as $participante) {
    if (
        (int)($participante["numero_rifa"] ?? 0) === $numero_rifa &&
        (int)($participante["id"] ?? 0) !== $id
    ) {
        $numero_ocupado = true;
        break;
    }
}

if ($numero_ocupado) {
    flock($fp, LOCK_UN);
    fclose($fp);

    http_response_code(409);
    echo json_encode(["mensaje" => "El numero de rifa ya esta registrado"]);
    exit;
}

foreach ($participantes as &$participante) {

    if ((int)$participante["id"] === $id) {

        $participante["nombre"] = $nombre;
        $participante["apellido"] = $apellido;
        $participante["numero_rifa"] = $numero_rifa;

        $encontrado = true;
        break;
    }
}

unset($participante);

if (!$encontrado) {
    flock($fp, LOCK_UN);
    fclose($fp);

    http_response_code(404);
    echo json_encode(["mensaje" => "Participante no encontrado"]);
    exit;
}

$json = json_encode(
    $participantes,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
);

rewind($fp);

$guardado = $json !== false &&
    fwrite($fp, $json) === strlen($json);

if ($guardado) {
    $guardado = ftruncate($fp, strlen($json));
}

fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

if (!$guardado) {
    http_response_code(500);
    echo json_encode(["mensaje" => "Error al actualizar participante"]);
    exit;
}

echo json_encode([
    "mensaje" => "Participante actualizado correctamente"
]);
?>