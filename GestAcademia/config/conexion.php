<?php
$servername = "localhost"; // Servidor (usualmente localhost)
$username = "root";        // Usuario de MySQL
$password = "root";        // Contraseña
$dbname = "centro_formacion"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");


// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    file_put_contents('debug.log', "Conexión exitosa\n", FILE_APPEND);
}

// No generar salida adicional
return $conn;