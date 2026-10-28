<?php
session_start();

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario = $_POST["usuario"] ?? "";
    $contrasena = $_POST["contrasena"] ?? "";

    if ($usuario == "userupla" && $contrasena == "upla123") {

        session_regenerate_id(true);
        $_SESSION["usuario"] = $usuario;

        echo json_encode([
            "mensaje" => "Inicio de sesion exitoso"
        ]);

    } else {

        http_response_code(401);

        echo json_encode([
            "mensaje" => "Usuario o contraseña incorrectos"
        ]);
    }

} else {

    http_response_code(405);

    echo json_encode([
        "mensaje" => "Metodo no permitido"
    ]);
}
?>