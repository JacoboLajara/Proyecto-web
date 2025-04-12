<?php
require_once __DIR__ . '/../libs/tcpdf/tcpdf.php';
require_once __DIR__ . '/../models/CursosModel.php';

$model = new CursosModel();
$cursos = $model->getTodosLosCursos();

$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Centro de Formación');
$pdf->SetTitle('Listado de Cursos');
$pdf->SetSubject('Lista de todos los cursos');

$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage('L');

$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Listado de Cursos', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 12);

if (!empty($cursos)) {
    $pdf->SetFillColor(200, 220, 255);
    $pdf->Cell(40, 10, "ID Curso", 1, 0, 'C', true);
    $pdf->Cell(120, 10, "Nombre", 1, 0, 'C', true);
    $pdf->Cell(30, 10, "Duración", 1, 0, 'C', true);
    $pdf->Cell(30, 10, "Precio", 1, 0, 'C', true);
    $pdf->Ln();

    foreach ($cursos as $curso) {
        $pdf->Cell(40, 8, $curso['ID_Curso'], 1, 0, 'C');
        $pdf->Cell(120, 8, $curso['Nombre'], 1, 0, 'L');
        $pdf->Cell(30, 8, $curso['Duracion_Horas'] . "h", 1, 0, 'C');
        $pdf->Cell(30, 8, number_format($curso['Precio_curso'], 2) . " €", 1, 0, 'C');
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, "No se encontraron cursos.", 0, 1, 'L');
}

$pdf->Output('ListadoCursos.pdf', 'I');
?>
