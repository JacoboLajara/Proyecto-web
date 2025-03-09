<?php
require_once __DIR__ . '/../libs/tcpdf/tcpdf.php';
require_once __DIR__ . '/../models/AlumnosModel.php';

// Verificar si se pasó un ID de alumno válido
if (!isset($_GET['idAlumno']) || empty($_GET['idAlumno'])) {
    die("Error: ID de alumno no proporcionado.");
}

$idAlumno = $_GET['idAlumno'];

// Obtener datos del alumno desde el modelo
$model = new AlumnosModel();
$datos = $model->getAlumnoDetalleConCursos($idAlumno);

if (!$datos || !isset($datos['alumno'])) {
    die("Error: No se encontraron datos para el alumno con ID: $idAlumno");
}

// Crear PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Centro de Formación');
$pdf->SetTitle('Ficha del Alumno');
$pdf->SetSubject('Historial de Cursos');

// Configuración de márgenes y tipo de documento
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage();

// **Restablecer colores**
$pdf->SetFillColor(255, 255, 255); // Fondo blanco
$pdf->SetDrawColor(0, 0, 0); // Bordes negros
$pdf->SetTextColor(0, 0, 0); // Texto negro

// **Título del documento**
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Ficha del Alumno', 0, 1, 'C');
$pdf->Ln(5);

// **Datos del alumno**
$alumno = $datos['alumno'];
$pdf->SetFont('helvetica', '', 12);

// **Definir los campos a mostrar**
$campos = [
    "ID Alumno" => $alumno['ID_Alumno'] ?? '',
    "ID Usuario" => $alumno['ID_Usuario'] ?? '',
    "Nombre" => ($alumno['Nombre'] ?? '') . " " . ($alumno['Apellido1'] ?? '') . " " . ($alumno['Apellido2'] ?? ''),
    "Dirección" => $alumno['Direccion'] ?? '',
    "Población" => $alumno['Poblacion'] ?? '',
    "Provincia" => $alumno['Provincia'] ?? '',
    "Código Postal" => $alumno['Codigo_Postal'] ?? '',
    "Fecha de Nacimiento" => $alumno['Fecha_Nacimiento'] ?? '',
    "Nivel de Estudios" => $alumno['Nivel_Estudios'] ?? '',
    "Fecha de Alta" => $alumno['Fecha_Alta'] ?? '',
    "Fecha de Baja" => $alumno['Fecha_Baja'] ?? '',
    "Teléfono" => $alumno['Telefono'] ?? '',
    "Email" => $alumno['Email'] ?? ''
];

// **Imprimir cada campo con alternancia de colores**
$fill = false;
foreach ($campos as $titulo => $valor) {
    $pdf->SetFillColor($fill ? 230 : 255, $fill ? 230 : 255, 255); // Alternar colores
    $pdf->Cell(50, 8, $titulo . ":", 1, 0, 'L', true);
    $pdf->Cell(100, 8, htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'), 1, 1, 'L', true);
    $fill = !$fill;
}

$pdf->Ln(5);

// **Título del historial**
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Historial de Cursos', 0, 1, 'C');
$pdf->Ln(3);

// **Encabezado de la tabla de cursos**
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(50, 8, "Código Curso", 1, 0, 'C', true);
$pdf->Cell(100, 8, "Nombre Curso", 1, 1, 'C', true);
$pdf->SetFont('helvetica', '', 12);

// **Si el alumno tiene cursos, los listamos**
if (!empty($datos['historial'])) {
    $fill = false;
    foreach ($datos['historial'] as $curso) {
        $pdf->SetFillColor($fill ? 240 : 255, $fill ? 240 : 255, 255);
        $pdf->Cell(50, 8, htmlspecialchars($curso['ID_Curso'] ?? '', ENT_QUOTES, 'UTF-8'), 1, 0, 'C', true);
        $pdf->Cell(100, 8, htmlspecialchars($curso['Nombre'] ?? '', ENT_QUOTES, 'UTF-8'), 1, 1, 'L', true);
        $fill = !$fill;
    }
} else {
    $pdf->Cell(150, 10, "El alumno no ha estado matriculado en ningún curso.", 1, 1, 'C');
}

// **Salida del PDF**
ob_end_clean(); // Evitar problemas de salida previa
$pdf->Output('FichaAlumno.pdf', 'I');
?>
