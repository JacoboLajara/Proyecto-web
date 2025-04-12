<?php
require_once __DIR__ . '/../../init.php';  // Ruta ajustada para incluir init.php
require_once __DIR__ . '/../../config/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Ayuda - Sistema de Gestión</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <link rel="stylesheet" href="../../CSS/formularios.css" />
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }

        .container {
            margin-top: 20px;
        }

        .faq-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .faq-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .faq-search {
            margin-bottom: 20px;
        }

        .sidebar a {
            color: white !important;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>📖 Centro de Ayuda </h2>
        <p class="text-white text-center">Estás en la sección de ayuda</p>

        <?php if (isset($_SESSION['usuario']) && isset($_SESSION['rol'])): ?>
            <?php if ($_SESSION['rol'] === 'Alumno'): ?>
                <li><a href="../../views/users/backAlumnos.php"><i class="fas fa-arrow-left"></i> Volver al Panel de Alumno</a>
                </li>
            <?php else: ?>
                <li><a href="../../views/users/backoffice.php"><i class="fas fa-arrow-left"></i> Volver al Panel Central</a>
                </li>
            <?php endif; ?>
        <?php else: ?>
            <li><a href="../../login.php"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a></li>
        <?php endif; ?>

        <hr>

        <h3 class="text-white text-center">📞 Soporte Técnico</h3>
        <p class="text-white text-center"><i class="fas fa-phone"></i> Teléfono: +34 123 456
            789</p>
        <p class="text-white text-center"><i class="fas fa-envelope"></i> Email: <a
                href="mailto:soporte@gestacademia.com" class="text-white">soporte@academia.com</a></p>
    </div>


    <!-- Contenido principal -->
    <div class="content">
        <div class="header">
            <h1>Centro de Ayuda y Preguntas Frecuentes</h1>
        </div>

        <div class="container">

            <div class="faq-search">
                <input type="text" id="buscadorFaq" class="form-control" placeholder="🔍 Buscar en la ayuda..."
                    onkeyup="filtrarPreguntas()">
            </div>

            <div class="accordion" id="faqAccordion">
                <!-- Categoría: Usuarios -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqUsuarios">
                            👤 Administradores y personal no docente
                        </button>
                    </h2>
                    <div id="faqUsuarios" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <ul class="list-group">
                            <li class="list-group-item">
                            <strong>¿Cómo gestiono usuarios?</strong><br>
                                    Accediendo desde el panel a la Gestión de usuarios (solo Administradores) seleccionando usuario la eliminación o cambio de contraseña, si se elimina el 
                                    usuario debe iniciar sesión y se le redirigirá a cambiar contraseña para  que introduzca la nueva y pueda entrar 
                                    a la aplicación según su rol (opción recomendada). Si la introduce el administrador tendrá que comunicársela de alguna forma.
                                </li>
                                <li class="list-group-item">
                                    <strong>¿Cómo gestiono Alumnos?</strong><br>
                                    para dar de alta a un alumno primero hemos de pulsar en la sidebar el enlace insertar alumno.A partir de ahí se habilitará el panel de inserción de datos 
                                    y se podrá interactuar con la aplicación, introducimos los datos y pulsamos el botón de insertar, si todo ha ido bien nos lo notificará.
                                    Si lo que se quiere es modificar en el mismo panel primero hemos de “Buscar Alumno”, seleccionar el alumno que buscamos y una vez cargados sus datos en pantalla 
                                    pulsamos en la sidebar “Modificar Alumno” para que active el panel de datos y podamos modificar, una vez finalizada la modificación pulsaremos el botón “Modificar Alumno” que aparece al final de la pantalla.
                                    Listados: dos tipos un listado general y un listado detalle de alumno si este se encuentra cargado en pantalla, para cargar el alumno, primero Buscar Alumno, seleccionarlo y aparecerá 
                                    la opción de Generar ficha de alumno.

                                </li>
                                <li class="list-group-item">
                                    <strong>¿Cómo gestiono Profesores?</strong><br>
                                    para dar de alta a un profesor primero hemos de pulsar en la sidebar el enlace insertar profesor. A partir de ahí se habilitará el panel de inserción de datos y se podrá interactuar con la aplicación, 
                                    introducimos los datos y pulsamos el botón de insertar, si todo ha ido bien nos lo notificará. 
                                    Si lo que se quiere es modificar en el mismo panel primero hemos de “Buscar Profesor”, seleccionar el profesor que buscamos y una vez cargados sus datos en pantalla pulsamos en la sidebar “Modificar Profesor” 
                                    para que active el panel de datos y podamos modificar, una vez finalizada la modificación pulsaremos el botón “Modificar Profesor” que aparece al final de la pantalla.
                                    Listados: dos tipos un listado general y un listado detalle de alumno si este se encuentra cargado en pantalla, para cargar el alumno, primero Buscar Profesor, seleccionarlo y aparecerá la opción de Ficha detalle de profesor.

                                </li>
                                <li class="list-group-item">
                                    <strong>¿Cómo gestiono personal_no_docente?</strong><br>
                                    Para dar de alta a personal no docente primero hemos de pulsar en la sidebar el enlace insertar personal. A partir de ahí se habilitará el panel de inserción de datos y se podrá interactuar con la aplicación, 
                                    introducimos los datos y pulsamos el botón de insertar, si todo ha ido bien nos lo notificará.Si lo que se quiere es modificar en el mismo panel primero hemos de “Buscar Personal no docente”, 
                                    seleccionar la figura que buscamos y una vez cargados sus datos en pantalla pulsamos en la sidebar “Modificar personal” para que active el panel de datos y podamos modificar, una vez finalizada la modificación pulsaremos 
                                    el botón “Modificar personal” que aparece al final de la pantalla.Listados:Tenemos un listado general de personal no docente
                                </li>
                                <li class="list-group-item">
                                    <strong>¿Cómo gestiono cursos?</strong><br>
                                    o	Gestión de cursos, seleccionamos Gestion de cursos en el panel y nos lleva a la pantalla de cursos, allí introducimos los datos del curso y guardamos, si el curso es modular señalamos el check de “es modular” y 
                                    aparecerá otra tabla en la que introduciremos los módulos, si el módulo tiene unidades formativas seleccionamos “Añadir unidades formativas” y guardamos el módulo, una vez hecho esto aparecerá otra tabla en la que podemos 
                                    ir introduciendo las unidades formativas, estas las podremos introducir todas y una vez introducidas pulsamos “Guardar unidades”.
                                    o	Importante ir guardando cada categoría antes de introducir datos en la siguiente, si queremos introducir módulos primero guardar curso, si queremos introducir unidades formativas primero guardar módulos.
                                    o	Para modificar un curso, seleccionamos consultar curso y en el cuadro que aparece seleccionamos el curso a modificar, una vez hecho esto seguir las indicaciones de pantalla
                                </li>
                                <li class="list-group-item">
                                    <strong>¿Cómo gestiono Aulas?</strong><br>
                                    Utilizamos un procedimiento similar al de introducción de alumnos, profesores y personal.
                                </li>
                                <li class="list-group-item">
                                    <strong>¿Cómo gestiono la matriculación de alumos?</strong><br>
                                    -	Matriculas, es necesario que los alumnos estén matriculados en sus cursos para poder gestionar notas, para matricular hemos de seleccionar el curso en el desplegable que aparece al inicio y una vez hecho nos aparecerá el listado de alumnos, 
                                    simplemente hemos de ir seleccionando alumnos y pulsar el botón de matricular.
                                    - Para dar de baja a un alumno,  seleccionamos Baja Matricula, procedimiento similar al anterior, seleccionamos curso y aparece el listado de alumnos matriculados, seleccionamos alumno/s a dar de baja del curso y pulsamos “Dar de baja”

                                </li>
                                <li class="list-group-item">
                                    <strong>¿Cómo gestiono las notas de los alumos?</strong><br>
                                    -	Gestión de notas, cuando se acceda a esta página se activa un desplegable en el que seleccionaremos el curso, una vez seleccionado aparecen los alumnos matriculados en el mismo, seleccionamos el alumno y aparecerán los módulos y unidades formativas 
                                    en los que están matriculados, las notas hay que ponerlas en las unidades formativas, la nota del módulo es la media de las notas.No está implantado la nota de curso sin módulo ni unidades al no existir en este momento cursos no modulares.

                                </li>
                                <li class="list-group-item">
                                    <strong>¿Cómo gestiono los horarios de aulas y cursos?</strong><br>
                                    - Seleccionar Asignar Horarios, para la asignación de horarios seleccionaremos Aula y curso y en el cuadro de horas de abajo iremos introduciendo los horarios, los horarios se pueden introducir por periodos horarios sin limitación, 
                                    si un horario está ocupado se verá un mensaje de que ese horario ya se encuentra ocupado y nos insta a consultar disponibilidad.
                                    -	La modificación de horario se hará en la misma pantalla seleccionando aula y curso y modificando la tabla de horas.
                                </li>
                                <li class="list-group-item">
                                    <strong>¿Cómo gestiono recibos?</strong><br>
                                    -Seleccionando Gestión de Recibos, los recibos se generarán automáticamente al inicio de cada uno de los meses, en esta pantalla gestionaremos si están o no pagados y anulaciones.
                                </li>
                                <li class="list-group-item">
                                    <strong>¿Cómo envio notificaciones?</strong><br>
                                    -En Gestión de notificaciones, aquí podemos gestionar las notificaciones y a quien se les envía, podrán enviarse a todos o alguno de los alumnos, profesores, personal y curso, según instrucciones de pantalla.
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Categoría: Alumnos -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqAlumnos">
                            🎓 Alumnos
                        </button>
                    </h2>
                    <div id="faqAlumnos" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>¿Cómo me inscribo en un curso?</strong><br>
                                    Actualmente no puedes inscribirte desde la web para solicitar la inscripción debes
                                    enviar un correo a <strong>formacion@gestacademia.com</strong> , y nos pondremos en
                                    contacto contigo para formalizar tu inscripción.
                                </li>
                                <li class="list-group-item">
                                    <strong>¿Dónde puedo ver mis cursos inscritos?</strong><br>
                                    En tu panel de alumno podrás ver los cursos en los que te encuentras inscrito.
                                </li>
                                <li class="list-group-item">
                                    <strong>¿Cómo consultar mis notas?</strong><br>
                                    Toda la información referente al alumno se encuentra en el panel del alumno, a el
                                    accedes directamente al loguearte.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Categoría: Profesores -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqProfesores">
                            👨‍🏫 Profesores
                        </button>
                    </h2>
                    <div id="faqProfesores" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>¿Cómo asigno notas a los alumnos?</strong><br>
                                    En tu panel de profesor puedes registrar calificaciones en "Gestión de notas".
                                </li>
                                <li class="list-group-item">
                                    <strong>¿Cómo gestionar los cursos?</strong><br>
                                    No tienes acceso a gestionar las opciones disponibles de cursos, si necesitas
                                    cualquier mdificación has de ponerte en contacto con
                                    <strong>soporte@gestacademia.com</strong> o con
                                    <strong>administracion@gestacademia.com</strong>.
                                </li>
                                <li class="list-group-item">
                                    <strong>¿Cómo consultar mis horarios?</strong><br>
                                    Los horarios están disponibles en la sección "Horario".
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Categoría: Documentación -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqDocumentos">
                            📄 Documentación
                        </button>
                    </h2>
                    <div id="faqDocumentos" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>¿Cómo descargo mis certificados y diplomas?</strong><br>
                                    En tu panel de alumno estarán disponibles tus certificados y diplomas, si necesitas
                                    algún otro tipo de documento has de contactar con
                                    <strong>administracion@tudominio.com</strong>.
                                </li>
                                <li class="list-group-item">
                                    <strong>¿Cómo puedo obtener un duplicado de mi diploma?</strong><br>
                                    Los diplomas y certificados se encuentran disponibles en tu panel, si hay algún
                                    problema con ellos contacta con la administración
                                    <strong>administracion@gestacademia.com</strong> para solicitar un duplicado.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Categoría: Soporte Técnico -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqSoporte">
                            ⚙️ Soporte Técnico
                        </button>
                    </h2>
                    <div id="faqSoporte" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>¿Qué hacer si tengo problemas de acceso?</strong><br>
                                    Tienes que contactar con soporte técnico enviando un correo a
                                    <strong>soporte@gestacademia.com</strong> o llamando al <strong>+34 123 456
                                        789</strong> (opción más rapida).
                                </li>
                                <li class="list-group-item">
                                    <strong>¿Cómo contactar con soporte técnico?</strong><br>
                                    Puedes enviar un correo a <strong>soporte@gestacademia.com</strong> o llamar al
                                    <strong>+34 123 456 789</strong>.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <script>
            function filtrarPreguntas() {
                let input = document.getElementById("buscadorFaq").value.toLowerCase();
                let preguntas = document.querySelectorAll(".list-group-item");

                preguntas.forEach(pregunta => {
                    let texto = pregunta.innerText.toLowerCase();
                    if (texto.includes(input)) {
                        pregunta.style.display = "";
                    } else {
                        pregunta.style.display = "none";
                    }
                });
            }
        </script>

</body>

</html>