<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    http_response_code(405);

    echo json_encode([
        "mensaje" => "Metodo no permitido"
    ]);
    exit;
}

// Eliminar las variables de sesion
$_SESSION = [];

// Eliminar la cookie de sesion
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir la sesion
session_destroy();

echo json_encode([
    "mensaje" => "Sesion cerrada correctamente"
]);
?>