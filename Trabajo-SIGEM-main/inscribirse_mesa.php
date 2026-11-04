

<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/verificar_sesion.php';

protegerPanel('preceptor');

function volver($tipo, $mensaje) {
    header('Location: dashboard.php?' . $tipo . '=' . urlencode($mensaje) . '#gestionar-inscripciones');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$token = $_POST['csrf_token'] ?? '';

if (
    !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    volver('error', 'La solicitud no es válida. Volvé a intentarlo.');
}

$idAlumno = filter_input(INPUT_POST, 'id_alumno', FILTER_VALIDATE_INT);
$idMesa = filter_input(INPUT_POST, 'id_mesa', FILTER_VALIDATE_INT);

if (!$idAlumno || !$idMesa || $idAlumno <= 0 || $idMesa <= 0) {
    volver('error', 'Seleccioná un alumno y una mesa válidos.');
}

try {
    // Comprobar que el alumno exista.
    $stmt = $pdo->prepare("
        SELECT ID_ALUMNOS
        FROM alumnos
        WHERE ID_ALUMNOS = ?
        LIMIT 1
    ");
    $stmt->execute([$idAlumno]);

    if (!$stmt->fetch()) {
        volver('error', 'El alumno seleccionado no existe.');
    }

    // Comprobar que la mesa exista y todavía no haya pasado.
    $stmt = $pdo->prepare("
        SELECT ID_MESAS, ID_MATERIA
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
        volver('error', 'La mesa no tiene una materia asignada.');
    }

    // Evitar inscripciones duplicadas.
    $stmt = $pdo->prepare("
        SELECT ID_INSCRIPCIONES
        FROM inscripciones
        WHERE ID_ALUMNOS = ?
          AND ID_MESAS = ?
        LIMIT 1
    ");
    $stmt->execute([$idAlumno, $idMesa]);

    if ($stmt->fetch()) {
        volver('error', 'El alumno ya está inscripto en esa mesa.');
    }

    // Registrar la inscripción realizada por Preceptoría.
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

    volver('mensaje', 'El alumno fue inscripto correctamente.');

} catch (PDOException $ex) {
    error_log($ex->getMessage());

    if ($ex->getCode() === '23000') {
        volver('error', 'El alumno ya está inscripto en esa mesa.');
    }

    volver('error', 'No se pudo realizar la inscripción. Revisá la base de datos.');
}
