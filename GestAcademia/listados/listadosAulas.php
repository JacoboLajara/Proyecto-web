<?php
// listadosAulas.php

// Incluir TCPDF y el modelo de Aulas
require_once __DIR__ . '/../libs/tcpdf/tcpdf.php';
require_once __DIR__ . '/../models/AulasModel.php';

// Crear una instancia del modelo
$model = new AulasModel();

// Obtener todos los alumnos (sin filtrar)
$aulas = $model->getAulas('all'); // Asegúrate de que este método ejecute: SELECT * FROM Alumno

// Crear un nuevo documento PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Centro de Formación');
$pdf->SetTitle('Listado de Aulas');
$pdf->SetSubject('Listado de Aulas');

// Establecer márgenes y configuración
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage('P');

// Configurar título del PDF
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Listado de Aulas', 0, 1, 'C');
$pdf->Ln(5);

// Configurar la fuente para el listado
$pdf->SetFont('helvetica', '', 12);

if (!empty($aulas)) {
    // Encabezados de la tabla
    $pdf->SetFillColor(200, 220, 255);
    $pdf->Cell(25, 10, "ID", 1, 0, 'C', true);
    $pdf->Cell(80, 10, "Nombre", 1, 0, 'C', true);
    $pdf->Cell(40, 10, "Capacidad", 1, 0, 'C', true);
    $pdf->Ln();
    

    // Recorrer el listado de aulas
    foreach ($aulas as $aulas) {
        $pdf->Cell(25, 8, $aulas['ID_Aula'], 1, 0, 'C');
        $pdf->Cell(80, 8, $aulas['Nombre'], 1, 0, 'L');
        $pdf->Cell(40, 8, $aulas['Capacidad'], 1, 0, 'L');
        $pdf->Ln();
        
    }
} else {
    $pdf->Cell(0, 10, "No se encontraron Aulas.", 0, 1, 'L');
}

// Salida del PDF: "I" para mostrar en el navegador
$pdf->Output('ListadoAulas.pdf', 'I');
?>
