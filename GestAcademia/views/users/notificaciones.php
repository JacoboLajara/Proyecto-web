<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Bootstrap y otros recursos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="../../js/aulas.js" defer></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Gestión de Notificaciones</title>
    <link rel="stylesheet" href="../../CSS/estilohojacontacto.css" />
    <link rel="stylesheet" href="../../CSS/formularios.css" />
</head>

<body><!-- Sidebar -->
    <div class="sidebar">
        <h2>Panel de Notificaciones</h2>
        <ul>
        <li><a href="./views/users/backoffice.php"><i class="fas fa-home"></i> Volver al panel central</a></li>
            
            <li><a href="#"><i class="fas fa-bell"></i> Ver Notificaciones</a></li>
            <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </div>
    <!-- Contenido principal -->
    <div class="content">
        <div class="header">
            <h1>Mantenimiento de Notificaciones</h1>
        </div>
    <?php
    if (isset($_GET['success'])) {
        echo '<div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                ' . htmlspecialchars($_GET['success']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
              </div>';
    }
    if (isset($_GET['error'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                ' . htmlspecialchars($_GET['error']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
              </div>';
    }
    ?>

    <div class="container-fluid mt-4">
        <form method="post" action="router.php?route=storeNotificacion">
            <fieldset>
                <div class="row">
                    <legend class="mb-3 text-center">Gestión de Notificaciones</legend>
                </div>

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="notificacion" class="form-label"><b>Notificación</b></label>
                        <textarea id="notificacion" name="notificacion" class="form-control" rows="4"
                            placeholder="Escribe el texto de la notificación..."></textarea>
                    </div>
                    <div class="col-md-4">
                        <label for="opciones" class="form-label"><b>Enviar a:</b></label>
                        <select class="form-select" id="opciones" name="opciones" onchange="mostrarPregunta()">
                            <option value="" disabled selected>Selecciona una figura</option>
                            <option value="Alumno">Alumno</option>
                            <option value="Profesor">Profesor</option>
                            <option value="Personal">Personal No Docente</option>
                            <option value="Curso">Curso</option>
                        </select>
                    </div>
                </div>

                <div id="preguntaTodos" style="display: none;" class="mb-3">
                    <p><b>¿Quieres enviar la notificación a todos o seleccionar algunos?</b></p>
                    <button type="button" class="btn btn-success" onclick="enviarATodos()">Enviar a Todos</button>
                    <button type="button" class="btn btn-primary" onclick="mostrarModalSeleccion()">Seleccionar Algunos</button>
                </div>

                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary">Enviar Notificación</button>
                    <a href="mainpage.php" class="btn-volver">Volver</a>
                </div>
            </fieldset>
        </form>
    </div>

    <!-- Modal para selección de usuarios -->
    <div class="modal fade" id="modalSeleccionUsuarios" tabindex="-1" aria-labelledby="modalSeleccionUsuariosLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSeleccionUsuariosLabel">Seleccionar Usuarios</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <label for="lista-usuarios" class="form-label">Usuarios disponibles:</label>
                    <select id="lista-usuarios" name="usuarios[]" multiple class="form-select" size="10"></select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="confirmarSeleccion()">Confirmar Selección</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../js/notificaciones.js" defer></script>
    <script>
        if (window.location.search.includes('success')) {
            document.querySelector('form').reset();
            document.getElementById('preguntaTodos').style.display = 'none';
        }

        const modalSeleccionUsuarios = document.getElementById('modalSeleccionUsuarios');
        modalSeleccionUsuarios.addEventListener('hidden.bs.modal', function () {
            document.getElementById('lista-usuarios').innerHTML = '';
        });
    </script>

</body>

</html>
