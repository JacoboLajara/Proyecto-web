<?php
require '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id_curso']) || !isset($data['alumnos'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$idCurso = trim($data['id_curso']);
$alumnos = $data['alumnos'];

//$conn = getConnection();

foreach ($alumnos as $idAlumno) {
    $stmt = $conn->prepare("INSERT INTO Alumno_Curso (ID_Alumno, ID_Curso, Fecha_Matricula, Estado) VALUES (?, ?, CURDATE(), 'Activo')");
    $stmt->bind_param("ss", $idAlumno, $idCurso);

    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Error al matricular al alumno ' . $idAlumno]);
        exit;
    }
}

echo json_encode(['success' => true, 'message' => 'Alumnos matriculados correctamente']);
$conn->close();
