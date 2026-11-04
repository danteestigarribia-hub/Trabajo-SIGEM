

<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/verificar_sesion.php';

protegerPanel('alumno');

function e($valor): string {
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

$idUsuario = $_SESSION['id_usuario'];
$error = null;
$alumno = false;
$mesas = [];

try {
    // Buscar alumno vinculado a la cuenta.
    $stmt = $pdo->prepare("
        SELECT a.ID_ALUMNOS, a.NyA, a.DNI, a.CORREO,
               a.DIRECCION, a.TELEFONO
        FROM usuarios u
        INNER JOIN alumnos a ON a.ID_ALUMNOS = u.id_alumno
        WHERE u.id_usuario = ?
        LIMIT 1
    ");
    $stmt->execute([$idUsuario]);
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($alumno) {
        // Consultar mesas y estado de inscripción.
        $stmt = $pdo->prepare("
            SELECT
                me.ID_MESAS,
                me.FECHA,
                me.AULA,
                ma.NOM_MATERIA,
                i.ID_INSCRIPCIONES
            FROM mesas me
            LEFT JOIN materias ma
                ON me.ID_MATERIA = ma.ID_MATERIAS
            LEFT JOIN inscripciones i
                ON i.ID_MESAS = me.ID_MESAS
                AND i.ID_ALUMNOS = ?
            WHERE me.FECHA >= CURDATE()
            ORDER BY me.FECHA ASC
        ");
        $stmt->execute([$alumno['ID_ALUMNOS']]);
        $mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $ex) {
    error_log($ex->getMessage());
    $error = 'No se pudo consultar la información. Intentá nuevamente.';
    $alumno = false;
    $mesas = [];
}

$totalMesas = count($mesas);
$totalInscriptas = 0;

foreach ($mesas as $mesa) {
    if ($mesa['ID_INSCRIPCIONES']) {
        $totalInscriptas++;
    }
}

$totalPendientes = $totalMesas - $totalInscriptas;
$iniciales = 'AL';

if ($alumno) {
    $partes = preg_split('/\s+/', trim($alumno['NyA']));
    $iniciales = strtoupper(
        substr($partes[0] ?? 'A', 0, 1) .
        substr($partes[1] ?? '', 0, 1)
    );
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Mesa de Examen| Portal Estudiantil</title>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background: #f2f6fc;
    color: #27364d;
}

/* ENCABEZADO */
header {
    background: white;
    padding: 18px 5%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 3px 15px #1d355710;
}

.logo {
    color: #173b78;
    font-size: 23px;
    font-weight: bold;
}

.logo span {
    color: #5596e6;
}

.btn-salir {
    text-decoration: none;
    color: white;
    background: #173b78;
    padding: 11px 18px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: bold;
}

/* CONTENEDOR */
main {
    width: 90%;
    max-width: 1150px;
    margin: 35px auto;
}

/* BIENVENIDA */
.bienvenida {
    background: linear-gradient(120deg, #173b78, #4389d5);
    color: white;
    padding: 30px;
    border-radius: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 28px;
}

.bienvenida h1 {
    font-size: 27px;
    margin-bottom: 10px;
}

.bienvenida p {
    color: #e0edff;
    line-height: 1.5;
}

.avatar {
    width: 75px;
    height: 75px;
    min-width: 75px;
    background: white;
    color: #173b78;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 25px;
    font-weight: bold;
}

/* TARJETAS */
.resumen {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 28px;
}

.tarjeta {
    background: white;
    padding: 23px;
    border-radius: 13px;
    box-shadow: 0 3px 15px #1d355710;
    border-left: 5px solid #4389d5;
}

.tarjeta small {
    display: block;
    color: #77869c;
    margin-bottom: 12px;
    font-size: 14px;
}

.tarjeta strong {
    font-size: 30px;
    color: #173b78;
}

/* SECCIONES */
.panel {
    background: white;
    padding: 26px;
    border-radius: 14px;
    box-shadow: 0 3px 15px #1d355710;
    margin-bottom: 25px;
}

.panel h2 {
    color: #173b78;
    margin-bottom: 10px;
    font-size: 21px;
}

.descripcion {
    color: #78869b;
    margin-bottom: 22px;
    font-size: 14px;
}

/* DATOS PERSONALES */
.datos {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.dato {
    background: #f4f7fc;
    padding: 16px;
    border-radius: 9px;
}

.dato strong {
    display: block;
    color: #6480a5;
    font-size: 12px;
    margin-bottom: 8px;
    text-transform: uppercase;
}

.dato span {
    color: #263d61;
    font-weight: bold;
    overflow-wrap: anywhere;
}

/* TABLA */
.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 620px;
    border-collapse: collapse;
}

th {
    background: #edf3fc;
    color: #244a80;
    font-size: 13px;
    text-transform: uppercase;
}

th, td {
    padding: 16px 13px;
    text-align: left;
    border-bottom: 1px solid #e8edf5;
}

td {
    font-size: 14px;
}

.estado {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.inscripto {
    background: #d9f5e4;
    color: #176b39;
}

.pendiente {
    background: #fff0cf;
    color: #805600;
}

.aviso {
    background: #fff3cd;
    color: #765500;
    padding: 16px;
    border-radius: 9px;
    line-height: 1.5;
}

.vacio {
    padding: 22px;
    background: #f4f7fc;
    color: #718096;
    border-radius: 9px;
    text-align: center;
}

footer {
    text-align: center;
    padding: 25px;
    color: #8794a7;
    font-size: 13px;
}

/* CELULARES */
@media(max-width: 700px) {
    main {
        width: 94%;
        margin: 20px auto;
    }

    .resumen {
        grid-template-columns: 1fr;
    }

    .datos {
        grid-template-columns: 1fr;
    }

    .bienvenida {
        padding: 23px;
    }

    .bienvenida h1 {
        font-size: 21px;
    }

    .avatar {
        width: 60px;
        height: 60px;
        min-width: 60px;
    }

    .panel {
        padding: 19px;
    }
}
</style>
</head>

<body>

<header>
    <div class="logo">Mesa de Examen <span>| Portal Estudiantil</span></div>
    <a href="cerrar_sesion.php" class="btn-salir">Cerrar sesión</a>
</header>

<main>

<?php if (isset($error)): ?>

    <section class="panel">
        <div class="aviso"><?= e($error) ?></div>
    </section>

<?php elseif (!$alumno): ?>

    <section class="panel">
        <div class="aviso">
            Tu cuenta todavía no está vinculada a un registro de alumno.
            Comunicate con Preceptoría.
        </div>
    </section>

<?php else: ?>

    <section class="bienvenida">
        <div>
            <h1>¡Hola, <?= e($alumno['NyA']) ?>!</h1>
            <p>Bienvenido a tu portal estudiantil. Consultá tus datos y mesas de examen.</p>
        </div>

        <div class="avatar"><?= e($iniciales) ?></div>
    </section>

    <section class="resumen">

        <div class="tarjeta">
            <small>Mesas disponibles</small>
            <strong><?= $totalMesas ?></strong>
        </div>

        <div class="tarjeta">
            <small>Mesas inscriptas</small>
            <strong><?= $totalInscriptas ?></strong>
        </div>

        <div class="tarjeta">
            <small>Sin inscripción</small>
            <strong><?= $totalPendientes ?></strong>
        </div>

    </section>

    <section class="panel">
        <h2>Mi perfil estudiantil</h2>
        <p class="descripcion">Información personal registrada en SIGEM.</p>

        <div class="datos">
            <div class="dato">
                <strong>Nombre y apellido</strong>
                <span><?= e($alumno['NyA']) ?></span>
            </div>

            <div class="dato">
                <strong>DNI</strong>
                <span><?= e($alumno['DNI']) ?></span>
            </div>

            <div class="dato">
                <strong>Correo electrónico</strong>
                <span><?= e($alumno['CORREO'] ?: 'No registrado') ?></span>
            </div>

            <div class="dato">
                <strong>Teléfono</strong>
                <span><?= e($alumno['TELEFONO'] ?: 'No registrado') ?></span>
            </div>

            <div class="dato">
                <strong>Dirección</strong>
                <span><?= e($alumno['DIRECCION'] ?: 'No registrada') ?></span>
            </div>
        </div>
    </section>

    <section class="panel">
        <h2>Mis mesas de examen</h2>
        <p class="descripcion">
            Consultá las fechas, aulas y el estado de tus inscripciones.
            Las inscripciones son gestionadas exclusivamente por Preceptoría.
        </p>

        <?php if (count($mesas) > 0): ?>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Fecha</th>
                            <th>Aula</th>
                            <th>Estado</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($mesas as $mesa): ?>
                        <tr>
                            <td><?= e($mesa['NOM_MATERIA'] ?: 'Sin materia asignada') ?></td>
                            <td><?= e(date('d/m/Y', strtotime($mesa['FECHA']))) ?></td>
                            <td><?= e($mesa['AULA'] ?: 'A confirmar') ?></td>
                            <td>
                                <?php if ($mesa['ID_INSCRIPCIONES']): ?>
                                    <span class="estado inscripto">Inscripto</span>
                                <?php else: ?>
                                    <span class="estado pendiente">Sin inscripción</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>

            <div class="vacio">
                Todavía no hay mesas de examen futuras para mostrar.
            </div>

        <?php endif; ?>
    </section>

<?php endif; ?>

</main>

<footer>
    Mesas de Examen· Sistema de Gestión Escolar
</footer>

</body>
</html>

