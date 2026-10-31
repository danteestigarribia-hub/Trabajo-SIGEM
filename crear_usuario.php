
<?php
require_once 'conexion.php';

$password = password_hash('123456', PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "INSERT INTO usuarios (usuario, password, rol, activo)
     VALUES (?, ?, ?, ?)"
);

$stmt->execute([
    'alumno1',
    $password,
    'alumno',
    '1'
]);

echo "Usuario creado correctamente";
?>
