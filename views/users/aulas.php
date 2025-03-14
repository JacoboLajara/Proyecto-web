<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Bootstrap y otros recursos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="../../js/aulas.js" defer></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Mantenimiento de aulas</title>
    <link rel="stylesheet" href="../../CSS/estilohojacontacto.css" />
    <link rel="stylesheet" href="../../CSS/formularios.css" />

</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Panel de control de aulas</h2>
        <ul>
        <li><a href="/views/users/backoffice.php"><i class="fas fa-home"></i> Volver al panel central</a></li>

            <li>
                <a href="#" data-bs-toggle="modal" data-bs-target="#modalBuscar" onclick="prellenarModal()">
                    <i class="fas fa-search"></i> Buscar Aula
                </a>
            </li>
            <li>
                <!-- Enlace para insertar aulas -->
                <a href="#" onclick="mostrarInsertarAulas()">
                    <i class="fas fa-plus"></i> Insertar aula
                </a>
            </li>
            <li id="modificarAulaLi" style="display: none;">
                <!-- Enlace para modificar profesor -->
                <a href="#" onclick="mostrarModificarAulas()">
                    <i class="fas fa-edit"></i> Modificar aula
                </a>
            </li>
            <li>
                <a href="/listados/listadosAulas.php" target="_blank">
                    <i class="fas fa-list"></i> Listado de todas las aulas
                </a>
            </li>
            <li><a href="#"><i class="fas fa-bell"></i> Ver Notificaciones</a></li>
            <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <div class="header">
            <h1>Mantenimiento de Aulas</h1>
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

        <form id="formAulas" action="../../mainpage.php?route=storeAulas" method="post">
            <div class="division1">
                <fieldset>

                    <div class="row g-5 align-items-right">
                        <legend class="mb-3">
                            <span class="titulo"> - Datos del Aula - </span>
                        </legend>
                    </div>

                    <div class="row g-5 align-items-center">
                        <!-- Input de búsqueda alineado a la derecha 
                    <div class="col text-end">
                        <input type="text" class="form-control d-inline w-15 me-2" placeholder="Buscar..." />
                    </div>

                    Radio Buttons uno encima del otro 
                    <div class="col-auto d-flex flex-column">
                         Radio Button 1 
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="capacidad" name="criterio"
                                value="capacidad" />
                            <label for="capacidad" class="form-check-label">Capacidad</label>
                        </div>

                        Radio Button 2 
                        <div class="form-check mt-1">
                            <input type="radio" class="form-check-input" id="nombre" name="criterio" value="nombre" />
                            <label for="nombre" class="form-check-label">Nombre</label>
                        </div>
                    </div>-->
                        <!-- Botón que abre el modal -->
                        <div class="col-auto">

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
                                            <label for="criterioBusqueda" class="form-label">Criterio de
                                                búsqueda:</label>
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
                                                <th>Capacidad</th>
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

                    <!-- Bloque 1º fila - ID y CAPACIDAD -->
                    <div class="row g-5 align-items-center">
                        <div class="col-3  align-items-baseline">
                        </div>
                        <!-- Id Aula -->
                        <div class="col-3  align-items-baseline">
                            <label for="id_aula" class="form-label me-3 label-arriba"><b>ID Aula</b></label>
                            <input id="id_aula" type="number" name="id_aula" class="w-35" maxlength="5" required style="width: 80px;">
                        </div>

                        <!-- Capacidad -->
                        <div class="col-3  align-items-baseline">
                            <label for="capacidad" class="form-label me-3 label-arriba"><b>Capacidad</b></label>
                            <input id="capacidad" type="text" name="capacidad" class="w-35" maxlength="75" required
                                onchange="comprueba(this)" onblur="comprueba(this)">
                        </div>
                        <div class="col-3 align-items-baseline">
                        </div>

                    </div>

                    <div class="container">
                        <div class="row">
                            <!-- Columna vacía a la izquierda -->
                            <div class="col-4"></div>

                            <!-- Columna con contenido centrado -->
                            <div class="col-4 d-flex align-items-baseline">
                                <label for="nombre" class="form-label me-3 label-arriba"><b>Nombre</b></label>
                                <input id="nombre" type="text" name="nombre" class="w-100" maxlength="100"
                                    onchange="comprueba(this)" onblur="comprueba(this)">
                            </div>

                            <!-- Columna vacía a la derecha -->
                            <div class="col-4"></div>
                        </div>
                    </div>

                    <input type="hidden" name="id_usuario" value="3">
                    <!-- Campo oculto para la diferenciar insercion o actualización -->
                    <input type="hidden" id="accion" name="accion" value="insert">
                    <div class="d-flex justify-content-end">
                        <!-- Botón para Insertar: se muestra solo en modo insertar -->
                        <button id="btnInsertar" type="submit" name="accion" value="insert" class="btn btn-success me-2"
                            style="display:none;">Insertar Aula</button>
                        <!-- Botón para Modificar: se muestra solo en modo modificar -->
                        <button id="btnModificar" type="submit" name="accion" value="update"
                            class="btn btn-warning me-2" style="display:none;">Modificar Aula</button>
                        <button type="button" class="btn btn-secondary"
                            onclick="location.href='../../mainpage.php'">Volver</button>
                    </div>

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