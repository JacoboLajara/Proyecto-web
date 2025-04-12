<?php
require_once __DIR__ . '/../init.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'Personal_No_Docente') {
    die("Acceso denegado");
}

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$idUsuario = $_SESSION['usuario'] ?? null;
$inicio = $_GET['inicio'] ?? null;
$fin = $_GET['fin'] ?? null;

if (!$idUsuario || !$inicio || !$fin) {
    die("Error: Usuario o fechas no válidas");
}

$conn = include __DIR__ . '/../config/conexion.php';

// Obtener nombre completo y DNI
$sqlNombre = "SELECT Nombre, Apellido1, Apellido2 FROM personal_no_docente WHERE ID_Personal = ?";
$stmtNombre = $conn->prepare($sqlNombre);
$stmtNombre->bind_param("s", $idUsuario);
$stmtNombre->execute();
$stmtNombre->bind_result($nombre, $apellido1, $apellido2);
$stmtNombre->fetch();
$stmtNombre->close();
$nombreCompleto = trim("$nombre $apellido1 $apellido2");

// Obtener registros
$sql = "SELECT * FROM Registro_Horario WHERE ID_Usuario = ? AND Fecha BETWEEN ? AND ? ORDER BY Fecha";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $idUsuario, $inicio, $fin);
$stmt->execute();
$result = $stmt->get_result();

function calcularHoras($entrada, $salida)
{
    if (!$entrada || !$salida) return 0;
    list($h1, $m1) = explode(":", $entrada);
    list($h2, $m2) = explode(":", $salida);
    return ($h2 * 60 + $m2) - ($h1 * 60 + $m1);
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Registro Horario');

// Cabecera
$sheet->setCellValue('A1', 'Nombre completo:');
$sheet->setCellValue('B1', $nombreCompleto);
$sheet->setCellValue('A2', 'DNI/NIE:');
$sheet->setCellValue('B2', $idUsuario);
$sheet->setCellValue('A3', 'Periodo:');
$sheet->setCellValue('B3', "$inicio a $fin");

// Encabezados
$sheet->setCellValue('A5', 'Fecha');
$sheet->setCellValue('B5', 'Turno');
$sheet->setCellValue('C5', 'Entrada');
$sheet->setCellValue('D5', 'Salida');
$sheet->setCellValue('E5', 'Horas trabajadas');

$fila = 6;
$totalMinutos = 0;

while ($row = $result->fetch_assoc()) {
    $fecha = $row['Fecha'];
    $turnos = [
        ['Mañana', $row['Hora_Entrada_Manana'], $row['Hora_Salida_Manana']],
        ['Tarde', $row['Hora_Entrada_Tarde'], $row['Hora_Salida_Tarde']]
    ];

    foreach ($turnos as [$turno, $entrada, $salida]) {
        $horasTrabajadas = calcularHoras($entrada, $salida);
        $totalMinutos += $horasTrabajadas;

        $sheet->setCellValue("A$fila", $fecha);
        $sheet->setCellValue("B$fila", $turno);
        $sheet->setCellValue("C$fila", $entrada ?: '--:--');
        $sheet->setCellValue("D$fila", $salida ?: '--:--');
        $sheet->setCellValue("E$fila", $horasTrabajadas > 0 ? sprintf('%02d:%02d', floor($horasTrabajadas / 60), $horasTrabajadas % 60) : '--:--');
        $fila++;
    }
}

// Total horas
$sheet->setCellValue("D$fila", 'TOTAL');
$sheet->setCellValue("E$fila", sprintf('%02d:%02d', floor($totalMinutos / 60), $totalMinutos % 60));

// Formato Excel
ob_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=registro_horario_{$idUsuario}.xlsx");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
