<?php
require_once __DIR__ . '/conexion.php';

function volverConError($mensaje) {
    header('Location: registro.php?error=' . urlencode($mensaje));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registro.php');
    exit;
}

$rol = $_POST['rol'] ?? '';
$dni = trim($_POST['dni'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';
$confirmar = $_POST['confirmar_password'] ?? '';

if (!in_array($rol, ['alumno', 'docente'], true)) {
    volverConError('Seleccioná un tipo de cuenta válido.');
}

if ($dni === '' || $correo === '' || $usuario === '') {
    volverConError('Completá todos los campos.');
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    volverConError('Ingresá un correo electrónico válido.');
}

if (strlen($password) < 6) {
    volverConError('La contraseña debe tener al menos 6 caracteres.');
}

if ($password !== $confirmar) {
    volverConError('Las contraseñas no coinciden.');
}

if (strlen($usuario) > 50) {
    volverConError('El nombre de usuario es demasiado largo.');
}

try {

    // Seleccionar la tabla y los campos según el tipo de cuenta.
    if ($rol === 'alumno') {
        $tabla = 'alumnos';
        $campoId = 'ID_ALUMNOS';
        $campoRelacion = 'id_alumno';
    } else {
        $tabla = 'docentes';
        $campoId = 'ID_DOCENTES';
        $campoRelacion = 'id_docente';
    }

    $pdo->beginTransaction();

    // Buscar el registro y comprobar que coincidan DNI y correo.
    $sql = "SELECT `$campoId`
            FROM `$tabla`
            WHERE DNI = :dni
            AND LOWER(TRIM(CORREO)) = LOWER(TRIM(:correo))
            LIMIT 1";

    $consulta = $pdo->prepare($sql);
    $consulta->execute([
        ':dni' => $dni,
        ':correo' => $correo
    ]);

    $persona = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$persona) {
        $pdo->rollBack();
        volverConError(
            'No encontramos un registro con ese DNI y correo. Verificá tus datos con la institución.'
        );
    }

    $idPersona = $persona[$campoId];

    // Comprobar si el nombre de usuario ya existe.
    $consulta = $pdo->prepare(
        "SELECT id_usuario FROM usuarios WHERE usuario = :usuario LIMIT 1"
    );

    $consulta->execute([':usuario' => $usuario]);

    if ($consulta->fetch()) {
        $pdo->rollBack();
        volverConError('Ese nombre de usuario ya está ocupado.');
    }

    // Comprobar si esa persona ya tiene una cuenta.
    $sql = "SELECT id_usuario
            FROM usuarios
            WHERE `$campoRelacion` = :idPersona
            LIMIT 1";

    $consulta = $pdo->prepare($sql);
    $consulta->execute([':idPersona' => $idPersona]);

    if ($consulta->fetch()) {
        $pdo->rollBack();
        volverConError('Esta persona ya tiene una cuenta registrada.');
    }

    // Proteger la contraseña.
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Crear la cuenta y vincularla con el alumno o docente.
    $sql = "INSERT INTO usuarios
            (usuario, password, rol, activo, `$campoRelacion`)
            VALUES
            (:usuario, :password, :rol, '1', :idPersona)";

    $consulta = $pdo->prepare($sql);

    $consulta->execute([
        ':usuario' => $usuario,
        ':password' => $passwordHash,
        ':rol' => $rol,
        ':idPersona' => $idPersona
    ]);

    $pdo->commit();

    header(
        'Location: login.php?mensaje=' .
        urlencode('Cuenta creada correctamente. Ya podés iniciar sesión.')
    );
    exit;

} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log($e->getMessage());

    volverConError(
        'Ocurrió un error al crear la cuenta. Revisá la configuración de la base de datos.'
    );
}