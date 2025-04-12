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
$idPersonal = $_SESSION['usuario'] ?? null;

$formatter = new IntlDateFormatter(
    'es_ES',
    IntlDateFormatter::FULL,
    IntlDateFormatter::NONE,
    'Europe/Madrid',
    IntlDateFormatter::GREGORIAN,
    "EEEE d/MM/yyyy"
);
$textoHoy = ucfirst($formatter->format(new DateTime()));
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Control Horario Semanal</title>
    <link rel="stylesheet" href="../../CSS/formularios.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
        }

        .sidebar h3 {
            margin-top: 0;
            font-size: 20px;
            border-bottom: 2px solid white;
            padding-bottom: 10px;
        }

        .sidebar form {
            margin-bottom: 20px;
        }

        .sidebar label {
            display: block;
            margin-top: 10px;
            font-size: 14px;
        }

        .sidebar input[type="date"] {
            width: 100%;
            padding: 5px;
            margin-top: 5px;
            border-radius: 4px;
            border: none;
        }

        .sidebar button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            background-color: #ffc107;
            color: #333;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .sidebar button:hover {
            background-color: #ffca2c;
        }

        .main-content {
            margin-left: 360px;
            padding: 30px;
        }

        .registro-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            margin-top: 20px;
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
            padding: 10px 20px;
            margin: 15px 10px 0 0;
            font-size: 14px;
            font-weight: bold;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-fichar:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h3>Exportar Horario</h3>

        <form method="GET" action="/exportadores/ExportadorHorarioPDF.php">
            <label for="inicio">Desde:</label>
            <input type="date" name="inicio" value="<?php echo date('Y-m-d', strtotime('monday this week')); ?>"
                required>

            <label for="fin">Hasta:</label>
            <input type="date" name="fin" value="<?php echo date('Y-m-d'); ?>" required>

            <button type="submit">PDF</button>
        </form>

        <form method="GET" action="/exportadores/ExportadorHorarioExcel.php">
            <label for="inicio">Desde:</label>
            <input type="date" name="inicio" value="<?php echo date('Y-m-d', strtotime('monday this week')); ?>"
                required>

            <label for="fin">Hasta:</label>
            <input type="date" name="fin" value="<?php echo date('Y-m-d'); ?>" required>

            <button type="submit">Excel</button>
        </form>
        <ul>
        <li><a href="./views/users/backoffice.php"><i class="fas fa-home"></i> Volver al panel central</a></li>
        <ul>
    </div>

    <div class="main-content">
        <h1>Registro Semanal de Jornada - <?php echo $nombreCompleto; ?></h1>
        <p><strong>Hoy:</strong> <?php echo $textoHoy; ?></p>

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
                <!-- Rellenado con JS -->
            </tbody>
        </table>

        <div id="botonesTurno"></div>
        <div id="respuesta" style="margin-top: 20px;"></div>
    </div>

    <script>
        window.ID_USUARIO = <?php echo json_encode($_SESSION['usuario'] ?? null); ?>;
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/js/controlHorario.js"></script>
</body>

</html>