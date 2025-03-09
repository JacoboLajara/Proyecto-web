<!DOCTYPE html>
<html lang="es">



<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baja Matriculaciónes de Alumnos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="stylesheet" href="../../CSS/formularios.css" />
</head>


<body class="bg-light">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Panel de control de aulas</h2>
        <ul>
            <li><a href="../../mainpage.php"><i class="fas fa-home"></i> Inicio</a></li>
            <li>
                <a href="/mainpage.php?route=createMatricula"><i class="fas fa-clipboard-list"></i> Volver a Matrículas</a>

            </li>
            <li>
                <!-- Enlace para modificar matriculas -->
                <a href="/mainpage.php?route=bajaAlumnos"><i class="fas fa-user-minus"></i> Baja Matrículas</a>
            </li>

            </li>

            <li><a href="#"><i class="fas fa-bell"></i> Ver Notificaciones</a></li>
            <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <div class="header">
            <h1>Baja de Matriculas</h1>
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
            <h2 class="mb-4 text-center">Baja de matriculas de Alumnos</h2>
            <div class="card p-4 shadow">
                <form id="bajaAlumnosForm">
                    <div class="mb-3">
                        <label for="id_curso" class="form-label">Selecciona un curso</label>
                        <select class="form-control" id="id_curso" required>
                            <option value="">Seleccione un curso</option>
                        </select>
                    </div>

                    <div id="alumnosContainer" class="d-none">
                        <h5>Alumnos Matriculados:</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Seleccionar</th>
                                    <th>Nombre</th>
                                    <th>Apellido 1</th>
                                    <th>Apellido 2</th>
                                    <th>Fecha Matrícula</th>
                                </tr>
                            </thead>
                            <tbody id="alumnosTableBody">
                                <!-- Los alumnos se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" class="btn btn-danger mt-3">Dar de Baja</button>
                </form>
                <div id="responseMessage" class="mt-3"></div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="/../../js/bajaAlumnos.js"></script>
</body>

</html>