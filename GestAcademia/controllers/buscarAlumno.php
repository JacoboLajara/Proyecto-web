
<?php
include '../config/conexion.php';

// Depurar los datos recibidos
$data = json_decode(file_get_contents('php://input'), true);
$criterio = $data['criterio'] ?? '';
file_put_contents('debug.log', "Solicitud recibida\n", FILE_APPEND);
file_put_contents('debug.log', "Criterio: $criterio\n", FILE_APPEND);

file_put_contents('debug.log', "Criterio de búsqueda: $criterio\n", FILE_APPEND);

// Preparar y ejecutar la consulta
$query = $conn->prepare("SELECT ID_Alumno, Nombre, Apellido1, Apellido2 FROM alumno WHERE Nombre LIKE ? OR Apellido1 LIKE ? OR Apellido2 LIKE ? OR ID_Alumno LIKE ?");
$query->bind_param("ssss", $criterio_param, $criterio_param, $criterio_param, $criterio_param);
$criterio_param = "%$criterio%";
$query->execute();

// Obtener resultados
$result = $query->get_result();
$resultados = [];
while ($row = $result->fetch_assoc()) {
    $resultados[] = $row;
}

// Enviar la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($resultados);
exit;
?>