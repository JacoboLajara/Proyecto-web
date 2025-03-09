<?php
// listadoRecibos.php
// Forzar la salida de errores en el log para depuración
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/debug.log');
error_reporting(E_ALL);

// Depuración: registrar los parámetros recibidos
error_log("Parámetros recibidos: " . print_r($_GET, true));

// Recuperar parámetros de la URL
$idCurso   = $_GET['curso']   ?? null;
$apellido1 = $_GET['apellido1'] ?? '';
$anio      = $_GET['anio']    ?? '';
$mes       = $_GET['mes']     ?? '';
$pendientes= isset($_GET['pendientes']) && $_GET['pendientes'] == '1';

// Depuración: mostrar los parámetros recibidos
error_log("Parametros recibidos: " . print_r($_GET, true));
// Incluir TCPDF y el modelo
require_once __DIR__ . '/../libs/tcpdf/tcpdf.php';
require_once __DIR__ . '/../models/RecibosModel.php';

// Crear instancia del modelo RecibosModel
$model = new RecibosModel();

// Obtener los recibos filtrados
$recibos = $model->getRecibos($apellido1, $anio, $mes, $idCurso, $pendientes);

// Limpiar el buffer de salida para evitar errores con TCPDF
if (ob_get_length()) {
    ob_clean();
}

// Crear el PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Centro de Formación');
$pdf->SetTitle('Listado de Recibos');
$pdf->SetSubject('Listado de Recibos');

// Configurar márgenes y página (por ejemplo, landscape)
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage('L');

// Título del PDF
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Listado de Recibos - Curso ' . $idCurso, 0, 1, 'C');
$pdf->Ln(5);

// Encabezados de la tabla
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(20, 10, 'ID', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Alumno', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Curso', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Importe', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'F.Emisión', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'F.Pago', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Periodo', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Estado', 1, 1, 'C', true);

// Datos de cada recibo
$pdf->SetFont('helvetica', '', 10);
if (!empty($recibos)) {
    foreach ($recibos as $recibo) {
        $pdf->Cell(20, 8, $recibo['ID_Recibo'], 1, 0, 'C');
        // Concatenar nombre del alumno (ajusta los índices según tu consulta)
        $alumnoNombre = $recibo['Nombre'] . " " . $recibo['Apellido1'] . " " . $recibo['Apellido2'];
        $pdf->Cell(40, 8, $alumnoNombre, 1, 0, 'L');
        $pdf->Cell(40, 8, $recibo['Curso'], 1, 0, 'L');
        $pdf->Cell(30, 8, $recibo['Importe'], 1, 0, 'C');
        $pdf->Cell(30, 8, $recibo['Fecha_Emision'], 1, 0, 'C');
        $pdf->Cell(30, 8, $recibo['Fecha_Pago'], 1, 0, 'C');
        $pdf->Cell(30, 8, $recibo['Periodo'], 1, 0, 'C');
        $pdf->Cell(30, 8, $recibo['Estado'], 1, 1, 'C');
    }
} else {
    $pdf->Cell(0, 10, "No se encontraron recibos.", 1, 1, 'C');
}

// Salida del PDF
$pdf->Output('ListadoRecibos.pdf', 'I');
exit;
