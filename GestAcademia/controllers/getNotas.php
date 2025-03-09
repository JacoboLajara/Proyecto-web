<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/conexion.php';

$idAlumno = $_GET['idAlumno'] ?? null;
$idCurso = $_GET['idCurso'] ?? null;

if (!$idAlumno || !$idCurso) {
    echo json_encode(["success" => false, "message" => "Faltan parámetros idAlumno o idCurso"]);
    exit;
}

// Verificar conexión a la BD
if (!$conn) {
    echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos"]);
    exit;
}

// 🔹 Obtener notas por Curso, Módulo y Unidad Formativa
$sql = "
    SELECT 
        n.ID_Nota,
        n.ID_Alumno,
        n.ID_Curso,
        n.ID_Modulo,
        n.ID_Unidad_Formativa,
        n.Tipo_Nota,
        n.Calificación,
        c.Nombre AS Curso_Nombre,
        m.Nombre AS Modulo_Nombre,
        uf.Nombre AS Unidad_Formativa_Nombre
    FROM Nota n
    LEFT JOIN Curso c ON n.ID_Curso = c.ID_Curso
    LEFT JOIN Modulo m ON n.ID_Modulo = m.ID_Modulo
    LEFT JOIN Unidad_Formativa uf ON n.ID_Unidad_Formativa = uf.ID_Unidad_Formativa
    WHERE n.ID_Alumno = ? AND n.ID_Curso = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Error en la consulta SQL"]);
    exit;
}

$stmt->bind_param("ss", $idAlumno, $idCurso);
$stmt->execute();
$result = $stmt->get_result();
$notas = [];

while ($row = $result->fetch_assoc()) {
    $notas[] = [
        "ID_Nota" => $row["ID_Nota"],
        "ID_Alumno" => $row["ID_Alumno"],
        "ID_Curso" => $row["ID_Curso"],
        "ID_Modulo" => $row["ID_Modulo"] ?? null,
        "ID_Unidad_Formativa" => $row["ID_Unidad_Formativa"] ?? null,
        "Tipo_Nota" => $row["Tipo_Nota"],
        "Calificación" => $row["Calificación"],
        "Curso_Nombre" => $row["Curso_Nombre"],
        "Modulo_Nombre" => $row["Modulo_Nombre"] ?? "-",
        "Unidad_Formativa_Nombre" => $row["Unidad_Formativa_Nombre"] ?? "-"
    ];
}

echo json_encode($notas);
$conn->close();
