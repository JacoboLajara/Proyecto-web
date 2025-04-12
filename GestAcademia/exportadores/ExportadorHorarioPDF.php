<?php
require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../libs/tcpdf/tcpdf.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'Personal_No_Docente') {
    die("Acceso denegado");
}

$idUsuario = $_SESSION['usuario'];
$nombreCompleto = $_SESSION['nombre_completo'] ?? $idUsuario;

// Validar parámetros GET
$inicio = $_GET['inicio'] ?? null;
$fin = $_GET['fin'] ?? null;
if (!$inicio || !$fin) {
    die("Parámetros de fecha no válidos");
}

$conn = include __DIR__ . '/../config/conexion.php';
$sql = "SELECT * FROM Registro_Horario WHERE ID_Usuario = ? AND Fecha BETWEEN ? AND ? ORDER BY Fecha";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $idUsuario, $inicio, $fin);
$stmt->execute();
$result = $stmt->get_result();

// Crear PDF
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

$pdf->Write(0, "Nombre: $nombreCompleto", '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0, "DNI: $idUsuario", '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0, "Periodo: del $inicio al $fin", '', 0, 'L', true, 0, false, false, 0);

$html = '<table border="1" cellpadding="4">
<tr style="background-color:#cccccc;">
    <th>Fecha</th>
    <th>Entrada Mañana</th>
    <th>Salida Mañana</th>
    <th>Horas Mañana</th>
    <th>Entrada Tarde</th>
    <th>Salida Tarde</th>
    <th>Horas Tarde</th>
</tr>';

$totalMinutos = 0;

function calcularHoras($entrada, $salida, &$totalMinutos) {
    if (!$entrada || !$salida) return '--:--';
    $inicio = DateTime::createFromFormat('H:i:s', $entrada);
    $fin = DateTime::createFromFormat('H:i:s', $salida);
    if (!$inicio || !$fin) return '--:--';
    $diff = $inicio->diff($fin);
    $min = $diff->h * 60 + $diff->i;
    $totalMinutos += $min;
    return $diff->format('%H:%I');
}

while ($row = $result->fetch_assoc()) {
    $horasM = calcularHoras($row['Hora_Entrada_Manana'], $row['Hora_Salida_Manana'], $totalMinutos);
    $horasT = calcularHoras($row['Hora_Entrada_Tarde'], $row['Hora_Salida_Tarde'], $totalMinutos);

    $html .= '<tr>' .
        '<td>' . date('d/m/Y', strtotime($row['Fecha'])) . '</td>' .
        '<td>' . ($row['Hora_Entrada_Manana'] ?? '--:--') . '</td>' .
        '<td>' . ($row['Hora_Salida_Manana'] ?? '--:--') . '</td>' .
        '<td>' . $horasM . '</td>' .
        '<td>' . ($row['Hora_Entrada_Tarde'] ?? '--:--') . '</td>' .
        '<td>' . ($row['Hora_Salida_Tarde'] ?? '--:--') . '</td>' .
        '<td>' . $horasT . '</td>' .
        '</tr>';
}

$html .= '</table>';
$pdf->writeHTML($html, true, false, true, false, '');

// Mostrar total de horas al final
$horasTotales = floor($totalMinutos / 60);
$minTotales = $totalMinutos % 60;
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Write(0, "Total de horas trabajadas: " . sprintf("%02d:%02d", $horasTotales, $minTotales), '', 0, 'L', true, 0, false, false, 0);

$pdf->Output("registro_horario_{$idUsuario}.pdf", 'D');
