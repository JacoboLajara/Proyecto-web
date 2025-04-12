<?php
require_once __DIR__ . '/../libs/tcpdf/tcpdf.php';
require_once __DIR__ . '/../models/PersonalModel.php';

// 🔎 Verificar si se pasó un ID válido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("⚠ Error: ID de personal no proporcionado.");
}

$idPersonal = $_GET['id'];

// 🔎 Obtener los datos del personal desde el modelo
$model = new PersonalModel();
$persona = $model->getPersonalDetalle($idPersonal);

// 🔎 Si no hay resultados, mostramos un error en pantalla
if (!$persona) {
    die("⚠ Error: No se encontraron datos para el personal con ID: $idPersonal");
}

// 📝 Crear PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Centro de Formación');
$pdf->SetTitle('Ficha del Personal No Docente');
$pdf->SetSubject('Detalles del Personal');

// 📌 Configuración de márgenes y tipo de documento
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage();

// 🎨 Restablecer colores
$pdf->SetFillColor(255, 255, 255); // Fondo blanco
$pdf->SetDrawColor(0, 0, 0); // Bordes negros
$pdf->SetTextColor(0, 0, 0); // Texto negro

// 🏷 Título del documento
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Ficha del Personal No Docente', 0, 1, 'C');
$pdf->Ln(5);

// 📊 Datos del personal
$pdf->SetFont('helvetica', '', 12);

// 🔍 Estructura de los datos
$campos = [
    "ID Personal" => $persona['ID_Personal'],
    "ID Usuario" => $persona['ID_Usuario'],
    "Nombre" => $persona['Nombre'] . " " . $persona['Apellido1'] . " " . $persona['Apellido2'],
    "Dirección" => $persona['Direccion'],
    "Población" => $persona['Poblacion'],
    "Provincia" => $persona['Provincia'],
    "Código Postal" => $persona['Codigo_Postal'],
    "Fecha de Nacimiento" => $persona['Fecha_Nacimiento'],
    "Nivel de Estudios" => $persona['Nivel_Estudios'],
    "Fecha de Alta" => $persona['Fecha_Alta'],
    "Fecha de Baja" => $persona['Fecha_Baja'] ?: 'Activo',
    "Teléfono" => $persona['Telefono'],
    "Email" => $persona['Email']
];

// 📌 Imprimir cada campo en el PDF
foreach ($campos as $titulo => $valor) {
    $pdf->Cell(50, 8, $titulo . ":", 1, 0, 'L', false);
    $pdf->Cell(100, 8, htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'), 1, 1, 'L', false);
}

$pdf->Ln(5);

// 📄 Salida del PDF
$pdf->Output('FichaPersonalNoDocente.pdf', 'I');
?>

