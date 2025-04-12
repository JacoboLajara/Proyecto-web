<?php

require_once 'conexion.php';
require_once 'Registro.php';

$conn = conexion();
$registro = new Registro($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_empleado = $_SESSION['id_empleado']; // Asumiendo que el ID del empleado está en la sesión
    $fecha = date('Y-m-d');
    $entrada_manana = $_POST['entrada_manana'];
    $salida_manana = $_POST['salida_manana'];
    $entrada_tarde = $_POST['entrada_tarde'];
    $salida_tarde = $_POST['salida_tarde'];

    // Calcular horas trabajadas
    $horas_manana = calcularHoras($entrada_manana, $salida_manana);
    $horas_tarde = calcularHoras($entrada_tarde, $salida_tarde);
    $horas_trabajadas = $horas_manana + $horas_tarde;

    $registro->crearRegistro($id_empleado, $fecha, $entrada_manana, $salida_manana, $entrada_tarde, $salida_tarde, $horas_trabajadas);

    header('Location: registro.php'); // Redirigir a la página de registro
}

function calcularHoras($entrada, $salida) {
    if ($entrada && $salida) {
        $entrada_ts = strtotime($entrada);
        $salida_ts = strtotime($salida);
        $diff = $salida_ts - $entrada_ts;
        return round($diff / 3600, 2);
    }
    return 0;
}

?>