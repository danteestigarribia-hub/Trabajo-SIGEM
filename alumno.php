
<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/verificar_sesion.php';

protegerPanel('alumno');

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function e($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$usuario = $_SESSION['usuario'] ?? 'Alumno';
$idUsuario = $_SESSION['id_usuario'];
try {
    // Obtener el alumno vinculado a la cuenta.
    $stmt = $pdo->prepare("
        SELECT id_alumno
        FROM usuarios
        WHERE id_usuario = ?
        LIMIT 1
    ");
    $stmt->execute([$idUsuario]);
    $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cuenta || !$cuenta['id_alumno']) {
        die("Esta cuenta no está vinculada a un alumno. Contactá al administrador.");
    }

    $idAlumno = (int)$cuenta['id_alumno'];

    // Datos personales del alumno.
    $stmt = $pdo->prepare("
        SELECT NyA, DNI, DIRECCION, CORREO, TELEFONO
        FROM alumnos
        WHERE ID_ALUMNOS = ?
    ");
    $stmt->execute([$idAlumno]);
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$alumno) {
        die("No se encontraron los datos del alumno.");
    }

    // Materias del alumno.
    $stmt = $pdo->prepare("
        SELECT DISTINCT m.ID_MATERIAS, m.NOM_MATERIA
        FROM inscripciones i
        INNER JOIN materias m ON m.ID_MATERIAS = i.ID_MATERIA
        WHERE i.ID_ALUMNOS = ?
        ORDER BY m.NOM_MATERIA
    ");
    $stmt->execute([$idAlumno]);
    $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calificaciones.
    $stmt = $pdo->prepare("
        SELECT
            m.NOM_MATERIA,
            i.CALIFICACIONES,
            i.FECHA
        FROM inscripciones i
        LEFT JOIN materias m ON m.ID_MATERIAS = i.ID_MATERIA
        WHERE i.ID_ALUMNOS = ?
        ORDER BY i.FECHA DESC
    ");
    $stmt->execute([$idAlumno]);
    $calificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mesas de examen futuras.
    $stmt = $pdo->prepare("
        SELECT
            me.ID_MESAS,
            me.FECHA,
            me.AULA,
            m.NOM_MATERIA,
            d.NyA AS DOCENTE
        FROM mesas me
        LEFT JOIN materias m ON m.ID_MATERIAS = me.ID_MATERIA
        LEFT JOIN docentes d ON d.ID_DOCENTES = me.ID_DOCENTE
        WHERE me.FECHA >= CURDATE()
        ORDER BY me.FECHA ASC
    ");
    $stmt->execute();
    $mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Inscripciones a mesas del alumno.
    $stmt = $pdo->prepare("
        SELECT
            i.ID_MESAS,
            me.FECHA,
            me.AULA,
            m.NOM_MATERIA
        FROM inscripciones i
        INNER JOIN mesas me ON me.ID_MESAS = i.ID_MESAS
        LEFT JOIN materias m ON m.ID_MATERIAS = me.ID_MATERIA
        WHERE i.ID_ALUMNOS = ?
        ORDER BY me.FECHA DESC
    ");
    $stmt->execute([$idAlumno]);
    $misInscripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $mesasInscritas = array_map(
        'intval',
        array_column($misInscripciones, 'ID_MESAS')
    );

} catch (PDOException $ex) {
    error_log($ex->getMessage());
    die("Ocurrió un error al consultar los datos. Revisá la configuración de la base de datos.");
}

$mensaje = $_GET['mensaje'] ?? '';
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel del Alumno - SIGEM</title>

<style>
* { box-sizing: border-box; }

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f0f2f8;
    color: #202944;
}

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    width: 245px;
    background: #0f1a4a;
    color: white;
    padding: 25px 18px;
}

.logo {
    font-size: 25px;
    font-weight: bold;
    margin-bottom: 35px;
}

.sidebar a {
    display: block;
    color: white;
    text-decoration: none;
    padding: 13px;
    border-radius: 7px;
    margin-bottom: 8px;
}

.sidebar a:hover {
    background: #263b82;
}

.contenido {
    margin-left: 245px;
    padding: 30px;
}

.encabezado {
    background: white;
    padding: 22px;
    border-radius: 12px;
    margin-bottom: 22px;
    box-shadow: 0 3px 12px rgba(0,0,0,.05);
}

.encabezado h1 {
    margin: 0 0 8px;
    color: #0f1a4a;
}

.tarjetas {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 18px;
    margin-bottom: 25px;
}

.tarjeta {
    background: white;
    padding: 22px;
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(0,0,0,.05);
}

.numero {
    font-size: 30px;
    font-weight: bold;
    color: #172b70;
}

.seccion {
    background: white;
    padding: 22px;
    border-radius: 12px;
    margin-bottom: 22px;
    box-shadow: 0 3px 12px rgba(0,0,0,.05);
    overflow-x: auto;
}

h2 {
    color: #0f1a4a;
    margin-top: 0;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 550px;
}

th, td {
    padding: 13px;
    border-bottom: 1px solid #e5e7ef;
    text-align: left;
}

th {
    background: #edf0fa;
    color: #172b70;
}

.boton {
    background: #172b70;
    color: white;
    padding: 9px 13px;
    border: 0;
    border-radius: 6px;
    cursor: pointer;
}

.boton:hover {
    background: #29449a;
}

.boton:disabled {
    background: #999;
    cursor: not-allowed;
}

.mensaje {
    padding: 13px;
    border-radius: 7px;
    margin-bottom: 18px;
}

.correcto {
    background: #d9f5df;
    color: #17652a;
}

.error {
    background: #ffe0e0;
    color: #a00000;
}

.datos p {
    margin: 9px 0;
}

.cerrar {
    position: absolute;
    bottom: 20px;
    left: 18px;
    right: 18px;
    background: #a72c36;
}

@media(max-width: 750px) {
    .sidebar {
        position: relative;
        width: 100%;
        padding: 15px;
    }

    .logo { margin-bottom: 15px; }

    .cerrar {
        position: static;
        margin-top: 15px;
    }

    .contenido {
        margin-left: 0;
        padding: 15px;
    }
}
</style>
</head>

<body>

<aside class="sidebar">
    <div class="logo">SIGEM</div>

    <a href="#inicio">🏠 Inicio</a>
    <a href="#materias">📚 Mis materias</a>
    <a href="#notas">📝 Mis calificaciones</a>
    <a href="#mesas">📅 Mesas de examen</a>
    <a href="#inscripciones">📋 Mis inscripciones</a>
    <a href="#datos">👤 Mis datos</a>

    <a class="cerrar" href="cerrar_sesion.php">Cerrar sesión</a>
</aside>

<main class="contenido">

    <section class="encabezado" id="inicio">
        <h1>Bienvenido, <?= e($alumno['NyA']) ?></h1>
        <p>Panel de gestión del alumno - SIGEM</p>
    </section>

    <?php if ($mensaje): ?>
        <div class="mensaje correcto"><?= e($mensaje) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="mensaje error"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="tarjetas">
        <div class="tarjeta">
            <p>Mis materias</p>
            <div class="numero"><?= count($materias) ?></div>
        </div>

        <div class="tarjeta">
            <p>Mesas disponibles</p>
            <div class="numero"><?= count($mesas) ?></div>
        </div>

        <div class="tarjeta">
            <p>Mis inscripciones</p>
            <div class="numero"><?= count($misInscripciones) ?></div>
        </div>
    </div>

    <section class="seccion" id="materias">
        <h2>📚 Mis materias</h2>

        <?php if (count($materias) > 0): ?>
            <table>
                <tr><th>Materia</th></tr>
                <?php foreach ($materias as $materia): ?>
                    <tr>
                        <td><?= e($materia['NOM_MATERIA']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Todavía no tenés materias registradas.</p>
        <?php endif; ?>
    </section>

    <section class="seccion" id="notas">
        <h2>📝 Mis calificaciones</h2>

        <?php if (count($calificaciones) > 0): ?>
            <table>
                <tr>
                    <th>Materia</th>
                    <th>Calificación</th>
                    <th>Fecha</th>
                </tr>

                <?php foreach ($calificaciones as $nota): ?>
                    <tr>
                        <td><?= e($nota['NOM_MATERIA'] ?? 'Sin materia') ?></td>
                        <td>
                            <?= $nota['CALIFICACIONES'] !== null
                                ? e($nota['CALIFICACIONES'])
                                : 'Pendiente' ?>
                        </td>
                        <td><?= e($nota['FECHA'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Todavía no tenés calificaciones registradas.</p>
        <?php endif; ?>
    </section>

    <section class="seccion" id="mesas">
        <h2>📅 Mesas de examen disponibles</h2>

        <?php if (count($mesas) > 0): ?>
            <table>
                <tr>
                    <th>Materia</th>
                    <th>Fecha</th>
                    <th>Aula</th>
                    <th>Docente</th>
                    <th>Acción</th>
                </tr>

                <?php foreach ($mesas as $mesa): ?>
                    <tr>
                        <td><?= e($mesa['NOM_MATERIA'] ?? 'Sin materia') ?></td>
                        <td><?= e($mesa['FECHA']) ?></td>
                        <td><?= e($mesa['AULA'] ?? 'A confirmar') ?></td>
                        <td><?= e($mesa['DOCENTE'] ?? 'A confirmar') ?></td>
                        <td>
                            <?php if (in_array((int)$mesa['ID_MESAS'], $mesasInscritas, true)): ?>
                                <button class="boton" disabled>Ya inscripto</button>
                            <?php else: ?>
                                <form action="inscribirse_mesa.php" method="POST"
                                      onsubmit="return confirm('¿Confirmás tu inscripción a esta mesa?');">
                                    <input type="hidden" name="csrf_token"
                                           value="<?= e($_SESSION['csrf_token']) ?>">
                                    <input type="hidden" name="id_mesa"
                                           value="<?= (int)$mesa['ID_MESAS'] ?>">
                                    <button class="boton" type="submit">Inscribirme</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No hay mesas de examen futuras disponibles.</p>
        <?php endif; ?>
    </section>

    <section class="seccion" id="inscripciones">
        <h2>📋 Mis inscripciones</h2>

        <?php if (count($misInscripciones) > 0): ?>
            <table>
                <tr>
                    <th>Materia</th>
                    <th>Fecha del examen</th>
                    <th>Aula</th>
                </tr>

                <?php foreach ($misInscripciones as $inscripcion): ?>
                    <tr>
                        <td><?= e($inscripcion['NOM_MATERIA'] ?? 'Sin materia') ?></td>
                        <td><?= e($inscripcion['FECHA']) ?></td>
                        <td><?= e($inscripcion['AULA'] ?? 'A confirmar') ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Todavía no estás inscripto a ninguna mesa.</p>
        <?php endif; ?>
    </section>

    <section class="seccion datos" id="datos">
        <h2>👤 Mis datos personales</h2>

        <p><strong>Nombre:</strong> <?= e($alumno['NyA']) ?></p>
        <p><strong>DNI:</strong> <?= e($alumno['DNI']) ?></p>
        <p><strong>Dirección:</strong> <?= e($alumno['DIRECCION']) ?></p>
        <p><strong>Correo:</strong> <?= e($alumno['CORREO']) ?></p>
        <p><strong>Teléfono:</strong> <?= e($alumno['TELEFONO']) ?></p>
    </section>

</main>
</body>
</html>
