


<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/verificar_sesion.php';

protegerPanel('preceptor');

$nombrePreceptor = $_SESSION['usuario'] ?? 'Preceptor';

// Token de seguridad.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Funciones auxiliares.
function e($valor): string {
    return htmlspecialchars(
        (string)($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

function fechaBonita(?string $fecha): string {
    if (!$fecha) {
        return 'Sin fecha';
    }

    $ts = strtotime($fecha);
    return $ts ? date('d/m/Y', $ts) : e($fecha);
}

function contarRegistros(PDO $pdo, string $tabla): int {
    $tablasPermitidas = [
        'alumnos',
        'docentes',
        'materias',
        'inscripciones'
    ];

    if (!in_array($tabla, $tablasPermitidas, true)) {
        return 0;
    }

    try {
        return (int)$pdo->query(
            "SELECT COUNT(*) FROM `$tabla`"
        )->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

// Listar alumnos disponibles.
$alumnosDisponibles = $pdo->query("
    SELECT ID_ALUMNOS, NyA, DNI
    FROM alumnos
    ORDER BY NyA ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Listar mesas futuras.
$mesasDisponibles = $pdo->query("
    SELECT
        me.ID_MESAS,
        me.FECHA,
        me.AULA,
        ma.NOM_MATERIA
    FROM mesas me
    LEFT JOIN materias ma
        ON me.ID_MATERIA = ma.ID_MATERIAS
    WHERE me.FECHA >= CURDATE()
    ORDER BY me.FECHA ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Listar alumnos inscriptos.
$inscriptos = $pdo->query("
    SELECT
        a.NyA,
        a.DNI,
        ma.NOM_MATERIA,
        me.FECHA,
        me.AULA,
        i.FECHA AS FECHA_INSCRIPCION
    FROM inscripciones i
    INNER JOIN alumnos a
        ON i.ID_ALUMNOS = a.ID_ALUMNOS
    INNER JOIN mesas me
        ON i.ID_MESAS = me.ID_MESAS
    INNER JOIN materias ma
        ON me.ID_MATERIA = ma.ID_MATERIAS
    ORDER BY me.FECHA ASC, a.NyA ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas.
$totalAlumnos = contarRegistros($pdo, 'alumnos');
$totalDocentes = contarRegistros($pdo, 'docentes');
$totalMaterias = contarRegistros($pdo, 'materias');
$totalInscripciones = contarRegistros($pdo, 'inscripciones');

// Próximas cinco mesas.
$mesas = [];

try {
    $sql = "
        SELECT
            me.FECHA,
            me.AULA,
            ma.NOM_MATERIA,
            d.NyA AS DOCENTE
        FROM mesas me
        LEFT JOIN materias ma
            ON me.ID_MATERIA = ma.ID_MATERIAS
        LEFT JOIN docentes d
            ON me.ID_DOCENTE = d.ID_DOCENTES
        WHERE me.FECHA >= CURDATE()
        ORDER BY me.FECHA ASC
        LIMIT 5
    ";

    $mesas = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $mesas = [];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#152d4a">

    <title>Mesas de Examen | Panel de Preceptor</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        html {
            scroll-behavior: smooth;
        }

        section[id],
        article[id] {
            scroll-margin-top: 25px;
        }

        /* El contenido utiliza todo el ancho disponible. */
        .content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            width: 100%;
        }

        .exam-panel {
            width: 100%;
            min-width: 0;
        }

        .main-nav .nav-link {
            text-decoration: none;
        }

        .form-inscripcion {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .campo-formulario label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #263d61;
        }

        .campo-formulario select {
            width: 100%;
            padding: 13px;
            border: 1px solid #dce3ee;
            border-radius: 8px;
            background: white;
            font-size: 14px;
        }

        .btn-inscribir {
            background: #173b78;
            color: white;
            border: none;
            padding: 13px 22px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            grid-column: 1 / -1;
        }

        .btn-inscribir:hover {
            background: #28569b;
        }

        .mensaje-exito {
            background: #d9f5e4;
            color: #176b39;
            padding: 13px;
            border-radius: 8px;
            margin-bottom: 16px;
        }

        .mensaje-error {
            background: #ffe0e0;
            color: #a32020;
            padding: 13px;
            border-radius: 8px;
            margin-bottom: 16px;
        }

        .seccion-inscriptos {
            margin-top: 25px;
        }

        .panel-formulario {
            margin-top: 25px;
        }

        .nav-link {
            scroll-margin-top: 20px;
        }

        @media(max-width: 650px) {
            .form-inscripcion {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="app-shell">

    <!-- MENÚ LATERAL -->
    <aside class="sidebar" id="sidebar">

        <a class="brand" href="#inicio">
            <span class="brand-mark">M</span>

            <span class="brand-copy">
                <strong>Mesas de Examen</strong>
                <small>Gestión académica</small>
            </span>
        </a>

        <div class="nav-caption">MENÚ PRINCIPAL</div>

        <nav class="main-nav" aria-label="Menú principal">

            <a class="nav-link active" href="#inicio">
                <span class="nav-icon">⌂</span>
                Inicio
            </a>

            <a class="nav-link" href="#mesas">
                <span class="nav-icon">▣</span>
                Mesas de examen
            </a>

            <a class="nav-link" href="#gestionar-inscripciones">
                <span class="nav-icon">☷</span>
                Inscripciones
            </a>

            <a class="nav-link" href="#alumnos-inscriptos">
                <span class="nav-icon">♟</span>
                Alumnos inscriptos
            </a>

        </nav>
        

        <div class="sidebar-bottom">

            <div class="help-card">
                <span class="help-icon">i</span>

                <div>
                    <strong>Panel académico</strong>
                    <p>Administración de información institucional.</p>
                </div>
            </div>

            <div class="sidebar-footer">
                <span class="status-dot"></span>
                Sistema local
                <span class="version">v1.0</span>
            </div>

            <a href="cerrar_sesion.php" class="nav-link">
                <span class="nav-icon">↪</span>
                Cerrar sesión
            </a>

        </div>

    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-area">

        <!-- BARRA SUPERIOR -->
        <header class="topbar">

            <button
                class="menu-toggle"
                id="menuToggle"
                aria-label="Abrir menú"
                aria-expanded="false"
            >☰</button>

            <div class="breadcrumb">
                <span>Inicio</span>
                <span class="crumb-separator">/</span>
                <strong>Panel de Preceptor</strong>
            </div>

            <div class="topbar-right">

                <span class="today" id="todayLabel"></span>

                <div class="avatar">P</div>

                <div class="user-label">
                    <strong><?= e($nombrePreceptor) ?></strong>
                    <small>Preceptor</small>
                </div>

            </div>

        </header>

        <div class="page-content">

            <!-- INICIO -->
            <section class="welcome-row" id="inicio">

                <div>
                    <p class="eyebrow">RESUMEN INSTITUCIONAL</p>

                    <h1>Panel de Preceptor</h1>

                    <p class="welcome-text">
                        Bienvenido, <?= e($nombrePreceptor) ?>.
                        Desde aquí podés gestionar las mesas de examen
                        y las inscripciones de los alumnos.
                    </p>
                </div>

                <div class="welcome-symbol" aria-hidden="true">
                    <span>▦</span>
                    <i></i>
                    <i></i>
                    <i></i>
                </div>

            </section>

            <!-- ESTADÍSTICAS -->
            <section class="stats-grid" aria-label="Resumen de registros">

                <article class="stat-card">

                    <div class="stat-top">
                        <span class="stat-icon blue">♙</span>
                        <span class="stat-tag">REGISTROS</span>
                    </div>

                    <p class="stat-label">Total de alumnos</p>

                    <div class="stat-bottom">
                        <strong><?= number_format($totalAlumnos, 0, ',', '.') ?></strong>
                        <span class="stat-note">en el sistema</span>
                    </div>

                    <div class="stat-line blue-line"></div>

                </article>

                <article class="stat-card">

                    <div class="stat-top">
                        <span class="stat-icon violet">♧</span>
                        <span class="stat-tag">REGISTROS</span>
                    </div>

                    <p class="stat-label">Total de docentes</p>

                    <div class="stat-bottom">
                        <strong><?= number_format($totalDocentes, 0, ',', '.') ?></strong>
                        <span class="stat-note">en el sistema</span>
                    </div>

                    <div class="stat-line violet-line"></div>

                </article>

                <article class="stat-card">

                    <div class="stat-top">
                        <span class="stat-icon orange">▤</span>
                        <span class="stat-tag">REGISTROS</span>
                    </div>

                    <p class="stat-label">Materias cargadas</p>

                    <div class="stat-bottom">
                        <strong><?= number_format($totalMaterias, 0, ',', '.') ?></strong>
                        <span class="stat-note">en el sistema</span>
                    </div>

                    <div class="stat-line orange-line"></div>

                </article>

                <article class="stat-card">

                    <div class="stat-top">
                        <span class="stat-icon green">☷</span>
                        <span class="stat-tag">REGISTROS</span>
                    </div>

                    <p class="stat-label">Inscripciones</p>

                    <div class="stat-bottom">
                        <strong><?= number_format($totalInscripciones, 0, ',', '.') ?></strong>
                        <span class="stat-note">registradas</span>
                    </div>

                    <div class="stat-line green-line"></div>

                </article>

            </section>

            <!-- PRÓXIMAS MESAS -->
            <section class="content-grid">

                <article class="panel exam-panel" id="mesas">

                    <div class="panel-heading">

                        <div>
                            <p class="eyebrow">CALENDARIO ACADÉMICO</p>
                            <h2>Próximas mesas de examen</h2>
                        </div>

                        <span class="panel-icon">▣</span>

                    </div>

                    <?php if ($mesas): ?>

                        <div class="table-wrap">

                            <table>
                                <thead>
                                    <tr>
                                        <th>FECHA</th>
                                        <th>MATERIA</th>
                                        <th>DOCENTE</th>
                                        <th>AULA</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php foreach ($mesas as $mesa): ?>

                                        <tr>
                                            <td>
                                                <span class="date-pill">
                                                    <?= e(fechaBonita($mesa['FECHA'])) ?>
                                                </span>
                                            </td>

                                            <td class="subject-cell">
                                                <?= e($mesa['NOM_MATERIA'] ?: 'Materia sin asignar') ?>
                                            </td>

                                            <td>
                                                <?= e($mesa['DOCENTE'] ?: 'Sin asignar') ?>
                                            </td>

                                            <td>
                                                <?= e($mesa['AULA'] ?: 'A confirmar') ?>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>
                            </table>

                        </div>

                    <?php else: ?>

                        <div class="empty-state">
                            <span class="empty-icon">▣</span>
                            <strong>No hay mesas próximas para mostrar</strong>
                            <p>
                                Cuando cargues mesas de examen con fecha
                                actual o futura, aparecerán en esta sección.
                            </p>
                        </div>

                    <?php endif; ?>

                    <div class="panel-foot">
                        <span class="status-dot"></span>
                        Información consultada desde la base de datos
                    </div>

                </article>

            </section>

            <!-- FORMULARIO DE INSCRIPCIÓN -->
            <section
                class="panel panel-formulario"
                id="gestionar-inscripciones"
            >

                <div class="panel-heading">

                    <div>
                        <p class="eyebrow">GESTIÓN ACADÉMICA</p>
                        <h2>Inscribir alumno a una mesa</h2>
                    </div>

                    <span class="panel-icon">☷</span>

                </div>

                <?php if (isset($_GET['mensaje'])): ?>

                    <div class="mensaje-exito">
                        <?= e($_GET['mensaje']) ?>
                    </div>

                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>

                    <div class="mensaje-error">
                        <?= e($_GET['error']) ?>
                    </div>

                <?php endif; ?>

                <?php if (
                    count($alumnosDisponibles) > 0 &&
                    count($mesasDisponibles) > 0
                ): ?>

                    <form
                        action="inscribirse_mesa.php"
                        method="POST"
                        class="form-inscripcion"
                    >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= e($_SESSION['csrf_token']) ?>"
                        >

                        <div class="campo-formulario">

                            <label for="id_alumno">
                                Seleccionar alumno
                            </label>

                            <select
                                name="id_alumno"
                                id="id_alumno"
                                required
                            >

                                <option value="">
                                    Elegí un alumno
                                </option>

                                <?php foreach ($alumnosDisponibles as $alumno): ?>

                                    <option value="<?= (int)$alumno['ID_ALUMNOS'] ?>">
                                        <?= e($alumno['NyA']) ?>
                                        - DNI: <?= e($alumno['DNI']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="campo-formulario">

                            <label for="id_mesa">
                                Seleccionar mesa de examen
                            </label>

                            <select
                                name="id_mesa"
                                id="id_mesa"
                                required
                            >

                                <option value="">
                                    Elegí una mesa
                                </option>

                                <?php foreach ($mesasDisponibles as $mesa): ?>

                                    <option value="<?= (int)$mesa['ID_MESAS'] ?>">
                                        <?= e($mesa['NOM_MATERIA'] ?: 'Sin materia') ?>
                                        -
                                        <?= e(fechaBonita($mesa['FECHA'])) ?>
                                        -
                                        Aula: <?= e($mesa['AULA'] ?: 'A confirmar') ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <button type="submit" class="btn-inscribir">
                            Inscribir alumno
                        </button>

                    </form>

                <?php else: ?>

                    <div class="empty-state">
                        <strong>No hay datos disponibles</strong>
                        <p>
                            Verificá que existan alumnos registrados
                            y mesas de examen futuras.
                        </p>
                    </div>

                <?php endif; ?>

            </section>

            <!-- TABLA DE ALUMNOS INSCRIPTOS -->
            <section
                class="panel seccion-inscriptos"
                id="alumnos-inscriptos"
            >

                <div class="panel-heading">

                    <div>
                        <p class="eyebrow">GESTIÓN ACADÉMICA</p>
                        <h2>Alumnos inscriptos</h2>
                    </div>

                    <span class="panel-icon">♟</span>

                </div>

                <?php if (count($inscriptos) > 0): ?>

                    <div class="table-wrap">

                        <table>

                            <thead>
                                <tr>
                                    <th>ALUMNO</th>
                                    <th>DNI</th>
                                    <th>MATERIA</th>
                                    <th>FECHA DE MESA</th>
                                    <th>AULA</th>
                                    <th>FECHA DE INSCRIPCIÓN</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($inscriptos as $fila): ?>

                                    <tr>

                                        <td class="subject-cell">
                                            <?= e($fila['NyA']) ?>
                                        </td>

                                        <td>
                                            <?= e($fila['DNI']) ?>
                                        </td>

                                        <td>
                                            <?= e($fila['NOM_MATERIA']) ?>
                                        </td>

                                        <td>
                                            <?= e(fechaBonita($fila['FECHA'])) ?>
                                        </td>

                                        <td>
                                            <?= e($fila['AULA'] ?: 'A confirmar') ?>
                                        </td>

                                        <td>
                                            <?= e(fechaBonita($fila['FECHA_INSCRIPCION'])) ?>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div class="empty-state">

                        <span class="empty-icon">♟</span>

                        <strong>Todavía no hay alumnos inscriptos</strong>

                        <p>
                            Las inscripciones realizadas aparecerán
                            automáticamente en esta sección.
                        </p>

                    </div>

                <?php endif; ?>

            </section>

            <!-- PIE DE PÁGINA -->
            <footer class="page-footer">

                <span>
                    © <?= date('Y') ?> Mesas de Examen
                </span>

                <span>
                    Sistema de Gestión Académica
                </span>

            </footer>

        </div>

    </main>

</div>

<div class="mobile-overlay" id="mobileOverlay"></div>

<!-- JAVASCRIPT -->
<script>

const menuToggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('mobileOverlay');

// Cerrar menú móvil.
function closeMenu() {
    sidebar.classList.remove('sidebar-open');
    overlay.classList.remove('show');
    menuToggle.setAttribute('aria-expanded', 'false');
}

// Abrir menú.
menuToggle.addEventListener('click', () => {

    const open = sidebar.classList.toggle('sidebar-open');

    overlay.classList.toggle('show', open);

    menuToggle.setAttribute(
        'aria-expanded',
        String(open)
    );

});

// Cerrar al tocar fuera del menú.
overlay.addEventListener('click', closeMenu);

// Actualizar enlace activo y cerrar menú móvil.
document.querySelectorAll('.main-nav a[href^="#"]').forEach(link => {

    link.addEventListener('click', () => {

        document.querySelectorAll('.main-nav .nav-link').forEach(item => {
            item.classList.remove('active');
        });

        link.classList.add('active');

        closeMenu();

    });

});

// Fecha actual.
const today = new Date();

document.getElementById('todayLabel').textContent =
    new Intl.DateTimeFormat('es-AR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    }).format(today);

</script>

</body>
</html>
