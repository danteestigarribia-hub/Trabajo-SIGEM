
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function protegerPanel(string $rolPermitido): void {
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: login.php');
        exit;
    }

    if ($_SESSION['rol'] !== $rolPermitido) {
        header('Location: login.php');
        exit;
    }
}
