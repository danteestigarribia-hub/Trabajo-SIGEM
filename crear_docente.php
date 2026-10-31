
<?php
require_once __DIR__ . '/conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idDocente = (int) ($_POST['id_docente'] ?? 0);
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($idDocente <= 0 || $usuario === '' || strlen($password) < 6) {
        $mensaje = 'Completá todos los campos. La contraseña debe tener al menos 6 caracteres.';
    } else {
        try {
            // Comprobar que existe el docente
            $consulta = $pdo->prepare(
                "SELECT ID_DOCENTES FROM docentes
                 WHERE ID_DOCENTES = ?"
            );
            $consulta->execute([$idDocente]);

            if (!$consulta->fetch()) {
                $mensaje = 'El docente seleccionado no existe.';
            } else {
                // Comprobar que no tenga otra cuenta
                $consulta = $pdo->prepare(
                    "SELECT id_usuario FROM usuarios
                     WHERE usuario = ? OR id_docente = ?"
                );
                $consulta->execute([$usuario, $idDocente]);

                if ($consulta->fetch()) {
                    $mensaje = 'El usuario ya existe o el docente ya tiene una cuenta.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);

                    $insertar = $pdo->prepare(
                        "INSERT INTO usuarios
                        (usuario, password, rol, activo, id_docente)
                        VALUES (?, ?, 'docente', '1', ?)"
                    );

                    $insertar->execute([
                        $usuario,
                        $hash,
                        $idDocente
                    ]);

                    $mensaje = '¡Cuenta de docente creada correctamente!';
                }
            }
        } catch (PDOException $e) {
            $mensaje = 'Error al crear la cuenta. Revisá la base de datos.';
        }
    }
}

// Obtener docentes registrados
$docentes = $pdo->query(
    "SELECT ID_DOCENTES, NyA FROM docentes ORDER BY NyA"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>SIGEM | Crear cuenta docente</title>
<style>
body {
    background: #0f1a4a;
    font-family: Arial;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}
form {
    background: white;
    padding: 30px;
    border-radius: 15px;
    width: 350px;
}
h2 { color: #152d4a; }
input, select, button {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    box-sizing: border-box;
    border-radius: 7px;
}
input, select { border: 1px solid #ccc; }
button {
    background: #152d4a;
    color: white;
    border: none;
    cursor: pointer;
}
.mensaje { color: #152d4a; }
</style>
</head>
<body>

<form method="POST">
    <h2>Crear cuenta docente</h2>

    <label>Seleccionar docente</label>
    <select name="id_docente" required>
        <option value="">Elegir docente</option>
        <?php foreach ($docentes as $d): ?>
            <option value="<?= (int)$d['ID_DOCENTES'] ?>">
                <?= htmlspecialchars($d['NyA']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Nombre de usuario</label>
    <input type="text" name="usuario" maxlength="50" required>

    <label>Contraseña</label>
    <input type="password" name="password" minlength="6" required>

    <button type="submit">Crear cuenta</button>

    <p class="mensaje">
        <?= htmlspecialchars($mensaje) ?>
    </p>
</form>

</body>
</html>
