<?php

$idCurso = $_GET['idCurso'] ?? null;
// listadoMatriculas.php

// Incluir TCPDF y el modelo de alumnos
require_once __DIR__ . '/../libs/tcpdf/tcpdf.php';
require_once __DIR__ . '/../models/MatriculasModel.php';

// Crear una instancia del modelo
$model = new MatriculasModel();

// Obtener todos los alumnos (sin filtrar)
$alumnos = $model->getAlumnosMatriculadosPorCurso($idCurso); // Asegúrate de que este método ejecute: SELECT * FROM Alumno

// Crear un nuevo documento PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Centro de Formación');
$pdf->SetTitle('Listado de Alumnos por curso');
$pdf->SetSubject('Listado de Alumnos por Curso');

// Establecer márgenes y configuración
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage('P');

// Configurar título del PDF
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Listado de Alumnos curso '.$idCurso, 0, 1, 'C');
$pdf->Ln(5);

// Configurar la fuente para el listado
$pdf->SetFont('helvetica', '', 12);

if (!empty($alumnos)) {
    // Encabezados de la tabla
    $pdf->SetFillColor(200, 220, 255);
    $pdf->Cell(25, 10, "ID_Alumno", 1, 0, 'C', true);
    $pdf->Cell(40, 10, "Nombre", 1, 0, 'C', true);
    $pdf->Cell(40, 10, "Apellido 1", 1, 0, 'C', true);
    $pdf->Cell(40, 10, "Apellido 2", 1, 0, 'C', true);
    $pdf->Ln();

    // Recorrer el listado de alumnos
    foreach ($alumnos as $alumno) {
        $pdf->Cell(25, 8, $alumno['ID_Alumno'], 1, 0, 'C');
        $pdf->Cell(40, 8, $alumno['Nombre'], 1, 0, 'L');
        $pdf->Cell(40, 8, $alumno['Apellido1'], 1, 0, 'L');
        $pdf->Cell(40, 8, $alumno['Apellido2'], 1, 0, 'L');
        $pdf->Ln();
       
    }
} else {
    $pdf->Cell(0, 10, "No se encontraron alumnos.", 0, 1, 'L');
}

// Salida del PDF: "I" para mostrar en el navegador
$pdf->Output('ListadoAlumnos.pdf', 'I');
?>
