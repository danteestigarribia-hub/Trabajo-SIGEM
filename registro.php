<?php
require_once __DIR__ . '/conexion.php';

$mensaje = $_GET['mensaje'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta - SIGEM</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #0f1a4a;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .contenedor {
            background: white;
            width: 100%;
            max-width: 430px;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(0,0,0,.25);
        }

        h1 {
            text-align: center;
            color: #0f1a4a;
            margin-top: 0;
        }

        p {
            text-align: center;
            color: #666;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #222;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin-top: 7px;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 15px;
        }

        button {
            width: 100%;
            padding: 13px;
            margin-top: 22px;
            background: #172b70;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #243d91;
        }

        .mensaje {
            padding: 10px;
            border-radius: 7px;
            margin: 12px 0;
            text-align: center;
        }

        .correcto {
            background: #d8f5df;
            color: #17652a;
        }

        .error {
            background: #ffe0e0;
            color: #a00000;
        }

        .volver {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #172b70;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="contenedor">

    <h1>Crear cuenta</h1>
    <p>Sistema de Gestión Educativa - SIGEM</p>

    <?php if ($mensaje): ?>
        <div class="mensaje correcto">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="mensaje error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="registrar_usuario.php" method="POST">

        <label>Tipo de cuenta</label>
        <select name="rol" required>
            <option value="">Seleccionar...</option>
            <option value="alumno">Alumno</option>
            <option value="docente">Docente</option>
        </select>

        <label>DNI</label>
        <input
            type="text"
            name="dni"
            maxlength="20"
            required
            placeholder="Ingresá tu DNI"
        >

        <label>Correo electrónico registrado en SIGEM</label>
        <input
            type="email"
            name="correo"
            maxlength="100"
            required
            placeholder="ejemplo@correo.com"
        >

        <label>Nombre de usuario</label>
        <input
            type="text"
            name="usuario"
            maxlength="50"
            required
            placeholder="Elegí un usuario"
        >

        <label>Contraseña</label>
        <input
            type="password"
            name="password"
            minlength="6"
            required
            placeholder="Mínimo 6 caracteres"
        >

        <label>Confirmar contraseña</label>
        <input
            type="password"
            name="confirmar_password"
            minlength="6"
            required
            placeholder="Repetí tu contraseña"
        >

        <button type="submit">Crear mi cuenta</button>

    </form>

    <a class="volver" href="login.php">Volver al inicio de sesión</a>

</div>

</body>
</html>