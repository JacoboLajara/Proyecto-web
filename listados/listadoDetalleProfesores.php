<?php
require_once __DIR__ . '/../libs/tcpdf/tcpdf.php';
require_once __DIR__ . '/../models/ProfesoresModel.php';
var_dump($_GET);
// Verificar si se pasó un ID válido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: ID de profesor no proporcionado.");
}

$idProfesor = $_GET['id'];

// Obtener datos del profesor
$model = new ProfesoresModel();
$datos = $model->getProfesorDetalle($idProfesor);

if (!$datos || !isset($datos['profesor'])) {
    die("Error: No se encontraron datos para el profesor con ID: $idProfesor");
}

// Extraer datos
$profesor = $datos['profesor'];

// Asegurar que los valores no sean null para evitar errores en htmlspecialchars
foreach ($profesor as $key => $value) {
    $profesor[$key] = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); // Si es null, usar ''
}

// **Crear PDF**
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Centro de Formación');
$pdf->SetTitle('Ficha del Profesor');
$pdf->SetSubject('Detalles del Profesor');

$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage();

// **Configurar colores y fuente**
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Ficha del Profesor', 0, 1, 'C');
$pdf->Ln(5);

// **Campos del profesor**
$pdf->SetFont('helvetica', '', 12);
$campos = [
    "ID Profesor" => $profesor['ID_Profesor'],
    "ID Usuario" => $profesor['ID_Usuario'],
    "Nombre" => $profesor['Nombre'] . " " . $profesor['Apellido1'] . " " . $profesor['Apellido2'],
    "Dirección" => $profesor['Direccion'],
    "Población" => $profesor['Poblacion'],
    "Provincia" => $profesor['Provincia'],
    "Código Postal" => $profesor['Codigo_Postal'],
    "Fecha de Nacimiento" => $profesor['Fecha_Nacimiento'],
    "Nivel de Estudios" => $profesor['Nivel_Estudios'],
    "Fecha de Alta" => $profesor['Fecha_Alta'],
    "Fecha de Baja" => $profesor['Fecha_Baja'],
    "Teléfono" => $profesor['Telefono'],
    "Email" => $profesor['Email']
];

foreach ($campos as $titulo => $valor) {
    $pdf->Cell(50, 8, $titulo . ":", 1, 0, 'L', false);
    $pdf->Cell(100, 8, $valor, 1, 1, 'L', false);
}

$pdf->Ln(5);

// **Salida del PDF**
ob_clean(); // Limpiar cualquier salida previa para evitar errores en TCPDF
$pdf->Output('FichaProfesor.pdf', 'I');
