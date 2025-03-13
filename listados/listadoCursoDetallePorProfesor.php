<?php
require_once __DIR__ . '/../init.php';



// Incluir TCPDF y el modelo de cursos
require_once __DIR__ . '/../libs/tcpdf/tcpdf.php';
require_once __DIR__ . '/../models/CursosModel.php';


// Crear una instancia del modelo
$model = new CursosModel();

/// Crear un nuevo documento PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Centro de Formación');
$pdf->SetTitle('Listado de Cursos');
$pdf->SetSubject('Listado de Cursos Activos');

// Establecer márgenes y configuración
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage('P');

// **Agregar título del documento**
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Listado de Cursos Activos', 0, 1, 'C'); // **Título centrado**
$pdf->Ln(5); // Espaciado antes de la tabla

// **Obtener cursos con módulos y unidades**
$cursos = $model->getCursosConModulosYUnidadesPorProfesor();
// 🔴 Depuración: Muestra los datos y detiene la ejecución
// echo "<pre>";
// var_dump($cursos);
// echo "</pre>";
// exit;

if (!$cursos || !is_array($cursos)) {
    die("No se encontraron cursos.");
}


$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, "Listado de cursos activos", 1, 1, 'C', true);
$pdf->Ln(5);

foreach ($cursos as $curso) {
    // Validar que el curso tenga información
    if (!isset($curso['ID_Curso']) || !isset($curso['Nombre']) || !isset($curso['Duracion_Horas'])) {
        continue;
    }

    // **DIBUJAR RECUADRO DEL CURSO**
    $pdf->SetFillColor(230, 230, 230); // Fondo gris claro
    $pdf->SetDrawColor(0, 0, 0); // Borde negro
    $pdf->SetLineWidth(0.5);

    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, "Curso: " . $curso['Nombre'] . " (ID: " . $curso['ID_Curso'] . ")", 1, 1, 'C', true);
    // ✅ Mostrar Aula del curso
    $pdf->SetFont('helvetica', '', 12);
    $aula = isset($curso['ID_Aula']) ? "Aula: " . $curso['ID_Aula'] : "Aula no asignada";
    $pdf->Cell(0, 8, $aula, 1, 1, 'C', true);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 8, "Duración: " . $curso['Duracion_Horas'] . " horas", 1, 1, 'C', true);
    $pdf->Ln(4);

    if (!empty($curso['modulos'])) {
        $rowColor = false; // Alternar colores de filas

        foreach ($curso['modulos'] as $modulo) {
            if (!isset($modulo['ID_Modulo']) || !isset($modulo['Nombre']) || !isset($modulo['Duracion_Horas'])) {
                continue;
            }

            // Alternar color de fondo de los módulos
            $pdf->SetFillColor($rowColor ? 245 : 220, 245, 245); // Alterna entre gris claro y blanco
            $rowColor = !$rowColor;

            // Mostrar módulos en tabla
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(140, 8, "Módulo: " . $modulo['Nombre'] . " (ID: " . $modulo['ID_Modulo'] . ")", 1, 0, 'L', true);
            $pdf->Cell(50, 8, "Duración: " . $modulo['Duracion_Horas'] . " horas", 1, 1, 'C', true);

            // Mostrar unidades formativas
            if (!empty($modulo['unidades'])) {
                $unidadColor = false;

                foreach ($modulo['unidades'] as $unidad) {
                    if (!isset($unidad['ID_Unidad_Formativa']) || !isset($unidad['Nombre']) || !isset($unidad['Duracion_Unidad'])) {
                        continue;
                    }

                    // Alternar color de fondo de las unidades formativas
                    $pdf->SetFillColor($unidadColor ? 255 : 240, 240, 240); // Alterna entre blanco y gris más claro
                    $unidadColor = !$unidadColor;

                    $pdf->SetFont('helvetica', '', 11);
                    $pdf->Cell(140, 8, "   Unidad: " . mb_convert_encoding($unidad['Nombre'], 'ISO-8859-1', 'UTF-8') . " (ID: " . $unidad['ID_Unidad_Formativa'] . ")", 1, 0, 'L', true);
                    $pdf->Cell(50, 8, "Duración: " . $unidad['Duracion_Unidad'] . " horas", 1, 1, 'C', true);
                }
            }
        }
    }
    $pdf->Ln(5);
}

// Salida del PDF
$pdf->Output('Listado_Cursos.pdf', 'I');
?>