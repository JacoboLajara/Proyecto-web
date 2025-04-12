<?php
include '../config/conexion.php';

// Obtener el ID desde la solicitud GET
$id = $_GET['id'] ?? 0;

// Preparar y ejecutar la consulta
$query = $conn->prepare("SELECT * FROM alumno WHERE ID_Alumno = ?");
$query->bind_param("s", $id); // Vincular el parámetro
$query->execute();

// Obtener el resultado
$result = $query->get_result();
$registro = $result->fetch_assoc(); // Obtener el resultado como un array asociativo

// Devolver los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($registro);