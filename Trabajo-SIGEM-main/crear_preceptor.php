<?php
require_once __DIR__ . '/conexion.php';

$usuario = 'preceptor1';
$password = password_hash('123456', PASSWORD_DEFAULT);
$rol = 'preceptor';
$activo = '1';

try {
    $consulta = $pdo->prepare(
        "SELECT id_usuario FROM usuarios WHERE usuario = ?"
    );
    $consulta->execute([$usuario]);

    if ($consulta->fetch()) {
        die("La cuenta ya existe.");
    }

    $sql = "INSERT INTO usuarios (usuario, password, rol, activo)
            VALUES (?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario, $password, $rol, $activo]);

    echo "Cuenta de preceptor creada correctamente.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>0