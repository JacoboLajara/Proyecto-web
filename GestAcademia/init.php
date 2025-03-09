<?php
// init.php
ini_set('session.save_path', realpath(__DIR__ . '/sesiones'));  // Verifica esta ruta
ini_set('session.cookie_path', '/');
session_start();

file_put_contents('debug.log', "DEBUG (init.php) - session_id(): " . session_id() . "\n", FILE_APPEND);

// Control de tiempo de inactividad (30 minutos)
$timeout = 1800;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
    session_unset();
    session_destroy();
    file_put_contents('debug.log', "DEBUG (init.php) - Sesión expirada\n", FILE_APPEND);
    header('Location: login.php');
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();



