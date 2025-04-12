<?php
require_once __DIR__ . '/../libs/tcpdf/tcpdf.php';
require_once __DIR__ . '/../models/UserModel.php';

$model = new UserModel();
$usuarios = $model->getAllUsersWithRoles();

$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Centro de Formación');
$pdf->SetTitle('Listado de Usuarios');
$pdf->SetSubject('Lista de todos los usuarios del sistema');

$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage('L');

$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Listado de Usuarios', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 12);

if (!empty($usuarios)) {
    $pdf->SetFillColor(200, 220, 255);
    $pdf->Cell(50, 10, "DNI/NIE", 1, 0, 'C', true);
    $pdf->Cell(80, 10, "Nombre Completo", 1, 0, 'C', true);
    $pdf->Cell(50, 10, "Rol", 1, 0, 'C', true);
    $pdf->Ln();

    foreach ($usuarios as $usuario) {
        $nombreCompleto = $usuario['Nombre'] . ' ' . $usuario['Apellido1'] . ' ' . ($usuario['Apellido2'] ?? '');
        $pdf->Cell(50, 8, $usuario['DNI_NIE'], 1, 0, 'C');
        $pdf->Cell(80, 8, $nombreCompleto, 1, 0, 'L');
        $pdf->Cell(50, 8, $usuario['Rol'], 1, 0, 'C');
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, "No se encontraron usuarios.", 0, 1, 'L');
}

$pdf->Output('ListadoUsuarios.pdf', 'I');
