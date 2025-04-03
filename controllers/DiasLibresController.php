<?php

require_once 'conexion.php';
require_once 'DiasLibres.php';

$conn = conectar();
$dias_libres = new DiasLibres($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_empleado = $_SESSION['id_empleado']; // Asumiendo que el ID del empleado está en la sesión
    $fecha = $_POST['fecha'];
    $motivo = $_POST['motivo'];

    $dias_libres->crearDiaLibre($id_empleado, $fecha, $motivo);

    header('Location: dias_libres.php'); // Redirigir a la página de días libres
}

// ... (Lógica para aprobar o rechazar solicitudes de días libres)

?>