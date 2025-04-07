<?php
require_once __DIR__ . '/../../init.php';
include __DIR__ . '/../../config/conexion.php';

// Verificar si el usuario está autenticado y tiene el rol de Alumno.
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'Personal_No_Docente') {
    header('Location: ../../login.php');
    exit();
}

$idPersonal = $_SESSION['usuario'] ?? null;
if (!$idPersonal) {
    die("Error: No se pudo identificar al empleado.");
}

// Obtener el nombre completo del alumno desde la base de datos
$nombreCompleto = '';
$sql = "SELECT Nombre, Apellido1, Apellido2 FROM personal_no_docente WHERE ID_Personal = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $idPersonal);
    $stmt->execute();
    $stmt->bind_result($nombre, $apellido1, $apellido2);
    if ($stmt->fetch()) {
        $nombreCompleto = trim("$nombre $apellido1 $apellido2");
    }
    $stmt->close();
} else {
    die("Error en la consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Control Horario Diario</title>
    <link rel="stylesheet" href="../../CSS/formularios.css" />

    <style>
        .registro-table {
            width: 100%;
            border-collapse: collapse;
        }

        .registro-table th,
        .registro-table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        .btn-fichar {
            padding: 6px 12px;
            margin: 0 5px;
            cursor: pointer;
        }
    </style>
</head>


<body class="bg-light">
    <div class="container mt-5">
        <h1>Registro de Jornada - <?php echo $nombreCompleto; ?></h1>
        <p><strong>Fecha:</strong> <span id="fechaActual"></span></p>

        <table class="registro-table">
            <thead>
                <tr>
                    <th>Turno</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Mañana</td>
                    <td id="entradaM">--:--</td>
                    <td id="salidaM">--:--</td>
                </tr>
                <tr>
                    <td>Tarde</td>
                    <td id="entradaT">--:--</td>
                    <td id="salidaT">--:--</td>
                </tr>
            </tbody>
        </table>

        <div id="botonesTurno" style="margin-top: 20px;">
            <!-- Los botones se generarán dinámicamente -->
        </div>

        <div id="respuesta" style="margin-top: 20px;"></div>
    </div>


    <!-- Pasar el ID de usuario al JS -->
    <script>
        window.ID_USUARIO = <?php echo json_encode($_SESSION['usuario'] ?? null); ?>;
    </script>

    <!-- Cargar primero jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Luego tu script, sin 'defer' -->
    <script src="/js/controlHorario.js"></script>
</body>


</body>

</html>