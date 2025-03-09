<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="../../js/alumnos.js" defer></script>

    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mantenimiento de alumnos</title>
    <link rel="stylesheet" href="../../CSS/estilohojacontacto.css">



    <style>
        legend {
            font-family: 'Franklin Gothic Medium';
            font-size: 36px;
            color: rgb(246, 248, 247);
            text-shadow: 5px 10px 18px #a7add6;
            text-align: center;
        }

        .division1 {
            width: 95%;
            margin-right: 2.5%;
            margin-left: 2.5%;
            height: 500px;
        }
    </style>
    

</head>

<body background-color="f7f2dc">
    <?php
    // Comprobamos que el registro a insertar no esté duplicado
    
    if (isset($_GET['error']) || isset($_GET['success'])) {
        $mensaje = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : htmlspecialchars($_GET['success']);
        $tipo = isset($_GET['error']) ? 'alert-danger' : 'alert-success';
        echo "
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Obtener el cuerpo del modal
            const mensajeModal = document.getElementById('mensajeModalBody');
            // Limpiar clases anteriores
            mensajeModal.className = ''; // Limpia todas las clases
            // Agregar la clase modal-body y el tipo (alert-success o alert-danger)
            mensajeModal.classList.add('modal-body', '$tipo');
            // Asignar el mensaje
            mensajeModal.textContent = '$mensaje';
            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('mensajeModal'));
            modal.show();
        });
    </script>";
    }
    ?>
    <!-- Modal para mensajes de error -->
    <div class="modal fade" id="mensajeModal" tabindex="-1" aria-labelledby="mensajeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mensajeModalLabel">Información</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="mensajeModalBody">
                    <!-- El contenido del mensaje se rellenará dinámicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- fin de Modal para mensajes de error -->

    <form action="../../mainpage.php?route=storeAlumno" method="post">

        <div class="division1">
            <fieldset>

                <div class="row g-5 align-items-right">
                    <legend class="mb-3">
                        <span class="titulo"> - Datos personales del Alumno - </span>
                    </legend>
                </div>

                <div class="row g-12 align-items-start">
                    <div class="row align-items-center">
                        <div class="col text-end">
                            <input type="text" class="form-control d-inline w-25 me-2" id="cadenaBuscar"
                                placeholder="Buscar..." />
                        </div>

                        <div class="col-auto d-flex flex-column radio-compact" style="margin-left: 20px;">

                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="dni" name="criterio" value="dni" />
                                <label for="nombre" class="form-check-label">DNI</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="apellido" name="criterio"
                                    value="apellido" />
                                <label for="apellido" class="form-check-label">Apellido</label>
                            </div>
                        </div>
                        <!-- Botón que abre el modal -->
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary ms-2" data-bs-toggle="modal"
                                data-bs-target="#modalBuscar" onclick="prellenarModal()" style="margin-left: 1%;">
                                Buscar
                            </button>

                        </div>
                    </div>
                </div>




                <!-- Modal  para la busqueda -->
                <div class="modal fade" id="modalBuscar" tabindex="-1" aria-labelledby="modalBuscarLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalBuscarLabel">Buscar Registro</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Formulario de búsqueda -->
                                <form id="formBuscar">
                                    <div class="mb-3">
                                        <label for="criterioBusqueda" class="form-label">Criterio de búsqueda:</label>
                                        <input type="text" id="criterioBusqueda" class="form-control"
                                            placeholder="Ingrese criterio...">
                                    </div>
                                    <button type="button" class="btn btn-primary"
                                        onclick="buscarRegistros()">Buscar</button>
                                </form>

                                <!-- Tabla de resultados -->
                                <table class="table table-bordered mt-3">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaResultados">
                                        <!-- Los datos se llenarán dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- fin de Modal para busquedas -->





                <div class="row g-3 align-items-center">
                    <!-- Nombre -->
                    <div class="col d-flex align-items-baseline">
                        <label for="nombre" class="form-label me-3 label-arriba"><b>Nombre</b></label>
                        <input id="nombre" type="text" name="nombre" class="w-50" maxlength="75" required
                            onchange="comprueba(this)" onblur="comprueba(this)">
                    </div>

                    <!-- Apellido 1 -->
                    <div class="col d-flex align-items-baseline">
                        <label for="apellido1" class="form-label me-3 label-arriba"><b>Apellido 1</b></label>
                        <input id="apellido1" type="text" name="apellido1" class="w-75" maxlength="75" required
                            onchange="comprueba(this)" onblur="comprueba(this)">
                    </div>

                    <!-- Apellido 2 -->
                    <div class="col d-flex align-items-baseline">
                        <label for="apellido2" class="form-label me-3 label-arriba"><b>Apellido 2</b></label>
                        <input id="apellido2" type="text" name="apellido2" class="w-75" maxlength="75" required
                            onchange="comprueba(this)" onblur="comprueba(this)">
                    </div>
                    <div class="col d-flex align-items-baseline">
                        <label for="id_alumno" class="form-label me-3 label-arriba"><b> DNI/NIE </b></label>
                        <input id="id_alumno" type="text" value="" name="id_alumno" title="formato 999999999X"
                            class="w-25" size="9" maxlenght="9" pattern="[0-9]{8}[A-Z]{1}" onchange="comprueba(this)"
                            onblur="comprueba(this)" />
                        </label>
                    </div>
                </div>

                <div class="col-12 d-flex align-items-baseline">
                    <label for="direccion" class="form-label me-3 label-arriba"><b>Dirección (opcional)</b></label>
                    <input id="direccion" type="text" name="direccion" class="w-75" maxlength="200"
                        onchange="comprueba(this)" onblur="comprueba(this)">
                </div>

                <div class="row g-3 align-items-center">
                    <!-- Poblacion -->
                    <div class="col d-flex align-items-baseline">
                        <label for="poblacion" class="form-label me-3 label-arriba"><b>Poblacion</b></label>
                        <input id="poblacion" type="text" name="poblacion" class="w-50" maxlength="75" required
                            onchange="comprueba(this)" onblur="comprueba(this)">
                    </div>

                    <!--  Provincia -->
                    <div class="col d-flex align-items-baseline">
                        <label for="provincia" class="form-label me-3 label-arriba"><b>Provincia</b></label>
                        <input id="provincia" type="text" name="provincia" class="w-75" maxlength="75" required
                            onchange="comprueba(this)" onblur="comprueba(this)">
                    </div>

                    <!-- Codigo Postal-->
                    <div class="col d-flex align-items-baseline">
                        <label for="cpostal" class="form-label me-3 label-arriba"><b>Codigo Postal</b></label>
                        <input id="cpostal" type="text" name="cpostal" class="w-15" maxlength="5" required
                            onchange="comprueba(this)" onblur="comprueba(this)">
                    </div>
                </div>


                <div class="row g-3 align-items-center">
                    <div class="col d-flex align-items-baseline">
                        <label><b> Fecha de nacimiento </b>
                            <input id="fechanac" type="date" value="" name="fechanac" onchange="comprueba(this)"
                                onblur="comprueba(this)" /></label>
                    </div>

                    <div class="col d-flex align-items-baseline">
                        <label for="Phone" class="form-label me-3 label-arriba"><b> Telefono </b>
                            <input type="tel" id="Phone" name="Phone" title="formato 999999999" pattern="[0-9]{9}"
                                onblur="comprueba(this)" required />
                        </label>
                    </div>

                    <div class="col d-flex align-items-baseline">
                        <label><b> e-mail </b>
                            <input id="mail" type="email" name="mail" title="formato eeeeeee@eeee.ee"
                                onchange="return ValidarMail(this)" required /><br></label>
                    </div>

                </div>
                <div class="row g-3 align-items-center">
                    <!-- Nivel de estudios -->
                    <div class="col d-flex">
                        <label for="estudios" class="form-label label-center me-3"><b>Nivel de estudios:</b></label>
                        <select class="form-select control-offset w-auto" name="estudios" id="estudios">
                            <option value="Sin estudios">Sin estudios</option>
                            <option value="E.S.O o similar">E.S.O o similar</option>
                            <option value="Bachiller">Bachiller</option>
                            <option value="F.P Básica">F.P Básica</option>
                            <option value="F.P Grado Medio">F.P Grado Medio</option>
                            <option value="F.P Grado Superior">F.P Grado Superior</option>
                            <option value="Diplomatura Universitaria">Diplomatura Universitaria</option>
                            <option value="Licenciatura">Licenciatura</option>
                            <option value="Doctorado">Doctorado</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>

                    <!-- Fecha de alta -->
                    <div class="col d-flex align-items-baseline">
                        <label for="fechalta" class="form-label label-center me-3"><b>Fecha de alta:</b></label>
                        <input id="fechalta" type="date" value="" name="fechalta" class="control-offset w-auto" />
                    </div>

                    <div class="col d-flex">
                        <label class="form-label label-center me-3"><b>Estado:</b></label>
                        <div class="form-check control-offset">
                            <input class="form-check-input" type="checkbox" id="estado" value="alta"
                                onchange="toggleEstado()">
                            <label class="form-check-label" for="estado" id="estado-label">Alta</label>
                        </div>
                    </div>

                    <!-- Campo oculto para la fecha de baja -->
                    <input type="hidden" name="id_usuario" value="4">

                    <input type="hidden" name="fechabaja" id="fechabaja">
                    <!-- Campo oculto para la diferenciar insercion o actualización -->
                    <input type="hidden" id="accion" name="accion" value="insert">


                </div>
                <div id="estilodatos3">
                    <!-- Botón para Insertar -->
                    <button type="submit" name="accion" value="insert" class="boton_personalizado1">Insertar</button>

                    <!-- Botón para Actualizar -->
                    <button type="submit" name="accion" value="update" class="boton_personalizado1">Modificar</button>

                    <!-- Botón para Volver -->
                    <button type="button" class="boton_personalizado1"
                        onclick="location.href='../../mainpage.php'">Volver</button>


                    <!--<button type="button" class="boton_personalizado1"
                        onclick="location.href='mainpage.php'">Volver</button> -->
                </div>


        </div>



    </form>
    <!--controla el estado-->

    <script src="../../js/estado.js"></script>

    <!-- Incluye Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</body>

</html>