<?php
require_once __DIR__ . '/verificar_sesion.php';

protegerPanel('docente');

$usuario = $_SESSION['usuario'] ?? 'Docente';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Docente - SIGEM</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #eef1f8;
        }

        header {
            background: #0f1a4a;
            color: white;
            padding: 22px;
        }

        .contenedor {
            padding: 30px;
        }

        .tarjeta {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,.08);
            margin-top: 20px;
        }

        a {
            display: inline-block;
            background: #172b70;
            color: white;
            padding: 12px 20px;
            border-radius: 7px;
            text-decoration: none;
            margin-top: 15px;
        }

        a:hover {
            background: #243d91;
        }
    </style>
</head>

<body>

<header>
    <h1>SIGEM - Panel Docente</h1>
    <p>Bienvenido, <?= htmlspecialchars($usuario) ?></p>
</header>

<div class="contenedor">

    <div class="tarjeta">
        <h2>Panel del Docente</h2>
        <p>Ingresaste correctamente al sistema SIGEM.</p>
        <p>Desde aquí podrás acceder a tus herramientas de trabajo.</p>

        <a href="cerrar_sesion.php">Cerrar sesión</a>
    </div>

</div>

</body>
</html>