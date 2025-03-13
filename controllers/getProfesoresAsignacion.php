<?php
include '../config/conexion.php';

// Configurar el tipo de contenido como JSON
header('Content-Type: application/json');

// Obtener el ID desde la solicitud GET (si no se envía, se asume null)
$id = isset($_GET['id']) ? trim($_GET['id']) : null;

if ($id) {
    // Si se proporciona un ID, obtener solo ese alumno
    $query = $conn->prepare("SELECT * FROM profesor WHERE ID_Profesor = ?");
    $query->bind_param("s", $id);
    $query->execute();
    $result = $query->get_result();
    $registro = $result->fetch_assoc(); // Obtener un solo registro

    // Devolver el registro en formato JSON
    echo json_encode($registro, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
    // Si no se proporciona un ID, obtener todos los alumnos
    $query = $conn->prepare("SELECT * FROM profesor");
    $query->execute();
    $result = $query->get_result();

    $alumnos = [];
    while ($row = $result->fetch_assoc()) {
        $alumnos[] = $row;
    }

    // Devolver todos los registros en formato JSON
    echo json_encode($alumnos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

// Cerrar la conexión
$query->close();
$conn->close();
?>