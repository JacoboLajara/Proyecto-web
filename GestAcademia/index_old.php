<?php
session_start();
require './config/conexion.php';

// Verificar si la base de datos está vacía
$result = $conn->query("SELECT COUNT(*) as total FROM Rol");
$row = $result->fetch_assoc();

if ($row['total'] == 0) {
    header("Location: setup.php");
    exit();
}

// Verificar si el usuario '00000000A' existe y aún no tiene contraseña
$sql = "SELECT DNI_NIE, password FROM Usuario WHERE DNI_NIE = '00000000A'";
$result = $conn->query($sql);
$admin = $result->fetch_assoc();

if ($admin && $admin['password'] === NULL) {
    header("Location: cambiar_admin.php");
    exit();
}

// Si el usuario ya ha cambiado sus credenciales, redirigir al login
header("Location: login.php");
exit();
?>
