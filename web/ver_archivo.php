<?php
session_start();

if (!isset($_SESSION["usuario_web"])) {
    http_response_code(403);
    exit("Acceso denegado. Debes iniciar sesión.");
}

$nombre = $_GET["archivo"] ?? "";

if (!is_string($nombre) || $nombre === "") {
    http_response_code(400);
    exit("Archivo no especificado.");
}

// Evitar rutas como ../archivo
if ($nombre !== basename($nombre)) {
    http_response_code(400);
    exit("Nombre de archivo inválido.");
}

$carpeta = realpath(__DIR__ . "/../comprobantes");

if ($carpeta === false) {
    http_response_code(404);
    exit("Carpeta no encontrada.");
}

$ruta = realpath($carpeta . DIRECTORY_SEPARATOR . $nombre);

if (
    $ruta === false ||
    dirname($ruta) !== $carpeta ||
    !is_file($ruta)
) {
    http_response_code(404);
    exit("Archivo no encontrado.");
}

$tipos = [
    "pdf" => "application/pdf",
    "jpg" => "image/jpeg",
    "jpeg" => "image/jpeg",
    "png" => "image/png"
];

$extension = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));

if (!isset($tipos[$extension])) {
    http_response_code(403);
    exit("Tipo de archivo no permitido.");
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$tipoReal = $finfo->file($ruta);

if ($tipoReal !== $tipos[$extension]) {
    http_response_code(403);
    exit("El contenido del archivo no es válido.");
}

header("Content-Type: " . $tipoReal);
header("Content-Length: " . filesize($ruta));
header("Content-Disposition: inline; filename=\"" . basename($ruta) . "\"");
header("X-Content-Type-Options: nosniff");
header("Cache-Control: no-store, private");

readfile($ruta);
exit;