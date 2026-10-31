<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/verificar_sesion.php';

protegerPanel('alumno');

function volver($tipo, $mensaje) {
    header('Location: alumno.php?' . $tipo . '=' . urlencode($mensaje) . '#mesas');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: alumno.php');
    exit;
}

// Verificar el token de seguridad.
$token = $_POST['csrf_token'] ?? '';

if (
    !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    volver('error', 'La solicitud no es válida. Volvé a intentarlo.');
}

$idMesa = filter_input(INPUT_POST, 'id_mesa', FILTER_VALIDATE_INT);

if (!$idMesa || $idMesa <= 0) {
    volver('error', 'La mesa seleccionada no es válida.');
}

try {
    // Obtener el alumno vinculado a la cuenta.
    $stmt = $pdo->prepare("
        SELECT id_alumno
        FROM usuarios
        WHERE id_usuario = ?
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['id_usuario']]);
    $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cuenta || !$cuenta['id_alumno']) {
        volver('error', 'Tu cuenta no está vinculada a un alumno.');
    }

    $idAlumno = (int)$cuenta['id_alumno'];

    // Comprobar que la mesa exista y que todavía no haya pasado.
    $stmt = $pdo->prepare("
        SELECT ID_MESAS, ID_MATERIA, FECHA
        FROM mesas
        WHERE ID_MESAS = ?
          AND FECHA >= CURDATE()
        LIMIT 1
    ");
    $stmt->execute([$idMesa]);
    $mesa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mesa) {
        volver('error', 'La mesa no existe o su fecha ya pasó.');
    }

    if (empty($mesa['ID_MATERIA'])) {
        volver('error', 'Esta mesa todavía no tiene una materia asignada.');
    }

    // Evitar una inscripción duplicada.
    $stmt = $pdo->prepare("
        SELECT ID_INSCRIPCIONES
        FROM inscripciones
        WHERE ID_ALUMNOS = ?
          AND ID_MESAS = ?
        LIMIT 1
    ");
    $stmt->execute([$idAlumno, $idMesa]);

    if ($stmt->fetch()) {
        volver('error', 'Ya estás inscripto a esta mesa.');
    }

    // Registrar la inscripción.
    $stmt = $pdo->prepare("
        INSERT INTO inscripciones
        (FECHA, ID_ALUMNOS, ID_MATERIA, ID_MESAS)
        VALUES (CURDATE(), ?, ?, ?)
    ");

    $stmt->execute([
        $idAlumno,
        $mesa['ID_MATERIA'],
        $idMesa
    ]);

    volver('mensaje', '¡Inscripción realizada correctamente!');

} catch (PDOException $ex) {
    error_log($ex->getMessage());

    // También contempla la restricción UNIQUE de la base de datos.
    if ($ex->getCode() === '23000') {
        volver('error', 'Ya estás inscripto a esta mesa.');
    }

    volver('error', 'Ocurrió un error al realizar la inscripción.');
}
