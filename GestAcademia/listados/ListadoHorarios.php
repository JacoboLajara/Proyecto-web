<?php
require_once __DIR__ . '/../libs/tcpdf/tcpdf.php';
require_once __DIR__ . '/../models/HorariosModel.php';

// Crear instancia del modelo
$model = new HorariosModel();
$aulas = $model->getAulas(); // Obtener todas las aulas

// Crear nuevo documento PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Centro de Formación');
$pdf->SetTitle('Listado de Aulas y Horarios');
$pdf->SetSubject('Horarios Ocupados');

// Establecer márgenes y configuración
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage("L");

// Configurar título del PDF
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Listado de Aulas y Horarios Ocupados', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 12);

// Recorrer las aulas y mostrar sus horarios ocupados
// Recorrer las aulas y mostrar sus horarios ocupados
foreach ($aulas as $aula) {
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, "Aula: " . $aula['Nombre'], 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);

    $horarios = $model->getHorariosOcupados($aula['ID_Aula']);

    if (!empty($horarios)) {
        // Crear tabla con los horarios de mañana y tarde
        $pdf->SetFillColor(200, 220, 255);
        $pdf->Cell(40, 10, "Día", 1, 0, 'C', true);
        $pdf->Cell(30, 10, "Mañana Inicio", 1, 0, 'C', true);
        $pdf->Cell(30, 10, "Mañana Fin", 1, 0, 'C', true);
        $pdf->Cell(30, 10, "Tarde Inicio", 1, 0, 'C', true);
        $pdf->Cell(30, 10, "Tarde Fin", 1, 0, 'C', true);
        $pdf->Cell(120, 10, "Curso", 1, 1, 'C', true);

        foreach ($horarios as $horario) {
            $pdf->Cell(40, 8, $horario['Dia'], 1, 0, 'C');
            $pdf->Cell(30, 8, ($horario['Hora_Inicio'] ?: "—"), 1, 0, 'C');
            $pdf->Cell(30, 8, ($horario['Hora_Fin'] ?: "—"), 1, 0, 'C');
            $pdf->Cell(30, 8, ($horario['Tarde_Inicio'] ?: "—"), 1, 0, 'C');
            $pdf->Cell(30, 8, ($horario['Tarde_Fin'] ?: "—"), 1, 0, 'C');
            $pdf->Cell(120, 8, $horario['Curso'], 1, 1, 'L');
        }
    } else {
        $pdf->Cell(0, 10, "No hay horarios ocupados.", 0, 1, 'L');
    }
    $pdf->Ln(5);
}


// Salida del PDF
$pdf->Output('Listado_Aulas_Horarios.pdf', 'I'); // Forzar descarga
?>
