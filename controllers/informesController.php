<?php

require_once 'conexion.php';
require_once 'Registro.php';

$conn = conectar();
$registro = new Registro($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_informe = $_POST['tipo_informe'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    // Generar informe según el tipo seleccionado
    if ($tipo_informe === 'diario') {
        $registros = $registro->obtenerRegistrosPorFecha($fecha_inicio);
    } elseif ($tipo_informe === 'semanal') {
        $registros = $registro->obtenerRegistrosPorRangoFechas($fecha_inicio, $fecha_fin);
    } elseif ($tipo_informe === 'mensual') {
        $registros = $registro->obtenerRegistrosPorRangoFechas($fecha_inicio, $fecha_fin);
    }

    // Mostrar resultados en la vista (puedes adaptar esto según tus necesidades)
    echo json_encode($registros);
}

?>