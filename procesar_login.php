
<?php
session_start();
require_once __DIR__ . '/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';

if ($usuario === '' || $password === '') {
    header('Location: login.php?error=1');
    exit;
}

$sql = "SELECT id_usuario, usuario, password, rol, activo
        FROM usuarios
        WHERE usuario = :usuario
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute(['usuario' => $usuario]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    header('Location: login.php?error=1');
    exit;
}

$activo = strtolower(trim($user['activo']));

if (!in_array($activo, ['1', 'si', 'sí', 'activo', 'true', 'habilitado'], true)) {
    header('Location: login.php?error=1');
    exit;
}

$rol = strtolower(trim($user['rol']));

$destinos = [
    'alumno' => 'alumno.php',
    'docente' => 'docente.php',
    'preceptor' => 'preceptor.php'
];

if (!isset($destinos[$rol])) {
    header('Location: login.php?error=1');
    exit;
}

session_regenerate_id(true);

$_SESSION['id_usuario'] = $user['id_usuario'];
$_SESSION['usuario'] = $user['usuario'];
$_SESSION['rol'] = $rol;

header('Location: ' . $destinos[$rol]);
exit;
