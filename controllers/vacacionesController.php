<?php

require_once 'conexion.php';
require_once 'Vacaciones.php';

$conn = conectar();
$vacaciones = new Vacaciones($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_empleado = $_SESSION['id_empleado']; // Asumiendo que el ID del empleado está en la sesión
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    $vacaciones->crearVacaciones($id_empleado, $fecha_inicio, $fecha_fin);

    header('Location: vacaciones.php'); // Redirigir a la página de vacaciones
}

// ... (Lógica para aprobar o rechazar solicitudes de vacaciones)

?>