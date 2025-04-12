<?php
require_once __DIR__ . '/../../init.php';  // Ruta ajustada para incluir init.php
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/NotificacionModel.php';

/*file_put_contents('debug.log', "DEBUG (backoffice.php) - session_id(): " . session_id() . "\n", FILE_APPEND);
file_put_contents('debug.log', "DEBUG (backoffice.php) - Usuario en sesión: " . ($_SESSION['usuario'] ?? 'No definido') . "\n", FILE_APPEND);
file_put_contents('debug.log', "DEBUG (backoffice.php) - Rol en sesión: " . ($_SESSION['rol'] ?? 'No definido') . "\n", FILE_APPEND);*/


// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../login.php');
    exit();
}

// Verificar si hay sesión activa y definir las variables
$usuarioAutenticado = isset($_SESSION['usuario']);
$rol = $usuarioAutenticado ? $_SESSION['rol'] : null;
if (!isset($_SESSION['auth_token'])) {
    file_put_contents('debug.log', "DEBUG (backoffice.php) - auth_token no encontrado, redirigiendo a login\n", FILE_APPEND);
    header('Location: ../../login.php');
    exit();
}
if ($rol === 'Profesor') {
    $idProfesor = $_SESSION['usuario'] ?? null;
    $notificacionModel = new NotificacionModel();
    $notificaciones = $notificacionModel->getNotificacionesPorProfesor($idProfesor);
} elseif ($rol === 'Personal_No_Docente'){
    $idPersonal = $_SESSION['usuario'] ?? null;
    $notificacionModel = new NotificacionModel();
    $notificaciones = $notificacionModel->getNotificacionesPorPersonal($idPersonal);
}

/*echo "<pre>";
echo "Bienvenido, " . $_SESSION['usuario'] . "<br>";
echo "Rol: " . $_SESSION['rol'] . "<br>";
echo "</pre>";*/
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BackOffice - Administración</title>

    <link rel="stylesheet" href="../../CSS/formularios.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>

<body>
    <div class="sidebar">
        <h2>BackOffice</h2>
        <ul>
            <?php if (!$usuarioAutenticado): ?>
                <li><a href="/login.php"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a></li>
            <?php else: ?>
                <!-- Menú según el rol del usuario -->
                <?php if ($rol === 'Administrador'): ?>
                    <li><a href="/mainpage.php?route=usuarios"><i class="fas fa-user-plus"></i> Gestión Usuario</a></li>
                    <li><a href="/mainpage.php?route=createAlumno"><i class="fas fa-user-graduate"></i> Gestión Alumno</a></li>
                    <li><a href="/mainpage.php?route=createProfesor"><i class="fas fa-chalkboard-teacher"></i> Gestión Profesores</a></li>
                    <li><a href="/mainpage.php?route=createPersonal"><i class="fas fa-users"></i> Gestión Personal</a></li>
                    <li><a href="/mainpage.php?route=createCurso"><i class="fas fa-book"></i> Gestión Curso</a></li>
                    <li><a href="/mainpage.php?route=createAula"><i class="fas fa-school"></i> Gestión Aulas</a></li>
                    <li><a href="/mainpage.php?route=createMatricula"><i class="fas fa-clipboard-list"></i> Matrículas</a></li>
                    <li><a href="/mainpage.php?route=createMatriculaprofesor"><i class="fas fa-clipboard-list"></i> Asignación Profesores</a></li>
                    <li><a href="/mainpage.php?route=createNotas"><i class="fas fa-clipboard"></i> Gestión de Notas</a></li>
                    <li><a href="/mainpage.php?route=createHorario"><i class="fas fa-calendar-alt"></i> Asignar Horarios</a></li>
                    <li><a href="/mainpage.php?route=recibos"><i class="fas fa-clipboard"></i> Gestión de Recibos</a></li>
                    <li><a href="/mainpage.php?route=createNotificacion"><i class="fas fa-bell"></i> Gestión de Notificaciones</a></li>
                    <li><a href="/mainpage.php?route=createHorarioEmpleado"><i class="fas fa-clock"></i> Control Horario</a></li>
                    <li><a href="ayuda.php"><i class="fas fa-question-circle"></i> Ayuda</a></li>

                <?php elseif ($rol === 'Personal_No_Docente'): ?>
                    <li><a href="/mainpage.php?route=createAlumno"><i class="fas fa-user-graduate"></i> Gestión Alumno</a></li>
                    <li><a href="/mainpage.php?route=createProfesor"><i class="fas fa-chalkboard-teacher"></i> Gestión Profesores</a></li>
                    <li><a href="/mainpage.php?route=createPersonal"><i class="fas fa-users"></i> Gestión Personal</a></li>
                    <li><a href="/mainpage.php?route=createCurso"><i class="fas fa-book"></i> Gestión Curso</a></li>
                    <li><a href="/mainpage.php?route=createAula"><i class="fas fa-school"></i> Gestión Aulas</a></li>
                    <li><a href="/mainpage.php?route=createMatricula"><i class="fas fa-clipboard-list"></i> Matrículas</a></li>
                    <li><a href="/mainpage.php?route=createNotas"><i class="fas fa-clipboard"></i> Gestión de Notas</a></li>
                    <li><a href="/mainpage.php?route=createHorario"><i class="fas fa-calendar-alt"></i> Asignar Horarios</a></li>
                    <li><a href="/mainpage.php?route=recibos"><i class="fas fa-clipboard"></i> Gestión de Recibos</a></li>
                    <li><a href="/mainpage.php?route=createNotificacion"><i class="fas fa-bell"></i> Gestión de Notificaciones</a></li>
                    <li><a href="/mainpage.php?route=createHorarioEmpleado"><i class="fas fa-clock"></i> Control Horario</a></li>
                    <li><a href="ayuda.php"><i class="fas fa-question-circle"></i> Ayuda</a></li>


                <?php elseif ($rol === 'Profesor'): 
                    if ($rol === 'Profesor') {
                    header("Location: /views/users/backProfesores.php");
                    exit();
                }
                ?>
                <?php endif; ?>
                <?php
                if ($rol === 'Alumno') {
                    header("Location: views/users/backAlumnos.php");
                    exit();
                }
                ?>

                <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            <?php endif; ?>
        </ul>

    </div>
    <div class="content">
        <div class="header">
            <h1>Bienvenido al BackOffice</h1>
            <p>Usuario: <?php echo $_SESSION['usuario']; ?> | Rol: <?php echo $_SESSION['rol']; ?></p>
        </div>
        <!-- Sección de Notificaciones -->
        <div class="section">
            <h2>Tus Notificaciones</h2>
            <?php if (!empty($notificaciones)): ?>
                <ul>
                    <?php foreach ($notificaciones as $notificacion): ?>
                        <li><?php echo htmlspecialchars($notificacion['Mensaje']); ?> - Fecha:
                            <?php echo htmlspecialchars($notificacion['Fecha']); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No tienes notificaciones.</p>
            <?php endif; ?>
        </div>
        <div class="section">
            <h2>Resumen del Sistema</h2>
            <p>Utiliza el menú lateral para navegar por las diferentes secciones del sistema de administración.</p>
        </div>
        <div class="section">
            <h2>Estadísticas Rápidas</h2>
            <ul>
                <li>Total de Usuarios: ...</li>
                <li>Total de Alumnos: ...</li>
                <li>Total de Profesores: ...</li>
            </ul>
        </div>
    </div>
</body>

</html>