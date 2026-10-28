<?php
session_start();

if (!isset($_SESSION["usuario_web"])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel principal</title>
</head>

<body>

    <h1>🎟️ Panel principal - Sistema de Rifa</h1>

    <h3>
        Bienvenido,
        <?php echo htmlspecialchars($_SESSION["usuario_web"]); ?>
    </h3>

    <hr>

    <h2>Menú principal</h2>

    <ul>
        <li><a href="rifa.php">Reservar números</a></li>
        <li><a href="buscar.php">Buscar participantes</a></li>
        <li><a href="subir_comprobante.php">Subir comprobante</a></li>
        <li><a href="archivos.php">Ver comprobantes</a></li>
        <li><a href="salir.php">Cerrar sesión</a></li>
    </ul>

</body>
</html>