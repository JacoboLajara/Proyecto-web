<?php
require_once __DIR__ . '/../../init.php';
include __DIR__ . '/../../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'Personal_No_Docente') {
    header('Location: ../../login.php');
    exit();
}

$idPersonal = $_SESSION['usuario'] ?? null;
if (!$idPersonal) {
    die("Error: No se pudo identificar al empleado.");
}

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
    <title>Control Horario Semanal</title>
    <link rel="stylesheet" href="../../CSS/formularios.css" />
    <style>
        .registro-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }

        .registro-table th,
        .registro-table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
            vertical-align: middle;
        }

        .registro-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .registro-table thead {
            background-color: #007bff;
            color: white;
        }

        .btn-fichar {
            padding: 6px 12px;
            margin: 10px 5px;
            cursor: pointer;
        }

        .export-buttons {
            margin-top: 30px;
            text-align: center;
        }

        .export-buttons button {
            padding: 8px 16px;
            margin: 5px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h1>Registro Semanal de Jornada - <?php echo $nombreCompleto; ?></h1>
        <p><strong>Hoy:</strong> <span id="fechaActual"></span></p>

        <table class="registro-table" id="tablaSemanal">
            <thead>
                <tr>
                    <th>Día</th>
                    <th>Turno</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th>Horas</th>
                </tr>
            </thead>
            <tbody>
                <!-- Filas dinámicas generadas por JS -->
            </tbody>
        </table>

        <!-- Botones para fichar (generados por JS) -->
        <div id="botonesTurno" style="margin-top: 20px;"></div>

        <!-- Línea divisoria -->
        <hr style="margin: 40px 0;">

        <!-- Botones de exportación -->
        <!-- Filtros y exportaciones -->
        <hr style="margin: 40px 0;">

        <div class="export-buttons">
            <form method="GET" action="/exportadores/ExportadorHorarioPDF.php" style="display:inline-block;">
                <label for="inicio">Desde:</label>
                <input type="date" name="inicio" value="<?php echo date('Y-m-d', strtotime('monday this week')); ?>"
                    required>
                <label for="fin">Hasta:</label>
                <input type="date" name="fin" value="<?php echo date('Y-m-d'); ?>" required>
                <button type="submit">Exportar a PDF</button>
            </form>

            <form method="GET" action="/exportadores/ExportadorHorarioExcel.php"
                style="display:inline-block; margin-left: 20px;">
                <label for="inicio">Desde:</label>
                <input type="date" name="inicio" value="<?php echo date('Y-m-d', strtotime('monday this week')); ?>"
                    required>
                <label for="fin">Hasta:</label>
                <input type="date" name="fin" value="<?php echo date('Y-m-d'); ?>" required>
                <button type="submit">Exportar a Excel</button>
            </form>
        </div>


        <!-- Mensajes -->
        <div id="respuesta" style="margin-top: 20px;"></div>
    </div>

    <script>
        window.ID_USUARIO = <?php echo json_encode($_SESSION['usuario'] ?? null); ?>;
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/js/controlHorario.js"></script>
</body>

</html>