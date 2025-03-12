<?php
session_start(); // Iniciar la sesión

// Eliminar todas las variables de sesión
$_SESSION = [];

// Destruir la sesión
session_destroy();

// Eliminar la cookie de sesión (opcional)
if (ini_get("session.use_cookies")) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Redirigir a la página de inicio de sesión
header("Location: login.php");
exit();

