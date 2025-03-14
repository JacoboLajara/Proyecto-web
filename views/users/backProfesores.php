<?php
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/NotasModel.php';
require_once __DIR__ . '/../../models/NotificacionModel.php';
require_once __DIR__ . '/../../models/HorariosModel.php';
require_once __DIR__ . '/../../models/MatriculaProfesorModel.php';

// Verificar si el usuario está autenticado y tiene el rol de Alumno.
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'Profesor') {
    header('Location: ../../login.php');
    exit();
}

$idProfesor = $_SESSION['usuario'] ?? null;
if (!$idProfesor) {
    die("Error: No se pudo identificar al profesor.");
}

// Obtener el nombre completo del alumno desde la base de datos
$nombreCompleto = '';
$sql = "SELECT Nombre, Apellido1, Apellido2 FROM profesor WHERE ID_Profesor = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $idProfesor);
    $stmt->execute();
    $stmt->bind_result($nombre, $apellido1, $apellido2);
    if ($stmt->fetch()) {
        $nombreCompleto = trim("$nombre $apellido1 $apellido2");
    }
    $stmt->close();
} else {
    die("Error en la consulta: " . $conn->error);
}

$matriculasModel = new MatriculaProfesorModel();
$notificacionModel = new NotificacionModel();
$horarioModel = new HorariosModel();

//$cursos = $matriculasModel->obtenerTodasLasNotasPorAlumno($idAlumno);
$notificaciones = $notificacionModel->getNotificacionesPorProfesor($idProfesor);
$horarios = $horarioModel->obtenerHorariosPorProfesor($idProfesor);

file_put_contents('debug.log', "DEBUG (panelAlumno.php) - session_id(): " . session_id() . "\n", FILE_APPEND);
file_put_contents('debug.log', "DEBUG (panelAlumno.php) - Usuario en sesión: " . ($_SESSION['usuario'] ?? 'No definido') . "\n", FILE_APPEND);
file_put_contents('debug.log', "DEBUG (panelAlumno.php) - Rol en sesión: " . ($_SESSION['rol'] ?? 'No definido') . "\n", FILE_APPEND);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Profesor</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../../CSS/formularios.css" />


</head>

<body>
    <div class="sidebar">
        <h2>Panel del Profesor</h2>
        <ul>
            <!-- 📌 Listado de todos los cursos -->
            <li><a href="/../../listados/listadoCursos.php" target="_blank"> 📄 Listado de todos los cursos</a></li>
            <!-- 📌 Listado de cursos con módulos y unidades formativas -->
            <li><a href="/listados/listadoCursoDetallePorProfesor.php" target="_blank">📄 Listado detalle de tus Cursos
                    y horarios</a></li>

            <li><a href="/mainpage.php?route=createNotas"><i class="fas fa-clipboard"></i> Gestión de Notas</a></li>
            <li> <a href="/listados/ListadoHorarios.php"><i class="fas fa-list"></i> Listar Todos los Horarios</a></li>
            <li><a href="/mainpage.php?route=createNotificacion"><i class="fas fa-bell"></i> Gestión de
                    Notificaciones</a></li>
            <li><a href="ayuda.php"><i class="fas fa-question-circle"></i> Ayuda</a></li>
            <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </div>

    <div class="content">

        <div class="header">
            <h1>Bienvenido, <?php echo $_SESSION['usuario']; ?></h1>
            <h2><?php echo htmlspecialchars($nombreCompleto); ?></h2>
            <p>Rol: <?php echo $_SESSION['rol']; ?></p>
        </div>

        <!-- Sección de Notas -->


        <!-- Sección de Notificaciones -->
        <div class="section">
            <h2>Tus Notificaciones</h2>
            <?php if (!empty($notificaciones)): ?>
                <ul>
                    <?php foreach ($notificaciones as $notificacion): ?>
                        <li><?php echo htmlspecialchars($notificacion['Mensaje']); ?> - Fecha:
                            <?php echo htmlspecialchars($notificacion['Fecha']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No tienes notificaciones.</p>
            <?php endif; ?>
        </div>

        <!-- Sección de Horarios -->
        <div class="section">
            <h2>Cursos asignados y horarios</h2>
            <?php if (!empty($horarios)): ?>
                <table border="1" cellpadding="10" cellspacing="0">
                    <tr>
                        <th rowspan="2">Curso</th>
                        <th rowspan="2" >Día</th>
                        <th colspan="2">Mañana</th> 
                        <th colspan="2">Tarde</th> 
                        <th rowspan="2">Aula</th>
                    </tr>
                    <tr>
                        
                        <th>Hora Inicio</th>
                        <th>Hora Fin</th>
                        <th>Hora Inicio</th>
                        <th>Hora Fin</th>
                        
                    </tr>

                    <?php foreach ($horarios as $horario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($horario['Curso'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($horario['Dia'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($horario['Hora_Inicio'] ?? '--:--'); ?></td>
                            <td><?php echo htmlspecialchars($horario['Hora_Fin'] ?? '--:--'); ?></td>
                            <td><?php echo htmlspecialchars($horario['Tarde_Inicio'] ?? '--:--'); ?></td>
                            <td><?php echo htmlspecialchars($horario['Tarde_Fin'] ?? '--:--'); ?></td>
                            <td><?php echo htmlspecialchars($horario['Aula'] ?? '--:--'); ?></td>
                        </tr>

                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No tienes horarios asignados.</p>
            <?php endif; ?>
        </div>

    </div>
</body>

</html>