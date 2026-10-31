
<?php
session_start();

if (isset($_SESSION['id_usuario'])) {
    $destinos = [
        'alumno' => 'alumno.php',
        'docente' => 'docente.php',
        'preceptor' => 'preceptor.php'
    ];

    $rol = strtolower($_SESSION['rol']);
    if (isset($destinos[$rol])) {
        header('Location: ' . $destinos[$rol]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SIGEM | Iniciar sesión</title>
<style>
* { box-sizing: border-box; }
body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #0f1a4a;
    font-family: Arial, sans-serif;
}
.login-box {
    width: 90%;
    max-width: 400px;
    padding: 35px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 15px 40px #0005;
}
.logo {
    text-align: center;
    color: #152d4a;
    font-size: 32px;
    font-weight: bold;
}
.subtitle {
    text-align: center;
    color: #777;
    margin-bottom: 30px;
}
label {
    display: block;
    margin: 15px 0 7px;
    color: #25344a;
    font-weight: bold;
}
input {
    width: 100%;
    padding: 13px;
    border: 1px solid #ccd3df;
    border-radius: 8px;
    font-size: 15px;
}
button {
    width: 100%;
    margin-top: 25px;
    padding: 14px;
    border: none;
    border-radius: 8px;
    background: #152d4a;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}
button:hover { background: #254d78; }
.error {
    padding: 10px;
    margin-top: 15px;
    border-radius: 7px;
    background: #fee2e2;
    color: #b91c1c;
    text-align: center;
}
</style>
</head>
<body>

<div class="login-box">
    <div class="logo">SIGEM</div>
    <p class="subtitle">Sistema de Gestión Académica</p>

    <form action="procesar_login.php" method="POST">
        <label>Usuario</label>
        <input type="text" name="usuario"
               placeholder="Ingresá tu usuario" required>

        <label>Contraseña</label>
        <input type="password" name="password"
               placeholder="Ingresá tu contraseña" required>

        <button type="submit">Iniciar sesión</button>

        <p>
    ¿No tenés una cuenta?
    <a href="registro.php">Crear cuenta</a>
</p>
    </form>

    <?php if (isset($_GET['error'])): ?>
        <div class="error">
            Usuario o contraseña incorrectos,
            o cuenta desactivada.
        </div>
    <?php endif; ?>
</div>

</body>
</html>
