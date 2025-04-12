<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Bootstrap y otros recursos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="../../js/personal.js" defer></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Mantenimiento de personal no docente</title>
    <link rel="stylesheet" href="../../CSS/estilohojacontacto.css" />
    <link rel="stylesheet" href="../../CSS/formularios.css" />

</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Panel de Personal no Docente</h2>
        <ul>
            <li><a href="./views/users/backoffice.php"><i class="fas fa-home"></i> Volver al panel central</a></li>
            <li>
                <a href="#" data-bs-toggle="modal" data-bs-target="#modalBuscar" onclick="prellenarModal()">
                    <i class="fas fa-search"></i> Buscar Personal no docente
                </a>
            </li>
            <li>
                <!-- Enlace para insertar profesor -->
                <a href="#" onclick="mostrarInsertarPersonal()">
                    <i class="fas fa-plus"></i> Insertar Personal no docente
                </a>
            </li>

            <!-- Enlace para modificar profesor -->
            <li id="modificarPersonalLi" style="display: none;">
                <a href="#" onclick="mostrarModificarPersonal()">
                    <i class="fas fa-edit"></i> Modificar Personal no docente
                </a>
            </li>
            <li>
                <a href="/listados/listadosPersonal.php" target="_blank">
                    <i class="fas fa-list"></i> Listado de todo el personal no docente
                </a>
            </li>
            <li>
                <a id="verFichaPersonal" href="#" target="_blank" class="btn btn-primary" style="display: none;">
                    Generar Ficha del Personal
                </a>
            </li>


            <!-- <li><a href="#"><i class="fas fa-bell"></i> Ver Notificaciones</a></li> -->
            <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <div class="header">
            <h1>Mantenimiento de personal no docente</h1>
        </div>
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

        <form id="formPersonal" action="../../mainpage.php?route=storePersonal" method="post">
            <div class="division1">
                <fieldset>

                    <div class="row g-5 align-items-right">
                        <legend class="mb-3">
                            <span class="titulo"> - Datos personal no docente - </span>
                        </legend>
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
                                            <label for="criterioBusqueda" class="form-label">Criterio de búsqueda: se
                                                podrá buscar uno de estos criterios DNI, Apellido1, Apellido2,
                                                Nombre</label>
                                            <input type="text" id="criterioBusqueda" class="form-control"
                                                placeholder="Ingrese criterio..." />
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
                                                <th>Apellido1</th>
                                                <th>Apellido2</th>
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
                            <label for="id_personal" class="form-label me-3 label-arriba"><b> DNI/NIE </b></label>
                            <input type="text" value="" name="id_personal" id="id_personal" title="formato 999999999X"
                                class="w-25" size="9" maxlenght="9" pattern="[0-9]{8}[A-Z]{1}"
                                onchange="comprueba(this)" onblur="comprueba(this)" />
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
                                <input type="date" id="fechanac" value="" name="fechanac" onchange="comprueba(this)"
                                    onblur="comprueba(this)" /></label>
                        </div>

                        <div class="col d-flex align-items-baseline">
                            <label><b> Telefono </b>
                                <input type="tel" id="Phone" name="Phone" title="formato 999999999" pattern="[0-9]{9}"
                                    required onchange="comprueba(this)" onblur="comprueba(this)" /></label>
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
                            <label class="form-label label-center me-3"><b>Fecha de alta:</b></label>
                            <input type="date" value="" name="fechalta" id="fechalta" class="control-offset w-auto" />
                        </div>

                        <!-- Estado -->
                        <div class="col d-flex">
                            <label class="form-label label-center me-3"><b>Estado:</b></label>
                            <div class="form-check control-offset">
                                <input class="form-check-input" type="checkbox" id="estado" value="baja">
                                <label class="form-check-label" for="estado" id="estado-label">Alta</label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="id_usuario" value="2">
                    <!-- Campo oculto para la diferenciar insercion o actualización -->
                    <input type="hidden" id="accion" name="accion" value="insert">
                    <div class="d-flex justify-content-end">
                        <!-- Botón para Insertar: se muestra solo en modo insertar -->
                        <button id="btnInsertar" type="submit" name="accion" value="insert" class="btn btn-success me-2"
                            style="display:none;">Insertar Personal</button>
                        <!-- Botón para Modificar: se muestra solo en modo modificar -->
                        <button id="btnModificar" type="submit" name="accion" value="update"
                            class="btn btn-warning me-2" style="display:none;">Modificar Personal</button>
                        <button type="button" class="btn-volver"
                            onclick="location.href='../../mainpage.php'">Volver</button>
                    </div>

                </fieldset>
            </div>

        </form>

        <script>
            // Detectar cambios en el checkbox
            document.getElementById('estado').addEventListener('change', function () {
                const estadoLabel = document.getElementById('estado-label');
                if (this.checked) {
                    estadoLabel.textContent = 'Baja'; // Cambiar texto a "Baja"
                } else {
                    estadoLabel.textContent = 'Alta'; // Cambiar texto a "Alta"
                }
            });
        </script>

        <!-- Incluye Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</body>

</html>