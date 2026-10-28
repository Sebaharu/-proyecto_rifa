<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    http_response_code(405);
    echo json_encode(["mensaje" => "Metodo no permitido"]);
    exit;
}

if (!isset($_SESSION["usuario"])) {
    http_response_code(401);
    echo json_encode(["mensaje" => "Acceso denegado. Inicie sesion"]);
    exit;
}

$id = $_GET["id"] ?? "";

if (!ctype_digit((string)$id) || (int)$id <= 0) {
    http_response_code(400);
    echo json_encode(["mensaje" => "ID invalido"]);
    exit;
}

$id = (int)$id;

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
$nuevos_participantes = [];

foreach ($participantes as $participante) {

    if ((int)($participante["id"] ?? 0) === $id) {
        $encontrado = true;
    } else {
        $nuevos_participantes[] = $participante;
    }
}

if (!$encontrado) {
    flock($fp, LOCK_UN);
    fclose($fp);

    http_response_code(404);
    echo json_encode(["mensaje" => "Participante no encontrado"]);
    exit;
}

$json = json_encode(
    $nuevos_participantes,
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
    echo json_encode(["mensaje" => "Error al eliminar participante"]);
    exit;
}

echo json_encode([
    "mensaje" => "Participante eliminado correctamente"
]);
?>