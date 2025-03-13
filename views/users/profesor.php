<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Bootstrap y otros recursos -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
  <script src="../../js/profesores.js" defer></script>
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Mantenimiento de personal docente</title>
  <link rel="stylesheet" href="../../CSS/estilohojacontacto.css" />
  <link rel="stylesheet" href="../../CSS/formularios.css" />

</head>

<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Panel de Docentes</h2>
    <ul>
    <li><a href="/views/users/backoffice.php"><i class="fas fa-home"></i> Volver al panel central</a></li>

      <li>
        <a href="#" data-bs-toggle="modal" data-bs-target="#modalBuscar" onclick="prellenarModal()">
          <i class="fas fa-search"></i> Buscar Profesor
        </a>
      </li>
      <li>
        <!-- Enlace para insertar profesor -->
        <a href="#" onclick="mostrarInsertarProf()">
          <i class="fas fa-plus"></i> Insertar Profesor
        </a>
      </li>
      <li id="modificarProfesorLi" style="display: none;">
        <!-- Enlace para modificar profesor -->
        <a href="#" onclick="mostrarModificarProf()">
          <i class="fas fa-edit"></i> Modificar profesor
        </a>
      </li>


      <li>
        <a href="/listados/listadosProfesor.php" target="_blank">
          <i class="fas fa-list"></i> Listar Todos los Profesores
        </a>
      </li>
      <li>
        <a id="verFichaProfesor" href="#" target="_blank" class="btn btn-primary" style="display: none;">
          Ficha detalle del Profesor
        </a>
      </li>

      <li><a href="#"><i class="fas fa-bell"></i> Ver Notificaciones</a></li>
      <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
    </ul>
  </div>

  <!-- Contenido principal -->
  <div class="content">
    <div class="header">
      <h1>Mantenimiento de Profesores</h1>
    </div>

    <!-- Bloque de mensajes: Si existe un parámetro GET de éxito o error, se muestra el modal -->
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
              });
          </script>";
    }
    ?>

    <!-- Modal para mensajes -->
    <div class="modal fade" id="mensajeModal" tabindex="-1" aria-labelledby="mensajeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="mensajeModalLabel">Información</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="mensajeModalBody">
            <!-- Contenido del mensaje se llenará dinámicamente -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para búsqueda -->
    <div class="modal fade" id="modalBuscar" tabindex="-1" aria-labelledby="modalBuscarLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalBuscarLabel">Buscar Registro</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Formulario de búsqueda -->
            <form id="formBuscar">
              <div class="mb-3">
                <label for="criterioBusqueda" class="form-label">Criterio de búsqueda: se podrá buscar uno de estos criterios DNI, Apellido1, Apellido2, Nombre</label>
                <input type="text" id="criterioBusqueda" class="form-control" placeholder="Ingrese criterio..." />
              </div>
              <button type="button" class="btn btn-primary" onclick="buscarRegistros()">Buscar</button>
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

    <!-- Sección principal: Formulario de profesores -->
    <div class="section">
      <form id="formProfesor" action="../../mainpage.php?route=storeProfesor" method="post">
        <fieldset>
          <legend class="mb-3">
            <span class="titulo"> - Gestión de Profesores - </span>
          </legend>

          <!-- Mantenemos todos los campos originales -->
          <div class="row g-5 align-items-right">
            <!-- ... (Conserva este bloque de búsqueda si lo requieres, o déjalo tal cual) ... -->
          </div>

          <div class="row align-items-center">
            <!-- Campos del formulario -->
            <div class="col d-flex align-items-baseline">
              <label for="nombre" class="form-label me-3 label-arriba"><b>Nombre</b></label>
              <input id="nombre" type="text" name="nombre" class="w-50" maxlength="75" required
                onchange="comprueba(this)" onblur="comprueba(this)">
            </div>
            <div class="col d-flex align-items-baseline">
              <label for="apellido1" class="form-label me-3 label-arriba"><b>Apellido 1</b></label>
              <input id="apellido1" type="text" name="apellido1" class="w-75" maxlength="75" required
                onchange="comprueba(this)" onblur="comprueba(this)">
            </div>
            <div class="col d-flex align-items-baseline">
              <label for="apellido2" class="form-label me-3 label-arriba"><b>Apellido 2</b></label>
              <input id="apellido2" type="text" name="apellido2" class="w-75" maxlength="75" required
                onchange="comprueba(this)" onblur="comprueba(this)">
            </div>
            <div class="col d-flex align-items-baseline">
              <label for="id_profesor" class="form-label me-3 label-arriba"><b> DNI/NIE </b></label>
              <input id="id_profesor" type="text" value="" name="id_profesor" title="formato 999999999X" class="w-25"
                size="9" maxlength="9" pattern="[0-9]{8}[A-Z]{1}" onchange="comprueba(this)" onblur="comprueba(this)">
            </div>
          </div>

          <div class="col-12 d-flex align-items-baseline">
            <label for="direccion" class="form-label me-3 label-arriba"><b>Dirección (opcional)</b></label>
            <input id="direccion" type="text" name="direccion" class="w-75" maxlength="200" onchange="comprueba(this)"
              onblur="comprueba(this)">
          </div>

          <div class="row g-3 align-items-center">
            <div class="col d-flex align-items-baseline">
              <label for="poblacion" class="form-label me-3 label-arriba"><b>Poblacion</b></label>
              <input id="poblacion" type="text" name="poblacion" class="w-50" maxlength="75" required
                onchange="comprueba(this)" onblur="comprueba(this)">
            </div>
            <div class="col d-flex align-items-baseline">
              <label for="provincia" class="form-label me-3 label-arriba"><b>Provincia</b></label>
              <input id="provincia" type="text" name="provincia" class="w-75" maxlength="75" required
                onchange="comprueba(this)" onblur="comprueba(this)">
            </div>
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
                <input type="tel" id="Phone" name="Phone" title="formato 999999999" pattern="[0-9]{9}" required
                  onchange="comprueba(this)" onblur="comprueba(this)" /></label>
            </div>
            <div class="col d-flex align-items-baseline">
              <label><b> e-mail </b>
                <input id="mail" type="email" name="mail" title="formato eeeeeee@eeee.ee"
                  onchange="return ValidarMail(this)" required /></label>
            </div>
          </div>

          <div class="row g-3 align-items-center">
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
            <div class="col d-flex align-items-baseline">
              <label for="fechalta" class="form-label label-center me-3"><b>Fecha de alta:</b></label>
              <input id="fechalta" type="date" value="" name="fechalta" class="control-offset w-auto" />
            </div>
            <div class="col-md-4 d-flex align-items-center">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="estado" value="alta" onchange="toggleEstado()">
                <label class="form-check-label" for="estado" id="estado-label">Alta</label>
              </div>
            </div>
            <!-- <div class="col d-flex">
              <label class="form-label label-center me-3"><b>Activo:</b></label>
              <div class="form-check control-offset">
                <input class="form-check-input" type="checkbox" id="estado" value="baja">
                <label class="form-check-label" for="estado" id="estado-label">Sí</label>
              </div>
            </div> -->
            <div class="row g-3 align-items-center">

              <div class="col d-flex">

              </div>

              <!-- Fecha de alta -->
              <div class="col d-flex ">
                <label for="especialidad" class="form-label label-center me-3"><b>Especialidad:</b></label>
                <select class="form-select control-offset w-auto" name="especialidad" id="especialidad">
                  <option value="Administración" selected>Administración</option>
                  <option value="Ofimatica">Ofimatica</option>
                  <option value="Programación">Programación</option>
                  <option value="Diseño gráfico y web">Diseño gráfico y web</option>
                  <option value="Gestión ambiental">Gestión ambiental</option>
                  <option value="Servicios a la comunidad">Servicios a la comunidad</option>
                  <option value="Comercio y marketing">Comercio y marketing</option>
                  <option value="Idiomas">Idiomas</option>
                  <option value="Otros">Otros</option>

                </select>

              </div>

              <!-- Estado -->
              <div class="col d-flex">

              </div>
            </div>
          </div>
          <input type="hidden" name="id_usuario" value="3">
          <input type="hidden" id="accion" name="accion" value="insert">
          <input type="hidden" name="fechabaja" id="fechabaja">
          <!-- Botones de envío y de volver -->
          <div class="d-flex justify-content-end">
            <!-- Botón para Insertar: se muestra solo en modo insertar -->
            <button id="btnInsertar" type="submit" name="accion" value="insert" class="btn btn-success me-2"
              style="display:none;">Insertar Profesor</button>
            <!-- Botón para Modificar: se muestra solo en modo modificar -->
            <button id="btnModificar" type="submit" name="accion" value="update" class="btn btn-warning me-2"
              style="display:none;">Modificar Profesor</button>
            <button type="button" class="btn-volver" onclick="location.href='../../mainpage.php'">Volver</button>
          </div>
        </fieldset>
      </form>
    </div>
  </div>

  <!-- <script>
    // Detectar cambios en el checkbox para el estado
    document.getElementById('estado').addEventListener('change', function () {
      const estadoLabel = document.getElementById('estado-label');
      if (this.checked) {
        estadoLabel.textContent = 'Sí';
      } else {
        estadoLabel.textContent = 'No';
      }
    });
  </script> -->
  <script src="../../js/estado.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</body>

</html>