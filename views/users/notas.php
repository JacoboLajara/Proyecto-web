<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Notas</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../../CSS/colores.css" />
    <link rel="stylesheet" href="../../CSS/estilohojacontacto.css" />
    <link rel="stylesheet" href="../../CSS/formularios.css" />
    <script src="../../js/notas.js" defer></script> <!-- ✅ Solo una vez -->

</head>

<body class="bg-light">
    <?php include __DIR__ . '/../../PHP/navbar3.php'; ?>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Panel de control de notas</h2>
        <ul>
        <li><a href="./views/users/backoffice.php"><i class="fas fa-home"></i> Volver al panel central</a></li>

            <li>
                <a href="#" id="linkNotas">
                    <i class="fas fa-list"></i> Listado de notas por alumno y curso
                </a>
            </li>
            <li><a href="#"><i class="fas fa-bell"></i> Ver Notificaciones</a></li>
            <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <div class="header">
            <h1>Mantenimiento de Notas</h1>
        </div>
        <?php
        if (isset($_GET['error']) || isset($_GET['success'])) {
            $mensaje = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : htmlspecialchars($_GET['success']);
            $tipo = isset($_GET['error']) ? 'alert-danger' : 'alert-success';
            echo "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const mensajeModal = document.getElementById('mensajeModalBody');
                mensajeModal.className = '';
                mensajeModal.classList.add('modal-body', '$tipo');
                mensajeModal.textContent = '$mensaje';
                const modal = new bootstrap.Modal(document.getElementById('mensajeModal'));
                modal.show();
                
                // Redirigir después de 3 segundos
                setTimeout(function() {
                    window.location.href = '/mainpage.php';
                }, 3000);
            });
        </script>";
        }
        ?>

        <div class="container mt-5">
            <h2 class="mb-4 text-center">Gestión de Notas</h2>
            <div class="card p-4 shadow">
                <form id="notasForm">

                    <!-- Selección de curso -->
                    <div class="mb-3">
                        <label for="id_curso" class="form-label">Selecciona un curso</label>
                        <select class="form-control" id="id_curso" required>
                            <option value="">Seleccione un curso</option>
                        </select>
                    </div>

                    <!-- Tabla de alumnos -->
                    <div id="alumnosContainer" style="display: none;">
                        <h3 class="mb-3">Alumnos Matriculados</h3>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="alumnosTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Seleccionar</th>
                                        <th>Nombre</th>
                                        <th>Apellido 1</th>
                                        <th>Apellido 2</th>
                                        <th>DNI</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="alumnosTableBody">
                                    <!-- Se llenará dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <!-- Tabla de módulos/unidades formativas -->
                    <div id="notasContainer" style="display: none;">
                        <h3 class="mb-3">Notas del Alumno</h3>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="notasTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Calificación</th>
                                    </tr>
                                </thead>
                                <tbody id="notasTableBody">
                                    <!-- Se llenará dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                        <!-- Botón para editar (se mostrará solo si existen notas precargadas) -->
                        <button id="editarNotasBtn" class="btn btn-warning" style="display: none;">Editar Notas</button>
                        <!-- Botón para guardar (ya sea inserción o actualización) -->
                        <button id="guardarNotaBtn" type="button" class="btn btn-success">Guardar Notas</button>

                    </div>
                </form>
            </div>
</body>

</html>