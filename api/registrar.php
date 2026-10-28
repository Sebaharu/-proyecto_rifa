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

$nombre = trim($_POST["nombre"] ?? "");
$apellido = trim($_POST["apellido"] ?? "");
$numero_rifa = $_POST["numero_rifa"] ?? "";

if ($nombre == "" || $apellido == "" || $numero_rifa === "") {
    http_response_code(400);
    echo json_encode(["mensaje" => "Todos los campos son obligatorios"]);
    exit;
}

if (!ctype_digit((string)$numero_rifa) || (int)$numero_rifa <= 0) {
    http_response_code(400);
    echo json_encode(["mensaje" => "El numero de rifa debe ser un entero positivo"]);
    exit;
}

$numero_rifa = (int)$numero_rifa;

$archivo = __DIR__ . "/../datos/participantes.json";

$fp = fopen($archivo, "c+");

if ($fp === false) {
    http_response_code(500);
    echo json_encode(["mensaje" => "No se pudo abrir el archivo"]);
    exit;
}

if (!flock($fp, LOCK_EX)) {
    fclose($fp);
    http_response_code(500);
    echo json_encode(["mensaje" => "No se pudo bloquear el archivo"]);
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

foreach ($participantes as $participante) {
    if (isset($participante["numero_rifa"]) &&
        (int)$participante["numero_rifa"] === $numero_rifa) {

        flock($fp, LOCK_UN);
        fclose($fp);

        http_response_code(409);
        echo json_encode(["mensaje" => "El numero de rifa ya esta registrado"]);
        exit;
    }
}

$nuevo_id = 1;

foreach ($participantes as $participante) {
    $nuevo_id = max($nuevo_id, (int)($participante["id"] ?? 0) + 1);
}

$nuevo_participante = [
    "id" => $nuevo_id,
    "nombre" => $nombre,
    "apellido" => $apellido,
    "numero_rifa" => $numero_rifa
];

$participantes[] = $nuevo_participante;

$json = json_encode(
    $participantes,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
);

rewind($fp);
$guardado = $json !== false
    && fwrite($fp, $json) === strlen($json);

if ($guardado) {
    $guardado = ftruncate($fp, strlen($json));
}

fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

if (!$guardado) {
    http_response_code(500);
    echo json_encode(["mensaje" => "Error al guardar participante"]);
    exit;
}

http_response_code(201);

echo json_encode([
    "mensaje" => "Participante registrado correctamente",
    "participante" => $nuevo_participante
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>