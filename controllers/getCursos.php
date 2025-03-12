<?php
include '../config/conexion.php';
// Preparar y ejecutar la consulta
$query = $conn->prepare("SELECT ID_Curso, Nombre FROM curso");
$query->execute();
$result = $query->get_result();

$cursos = [];
while ($row = $result->fetch_assoc()) {
    $cursos[] = $row;
}

// Devolver los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($cursos);

// Cerrar la conexión
$query->close();
$conn->close();