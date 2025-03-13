<?php
require_once __DIR__ . '/../init.php';
include '../config/conexion.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['auth_token'])) {
    file_put_contents('debug.log', "DEBUG - Usuario no autenticado, redirigiendo a login\n", FILE_APPEND);
    header('Location: ../../login.php');
    exit();
}

$usuarioAutenticado = isset($_SESSION['usuario']);
$rol = $usuarioAutenticado ? $_SESSION['rol'] : null;

// Verificar conexión a la base de datos
if (!$conn) {
    die(json_encode(["error" => "No se pudo conectar a la base de datos."]));
}

$cursos = [];

// Si el usuario es profesor, obtener los cursos asociados
if ($rol === 'Profesor') {
    $idProfesor = $_SESSION['usuario'];

    // Preparar la consulta de manera segura
    $query = $conn->prepare("SELECT c.ID_Curso, c.Nombre, c.Tipo, c.Tipo_cuota, c.Duracion_Horas, c.Precio_Curso, 
                                    pc.Fecha_Matricula, pc.Estado
                              FROM profesor_curso pc
                              JOIN Curso c ON pc.ID_Curso = c.ID_Curso
                              WHERE pc.ID_Profesor = ?");
    
    // Verificar si la consulta se preparó correctamente
    if (!$query) {
        die(json_encode(["error" => "Error al preparar la consulta SQL: " . $conn->error]));
    }

    // Enlazar parámetro
    $query->bind_param("s", $idProfesor);

} else {
    // Si no es profesor, obtener todos los cursos
    $query = $conn->prepare("SELECT ID_Curso, Nombre FROM curso");

    if (!$query) {
        die(json_encode(["error" => "Error al preparar la consulta SQL: " . $conn->error]));
    }
}

// Ejecutar la consulta y verificar si hay resultados
if (!$query->execute()) {
    die(json_encode(["error" => "Error al ejecutar la consulta: " . $query->error]));
}

$result = $query->get_result();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cursos[] = $row;
    }
} else {
    die(json_encode(["error" => "No se encontraron cursos en la base de datos."]));
}

// Cerrar la consulta y la conexión
$query->close();
$conn->close();

// Devolver los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($cursos);
