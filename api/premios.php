<?php
session_start();

header("Content-Type: application/json");

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

$archivo = __DIR__ . "/../datos/premios.json";

if (!file_exists($archivo)) {
    http_response_code(500);
    echo json_encode(["mensaje" => "Archivo no encontrado"]);
    exit;
}

$contenido = file_get_contents($archivo);
$premios = json_decode($contenido, true);

if (!is_array($premios)) {
    http_response_code(500);
    echo json_encode(["mensaje" => "Error al leer premios"]);
    exit;
}

echo json_encode($premios, JSON_PRETTY_PRINT);
?>