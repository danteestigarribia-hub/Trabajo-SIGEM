<?php
require_once __DIR__ . '/conexion.php';

function contarRegistros(PDO $pdo, string $tabla): int {
    try {
        return (int) $pdo->query(
            "SELECT COUNT(*) FROM $tabla"
        )->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

$totalAlumnos = contarRegistros($pdo, 'alumnos');
$totalDocentes = contarRegistros($pdo, 'docentes');
$totalMaterias = contarRegistros($pdo, 'materias');
$totalInscripciones = contarRegistros($pdo, 'inscripciones');

$mesas = [];

try {
    $sql = "SELECT
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
            LIMIT 5";

    $mesas = $pdo->query($sql)->fetchAll();
} catch (PDOException $e) {
    $mesas = [];
}

function e($valor): string {
    return htmlspecialchars(
        (string) ($valor ?? ''),
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#152d4a">
    <title>SIGEM | Panel principal</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="app-shell">

    <aside class="sidebar" id="sidebar">
        <a class="brand" href="index.php">
            <span class="brand-mark">S</span>
            <span class="brand-copy">
                <strong>SIGEM</strong>
                <small>Sistema de gestión académica</small>
            </span>
        </a>

        <div class="nav-caption">MENÚ PRINCIPAL</div>

        <nav class="main-nav" aria-label="Menú principal">
            <a class="nav-link active" href="index.php">
                <span class="nav-icon">▦</span>
                Panel principal
            </a>
            <a class="nav-link" href="#alumnos">
                <span class="nav-icon">♙</span>
                Alumnos
            </a>
            <a class="nav-link" href="#docentes">
                <span class="nav-icon">♧</span>
                Docentes
            </a>
            <a class="nav-link" href="#materias">
                <span class="nav-icon">▤</span>
                Materias
            </a>
            <a class="nav-link" href="#inscripciones">
                <span class="nav-icon">☷</span>
                Inscripciones
            </a>
            <a class="nav-link" href="#mesas">
                <span class="nav-icon">▣</span>
                Mesas de examen
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
        </div>
    </aside>

    <main class="main-area">

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
                <strong>Panel principal</strong>
            </div>

            <div class="topbar-right">
                <span class="today" id="todayLabel"></span>
                <div class="avatar">AD</div>
                <div class="user-label">
                    <strong>Administrador</strong>
                    <small>Gestión académica</small>
                </div>
            </div>
        </header>

        <div class="page-content">

            <section class="welcome-row">
                <div>
                    <p class="eyebrow">RESUMEN INSTITUCIONAL</p>
                    <h1>Panel principal</h1>
                    <p class="welcome-text">
                        Bienvenido a SIGEM. Este es el resumen general
                        de la gestión académica.
                    </p>
                </div>

                <div class="welcome-symbol" aria-hidden="true">
                    <span>▦</span>
                    <i></i>
                    <i></i>
                    <i></i>
                </div>
            </section>

            <section class="stats-grid" aria-label="Resumen de registros">

                <article class="stat-card" id="alumnos">
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

                <article class="stat-card" id="docentes">
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

                <article class="stat-card" id="materias">
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

                <article class="stat-card" id="inscripciones">
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

                <aside class="panel quick-panel">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">ACCESOS DIRECTOS</p>
                            <h2>Acciones rápidas</h2>
                        </div>
                    </div>

                    <a class="quick-action" href="#alumnos">
                        <span class="quick-icon blue">＋</span>
                        <span>
                            <strong>Gestión de alumnos</strong>
                            <small>Consultar el módulo de alumnos</small>
                        </span>
                        <b>›</b>
                    </a>

                    <a class="quick-action" href="#docentes">
                        <span class="quick-icon violet">＋</span>
                        <span>
                            <strong>Gestión de docentes</strong>
                            <small>Consultar el módulo de docentes</small>
                        </span>
                        <b>›</b>
                    </a>

                    <a class="quick-action" href="#materias">
                        <span class="quick-icon orange">＋</span>
                        <span>
                            <strong>Materias</strong>
                            <small>Ver resumen de materias</small>
                        </span>
                        <b>›</b>
                    </a>

                    <a class="quick-action" href="#mesas">
                        <span class="quick-icon green">＋</span>
                        <span>
                            <strong>Mesas de examen</strong>
                            <small>Revisar próximas fechas</small>
                        </span>
                        <b>›</b>
                    </a>

                    <div class="quick-note">
                        <span>✦</span>
                        <p>
                            Los accesos son visuales en esta versión.
                            Los formularios de alta, edición y eliminación
                            se desarrollarán en los siguientes módulos.
                        </p>
                    </div>
                </aside>

            </section>

            <footer class="page-footer">
                <span>© <?= date('Y') ?> SIGEM · Sistema de Gestión Académica</span>
                <span>Interfaz inicial <b>•</b> PHP + MySQL</span>
            </footer>

        </div>
    </main>
</div>

<div class="mobile-overlay" id="mobileOverlay"></div>

<script>
const menuToggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('mobileOverlay');

function closeMenu() {
    sidebar.classList.remove('sidebar-open');
    overlay.classList.remove('show');
    menuToggle.setAttribute('aria-expanded', 'false');
}

menuToggle.addEventListener('click', () => {
    const open = sidebar.classList.toggle('sidebar-open');
    overlay.classList.toggle('show', open);
    menuToggle.setAttribute('aria-expanded', String(open));
});

overlay.addEventListener('click', closeMenu);

document.querySelectorAll('.main-nav a').forEach(link => {
    link.addEventListener('click', closeMenu);
});

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