<?php

// Recuperar los parámetros de la URL
$idCurso = $_GET['idCurso'] ?? null;
$idAlumno = $_GET['idAlumno'] ?? null;
$NotaMedia = 0;
$media=0;
$count=0;
// listadoMatriculas.php

// Incluir TCPDF y el modelo de alumnos
require_once __DIR__ . '/../libs/tcpdf/tcpdf.php';
require_once __DIR__ . '/../models/NotasModel.php';

// Crear una instancia del modelo
$model = new NotasModel();

// Obtener todos los alumnos (sin filtrar)
$alumnos = $model->obtenerNotasPorAlumnoYCurso($idAlumno,$idCurso); // Asegúrate de que este método ejecute: SELECT * FROM Alumno

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
$pdf->Cell(0, 10, 'Notas del alumno '.$idAlumno, 0, 1, 'C');
$pdf->Ln(5);
// Suponiendo que $alumno es un arreglo con los datos del alumno
// Mostrar datos del alumno (tomamos la primera fila del listado)
if (!empty($alumnos)) {
    $primerAlumno = $alumnos[0];
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Nombre Alumno: ', 0, 1);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, $primerAlumno['AlumnoNombre'].' '.$primerAlumno['Apellido1'].' '.$primerAlumno['Apellido2'], 0, 1);
    $pdf->Ln(5);
}

// Configurar la fuente para el listado
$pdf->SetFont('helvetica', '', 10);

if (!empty($alumnos)) {
    // Encabezados de la tabla
    $pdf->SetFillColor(200, 220, 255);
    $pdf->Cell(25, 10, "ID_Modulo", 1, 0, 'C', true);
    $pdf->Cell(40, 10, "Modulo", 1, 0, 'C', true);
    $pdf->Cell(25, 10, "Cod. Unidad", 1, 0, 'C', true);
    $pdf->Cell(70, 10, "Unidad Formativa", 1, 0, 'C', true);
    $pdf->Cell(25, 10, "Nota", 1, 0, 'C', true);
    $pdf->Ln();

    // Recorrer el listado de alumnos
    foreach ($alumnos as $alumno) {
        $pdf->Cell(25, 8, $alumno['ID_Modulo'], 1, 0, 'C');
        $pdf->Cell(40, 8, $alumno['ModuloNombre'], 1, 0, 'L');
        $pdf->Cell(25, 8, $alumno['ID_Unidad_Formativa'], 1, 0, 'L');
        $pdf->Cell(70, 8, $alumno['UnidadFormativaNombre'], 1, 0, 'L');
        $pdf->Cell(25, 8, $alumno['Nota'], 1, 0, 'R');
        $pdf->Ln();
        $NotaMedia += $alumno['Nota'];
        $count ++;
    }
   
    $media = ($count > 0) ? $NotaMedia / $count : 0;
} else {
    $pdf->Cell(0, 10, "El alumno seleccionado no tiene calificaciones para este curso.", 0, 1, 'L');
}
$pdf->Cell(25, 8, 'Nota Media: ' , 1, 0, 'L');
$pdf->Cell(40, 8,number_format($media, 2, ',', ''), 1, 0, 'R');

// Salida del PDF: "I" para mostrar en el navegador
$pdf->Output('ListadoNotasAlumnoCurso.pdf', 'I');
?>
