<?php
session_start();
require './config/conexion.php'; // Archivo de conexión a MySQL

// Verificar si el usuario es el administrador inicial
$sql = "SELECT DNI_NIE, password FROM Usuario WHERE DNI_NIE = '00000000A'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($row) {
    // Si el usuario sigue siendo '00000000A' o la contraseña es NULL, redirigirlo a cambiar usuario y contraseña
    if ($row['password'] === NULL) {
        header("Location: cambiar_admin.php");
        exit();
    }
}
?>