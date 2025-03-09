<?php


require_once __DIR__ . '/../init.php';  // Asegurar que carga correctamente

// Verificar sesión
if (!isset($_SESSION['usuario']) || !isset($_SESSION['idAlumno'])) {
    die("⚠️ ERROR: No hay sesión iniciada.");
}


error_log("📌 DEBUG: Accediendo a CertificadoInscripcion.php");

if (!file_exists(dirname(__DIR__) . '/config/conexion.php')) {
    die("❌ Error: No se encontró el archivo de conexión.");
}

if (!file_exists(dirname(__DIR__) . '/libs/tcpdf/tcpdf.php')) {
    die("❌ Error: No se encontró TCPDF.");
}
require_once dirname(__DIR__) . '/init.php';
require_once dirname(__DIR__) . '/libs/tcpdf/tcpdf.php';
require_once dirname(__DIR__) . '/config/conexion.php';


// Verificar si el usuario está autenticado y tiene el rol de Alumno.
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'Alumno') {
    header('Location: ../../login.php');
    exit();
}

$idAlumno = $_SESSION['idAlumno'] ?? null;
if (!$idAlumno) {
    die("Error: No se pudo identificar al alumno.");
}

// Obtener datos del alumno y del curso desde la base de datos
$sql = "SELECT a.Nombre, a.Apellido1, a.Apellido2, a.ID_Alumno, c.Nombre AS CursoNombre, c.Duracion_Horas
        FROM alumno a
        JOIN alumno_curso i ON a.ID_Alumno = i.ID_Alumno
        JOIN curso c ON i.ID_Curso = c.ID_Curso
        WHERE a.ID_Alumno = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $idAlumno);
    $stmt->execute();
    $stmt->bind_result($nombre, $apellido1, $apellido2, $dni, $cursoNombre, $duracionHoras);
    if ($stmt->fetch()) {
        $nombreCompleto = trim("$nombre $apellido1 $apellido2");
    } else {
        die("Error: No se encontraron datos para el alumno.");
    }
    $stmt->close();
} else {
    die("Error en la consulta: " . $conn->error);
}

// Crear nuevo documento PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nombre del Centro de Formación');
$pdf->SetTitle('Certificado de Inscripción');
$pdf->SetSubject('Certificado de Inscripción en Acción Formativa');

// Configuración de márgenes
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Añadir una página
$pdf->AddPage();

// Contenido del certificado
$html = <<<EOD
<h1 style="text-align: center;">CERTIFICADO DE INSCRIPCIÓN EN ACCIÓN FORMATIVA</h1>
<p style="text-align: center;">
    <strong>Nombre del Centro de Formación</strong><br>
    Dirección del Centro<br>
    Teléfono: 123456789<br>
    Correo Electrónico: info@centroformacion.com
</p>
<p>En [Ciudad], a [Fecha Actual]</p>
<p>CERTIFICA:</p>
<p>Que <strong>$nombreCompleto</strong>, con DNI/NIE <strong>$dni</strong>, ha formalizado su inscripción en la acción formativa "<strong>$cursoNombre</strong>", 
con una duración de <strong>$duracionHoras</strong> horas, que está siendo impartido en nuestro centro <strong>Nombre del Centro</strong>, con fecha de inicio el <strong>'01/02/2025'</strong>
 y fecha prevista de finalización el <strong>'26/05/2025'</strong>.</p>
<p>Este certificado se expide a solicitud del interesado para los efectos que estime oportunos.</p>
<br><br><br>
<p>Atentamente,</p>
<p><strong>Nombre del Director</strong><br>
Director/a del Centro de Formación <br>
Firma y Sello del Centro</p>
EOD;

// Reemplazar [Ciudad] y [Fecha Actual] con valores reales
$ciudad = 'Nombre de la Ciudad';
$fechaActual = date('d/m/Y');
$html = str_replace('[Ciudad]', $ciudad, $html);
$html = str_replace('[Fecha Actual]', $fechaActual, $html);

// Imprimir contenido en el PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Cerrar y generar el PDF
$pdf->Output('certificado_inscripcion.pdf', 'I');
?>
