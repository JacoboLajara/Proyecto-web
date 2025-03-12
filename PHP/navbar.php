<div class="navbar">
    <a href="#home">Quiénes somos</a>
    
    <!-- Menú desplegable: Productos y Servicios -->
    <div class="dropdown">
        <button class="dropbtn">Productos y Servicios <i class="fa fa-caret-down"></i></button>
        <div class="dropdown-content">
            <div class="header">
                <h2>Gestión de Usuarios y personal</h2>
            </div>
            <div class="row">
                <div class="column">
                    <h3>Personal</h3>
                    <a href="mainpage.php?route=createUser" target="_blank">Alta Usuario</a>
                    <a href="mainpage.php?route=createAlumno" target="_blank">Alta Alumno</a>
                    <a href="mainpage.php?route=createProfesor" target="_blank">Alta Profesores</a>
                    <a href="mainpage.php?route=createPersonal" target="_blank">Alta Personal</a>
                    <a href="formulario_de_contacto.php">Contáctanos</a>
                </div>
                <div class="column">
                    <h3>Gestión de cursos</h3>
                    <a href="mainpage.php?route=createCurso" target="_blank">Alta Curso</a>
                    <a href="mainpage.php?route=createMatricula" target="_blank">Matrículas</a>
                    <a href="#">Poster y 3D</a>
                    <a href="#">Flyings</a>
                </div>
                <div class="column">
                    <h3>Aulas y Horarios</h3>
                    <a href="mainpage.php?route=createAula" target="_blank">Gestión Aulas</a>
                    <a href="#">Personalizamos tus items</a>
                    <a href="#">Otros</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Menú desplegable: Horario y disponibilidad -->
    <div class="dropdown">
        <button class="dropbtn">Horario y disponibilidad <i class="fa fa-caret-down"></i></button>
        <div class="dropdown-content">
            <div class="header">
                <h2>Horarios y disponibilidad</h2>
            </div>
            <div class="row">
                <div class="column">
                    <h3>Horarios</h3>
                    <a href="#">Horario de atención</a>
                    <a href="#">Disponibilidad de aulas</a>
                    <a href="#">Calendario de cursos</a>
                </div>
                <div class="column">
                    <h3>Reservas</h3>
                    <a href="#">Reservar aula</a>
                    <a href="#">Consultar disponibilidad</a>
                    <a href="#">Cancelar reserva</a>
                </div>
                <div class="column">
                    <h3>Otros</h3>
                    <a href="#">Políticas de uso</a>
                    <a href="#">Preguntas frecuentes</a>
                    <a href="#">Contacto para reservas</a>
                </div>
            </div>
        </div>
    </div>

    <a href="formulario_de_contacto.php">Contacto</a>
    <a href="#home">Portafolio</a>
    <a href="formulario_de_contacto.php">Regístrate</a>

    <?php
    if ($_SESSION['contador'] == 0) {
        echo "<div class='column_1'>
                <form action='iniciosesion.php' method='Post'>
                    <input name='enviar' type='submit' value='Iniciar sesión'>
                </form>
              </div>";
    } else {
        echo "<div class='column_1'>
                <p>Bienvenido : " . htmlspecialchars($_SESSION['nombre_envio']) . '</p>
              </div>';
    }
    ?>
</div>