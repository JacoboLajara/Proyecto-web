<?php
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/NotasModel.php';
require_once __DIR__ . '/../../models/NotificacionModel.php';
require_once __DIR__ . '/../../models/HorariosModel.php';

// Verificar si el usuario está autenticado y tiene el rol de Alumno.
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'Alumno') {
    header('Location: ../../login.php');
    exit();
}

$idAlumno = $_SESSION['idAlumno'] ?? null;
if (!$idAlumno) {
    die("Error: No se pudo identificar al alumno.");
}

// Obtener el nombre completo del alumno desde la base de datos
$nombreCompleto = '';
$sql = "SELECT Nombre, Apellido1, Apellido2 FROM alumno WHERE ID_Alumno = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $idAlumno);
    $stmt->execute();
    $stmt->bind_result($nombre, $apellido1, $apellido2);
    if ($stmt->fetch()) {
        $nombreCompleto = trim("$nombre $apellido1 $apellido2");
    }
    $stmt->close();
} else {
    die("Error en la consulta: " . $conn->error);
}

$notasModel = new NotasModel();
$notificacionModel = new NotificacionModel();
$horarioModel = new HorariosModel();

$notas = $notasModel->obtenerTodasLasNotasPorAlumno($idAlumno);
$notificaciones = $notificacionModel->getNotificacionesPorAlumno($idAlumno);
$horarios = $horarioModel->obtenerHorariosPorAlumno($idAlumno);

file_put_contents('debug.log', "DEBUG (panelAlumno.php) - session_id(): " . session_id() . "\n", FILE_APPEND);
file_put_contents('debug.log', "DEBUG (panelAlumno.php) - Usuario en sesión: " . ($_SESSION['usuario'] ?? 'No definido') . "\n", FILE_APPEND);
file_put_contents('debug.log', "DEBUG (panelAlumno.php) - Rol en sesión: " . ($_SESSION['rol'] ?? 'No definido') . "\n", FILE_APPEND);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Alumno</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../../CSS/formularios.css" />


</head>

<body>
    <div class="sidebar">
        <h2>Panel del Alumno</h2>
        <ul>
            <!-- <li><i class="fas fa-clipboard"></i>Notas</li>
            <li><i class="fas fa-bell"></i>Notificaciones</li> -->
            <li>
                <a href="/router.php?route=encuestas">
                    <i class="fas fa-poll"></i>
                    Realizar Encuesta de Satisfacción
                </a>
            </li>
            <li><a href="../../listados/CertificadoInscripcion.php"><i class="fas fa-file-download"></i> Descargar
                    Certificados</a></li>
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
        <div class="section">
            <h2>Tus Notas</h2>

            <?php if (!empty($notas)): ?>
                <?php
                // Agrupar las notas por curso y luego por módulo
                $notasPorCurso = [];

                foreach ($notas as $nota) {
                    $curso = $nota['Curso'] ?? 'Sin Curso';
                    $modulo = $nota['Modulo'] ?? 'Sin Módulo';

                    // Agrupar por curso y luego por módulo
                    $notasPorCurso[$curso][$modulo][] = $nota;
                }
                ?>

                <?php foreach ($notasPorCurso as $curso => $modulos): ?>
                    <!-- Mostrar el nombre del curso -->
                    <h3>Curso: <?php echo htmlspecialchars($curso); ?></h3>

                    <?php foreach ($modulos as $modulo => $notasModulo): ?>
                        <!-- Mostrar el nombre del módulo -->
                        <h4>→ Módulo: <?php echo htmlspecialchars($modulo); ?></h4>

                        <table border="1" cellpadding="10" cellspacing="0">
                            <tr>
                                <th>Unidad Formativa</th>
                                <th>Calificación</th>
                                <th>Fecha Registro</th>
                            </tr>
                            <?php foreach ($notasModulo as $nota): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($nota['Unidad_Formativa'] ?? 'Sin Unidad'); ?></td>
                                    <td><?php echo htmlspecialchars($nota['Calificación']); ?></td>
                                    <td><?php echo htmlspecialchars($nota['Fecha_Registro']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                        <br> <!-- Espacio entre módulos -->
                    <?php endforeach; ?>

                    <hr> <!-- Separador entre cursos -->
                <?php endforeach; ?>

            <?php else: ?>
                <p>No tienes notas registradas.</p>
            <?php endif; ?>
        </div>



        <!-- Sección de Horarios -->
        <div class="section">
            <h2>Horarios de tus Cursos</h2>
            <?php if (!empty($horarios)): ?>
                <table border="1" cellpadding="10" cellspacing="0">
                    <tr>
                        <th rowspan="2">Curso</th>
                        <th rowspan="2">Día</th>
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
        <!-- Sección de Certificados -->
        <div class="section">
            <li>
                <a href="/router.php?route=encuestas">
                    <i class="fas fa-poll"></i>
                    Realizar Encuesta de Satisfacción
                </a>
            </li>
            <li><a href="../../listados/CertificadoInscripcion.php"><i class="fas fa-file-download"></i> Descargar
                    Certificado de Inscripción</a></li>

            <p>Puedes descargar tus certificados y diplomas desde esta sección.</p>
        </div>
    </div>
</body>

</html>