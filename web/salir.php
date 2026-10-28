<?php
session_start();

// Verificar que exista una sesión web
if (!isset($_SESSION["usuario_web"])) {
    header("Location: index.php");
    exit;
}

// Eliminar todas las variables de sesión
$_SESSION = [];

// Eliminar la cookie de sesión
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

// Destruir la sesión
session_destroy();

// Evitar que se almacene la página en caché
header("Cache-Control: no-store, no-cache, must-revalidate");

// Redirigir al login
header("Location: index.php");
exit;
?>