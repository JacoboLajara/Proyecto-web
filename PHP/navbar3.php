<?php
// Detectar la ruta actual
$current_page = basename($_SERVER['PHP_SELF']);

// Definir rutas relativas según la ubicación del archivo actual
$base_path = (strpos($_SERVER['PHP_SELF'], '/views/users/') !== false) ? '../../' : './';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menú de Navegación</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

    <style>
        .navbar {
            background-color: rgb(50, 6, 245) !important;
            /* Color de fondo */
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
            /* Texto en blanco */
        }

        .navbar-toggler {
            border: 1px solid white;
            /* Borde blanco para el botón */
        }

        .navbar-toggler-icon {
            background-color: white;
            /* Icono blanco */
        }

        .navbar-collapse {
            background-color: rgb(50, 6, 245);
            /* Fondo del menú desplegable */
            padding: 20px;
            /* Espaciado interno */
        }

        .menu-column {
            margin: 10px;
            /* Espaciado entre columnas */
        }

        .menu-column h5 {
            color: white;
            /* Color del título de la columna */
            margin-bottom: 15px;
            /* Espaciado debajo del título */
        }

        .menu-column .nav-link {
            padding: 5px 0;
            /* Espaciado entre opciones */
        }
    </style>
</head>

<body>

    <!-- Barra de navegación -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <!-- Enlace a la página principal -->
            <a class="navbar-brand" href="<?= $base_path ?>mainpage.php">Inicio</a>

            <!-- Botón de menú hamburguesa -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Contenido del menú -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="row">
                    <!-- Columna 1: Personas -->
                    <div class="col-md-4 menu-column">
                        <h5>Personas</h5>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link <?= ($current_page == 'usuarios.php') ? 'active' : '' ?>"
                                    href="<?= $base_path ?>views/users/usuarios.php">Usuarios</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $base_path ?>views/users/profesores.php">Profesores</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $base_path ?>views/users/personal.php">Personal no
                                    docente</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $base_path ?>views/users/alumnos.php">Alumnos</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Columna 2: Cursos -->
                    <div class="col-md-4 menu-column">
                        <h5>Cursos</h5>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link <?= ($current_page == 'cursos.php') ? 'active' : '' ?>"
                                    href="<?= $base_path ?>views/users/cursos.php">Gestión de cursos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $base_path ?>views/courses/gestion_aulas.php">Gestión de
                                    aulas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $base_path ?>views/courses/gestion_horarios.php">Gestión
                                    de horarios</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $base_path ?>views/users/notas.php">Gestión de notas</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Columna 3: Otras opciones (opcional) -->
                    <div class="col-md-4 menu-column">
                        <h5>Otras opciones</h5>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $base_path ?>views/other/opcion1.php">Opción 1</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $base_path ?>views/other/opcion2.php">Opción 2</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const navbarToggler = document.querySelector('.navbar-toggler');
            const navbarCollapse = document.querySelector('.navbar-collapse');

            if (!navbarToggler) {
                console.error("No se encontró el botón de la hamburguesa");
            }

            if (!navbarCollapse) {
                console.error("No se encontró el menú colapsable");
            }

            navbarToggler.addEventListener("click", function () {
                console.log("Menú hamburguesa clickeado");
                console.log("Estado del menú:", navbarCollapse.classList.contains('show') ? 'Abierto' : 'Cerrado');
            });
        });

        var base_path = "<?= $base_path ?>";

    </script>

</body>

</html>