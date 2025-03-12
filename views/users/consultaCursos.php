<?php
require_once __DIR__ . '/../../models/CursosModel.php';
$model = new CursosModel();
$cursos = [];

// Obtener los cursos de la base de datos
$result = $model->getConnection()->query("SELECT ID_Curso, Nombre FROM Curso ORDER BY Nombre ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cursos[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Cursos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="../../js/consultaCursos.js" defer></script>
   
    <link rel="stylesheet" href="../../CSS/formularios.css" />

    <title>Consulta/Modificación de Cursos</title>
</head>

<body>
     <!-- Sidebar -->
     <div class="sidebar">
        <h2>Panel de Mantenimiento de Cursos</h2>
        <ul>
        <li><a href="./views/users/backoffice.php"><i class="fas fa-home"></i> Volver al panel central</a></li>
            <li>
                <a href="router.php?route=consultaCursos">
                    <i class="fas fa-eye"></i> Consultar Cursos
                </a>
            </li>
            <li>
                <a href="/listados/listadoCursoDetalle.php">
                    <i class="fas fa-list"></i> Listado de todos los cursos
                </a>
            </li>
            <li><a href="#"><i class="fas fa-bell"></i> Ver Notificaciones</a></li>
            <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <div class="header">
            <h1>Mantenimiento de Cursos</h1>
        </div>
    <div class="container mt-5">
        <h2 class="mb-4">Consultar Cursos</h2>

        <!-- Desplegable de cursos -->
        <div class="mb-3">
            <label for="cursoSelect" class="form-label">Selecciona un curso:</label>
            <select id="cursoSelect" class="form-select" onchange="cargarCurso()">
                <option value="">-- Selecciona un curso --</option>
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?php echo htmlspecialchars($curso['ID_Curso']); ?>">
                        <?php echo htmlspecialchars($curso['Nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Contenedor de detalles del curso -->
        <div id="detalleCurso" class="mt-4"></div>
        <button id="editarCursoBtn" class="btn btn-warning mt-3" style="display: none;"
            onclick="habilitarEdicion()">Editar Curso</button>
        <button id="guardarCambiosBtn" class="btn btn-success mt-3" style="display: none;"
            onclick="guardarCambios()">Guardar Cambios</button>

    </div>
</body>

</html>