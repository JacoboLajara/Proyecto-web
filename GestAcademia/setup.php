<?php
include_once __DIR__ . '/config/conexion.php';

echo "<h2>Inicializando base de datos...</h2>";

// 1️⃣ Insertar roles si no existen
$conn->query("INSERT IGNORE INTO Rol (Nombre) VALUES 
    ('Administrador'), ('Personal_No_Docente'),('Profesor'), ('Alumno')");

// 2️⃣ Insertar usuario administrador si no existe
$conn->query("INSERT IGNORE INTO Usuario (DNI_NIE, password, Fecha_Creacion, ID_Rol) 
    VALUES ('00000000A', NULL, NOW(), (SELECT ID_Rol FROM Rol WHERE Nombre = 'Administrador'))");

// 3️⃣ Asignar el rol de Administrador al usuario inicial si no está asignado
$conn->query("INSERT IGNORE INTO Usuario_Rol (DNI_NIE, ID_Rol)
    VALUES (
        '00000000A',
        (SELECT ID_Rol FROM Rol WHERE Nombre = 'Administrador')
    )");

echo "<p>✅ Base de datos inicializada correctamente.</p>";
echo "<p><a href='mainpage.php'>Ir a la aplicación</a></p>";
?>