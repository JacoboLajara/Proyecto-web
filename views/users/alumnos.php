<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Bootstrap y otros recursos -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
  <script src="../../js/alumnos.js" defer></script>
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Mantenimiento de Alumnos</title>
  <link rel="stylesheet" href="../../CSS/estilohojacontacto.css" />
  <link rel="stylesheet" href="../../CSS/formularios.css" />



</head>

<body>
  <!-- Sidebar con enlaces para búsqueda, inserción y modificación -->
  <div class="sidebar">
    <h2>Panel de Alumnos</h2>
    <ul>
      <li><a href="./views/users/backoffice.php"><i class="fas fa-home"></i> Volver al panel central</a></li>
      <li>
        <!-- Enlace que activa el modal de búsqueda -->
        <a href="#" data-bs-toggle="modal" data-bs-target="#modalBuscar" onclick="prellenarModal()">
          <i class="fas fa-search"></i> Buscar Alumno
        </a>
      </li>
      <li>
        <!-- Enlace para insertar alumno -->
        <a href="#" onclick="mostrarInsertar()">
          <i class="fas fa-plus"></i> Insertar Alumno
        </a>
      </li>
      <!-- Enlace para modificar alumno -->
      <li id="modificarAlumnoLi" style="display: none;">
        <a href="#" onclick="mostrarModificar()">
          <i class="fas fa-edit"></i> Modificar Alumno
        </a>
      </li>
      <!-- Enlace para listar alumnos -->
      <li>
        <a href="/listados/listadosAlumnos.php">
          <i class="fas fa-list"></i> Listar Todos los Alumnos
        </a>
      </li>
      <li>
        <a id="generarFichaAlumno" href="#" target="_blank" class="btn btn-primary"
          style="display: none; visibility: visible; opacity: 1;">
          <i class="fas fa-file-alt"></i> Generar Ficha del Alumno
        </a>
      </li>

      <li>
        <a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
      </li>
    </ul>
  </div>

  <!-- Contenido principal -->
  <div class="content">
    <div class="header">
      <h1>Mantenimiento de Alumnos</h1>
    </div>

    <!-- Bloque de mensajes: Si existe un parámetro GET de éxito o error, se muestra el modal y se redirige -->
    <?php
    if (isset($_GET['error']) || isset($_GET['success'])) {
      $mensaje = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : htmlspecialchars($_GET['success']);
      $tipo = isset($_GET['error']) ? 'alert-danger' : 'alert-success';
      // El script muestra el modal y luego redirige a backoffice
      echo "
        <script>
          document.addEventListener('DOMContentLoaded', function() {
              const mensajeModal = document.getElementById('mensajeModalBody');
              mensajeModal.className = '';
              mensajeModal.classList.add('modal-body', '$tipo');
              mensajeModal.textContent = '$mensaje';
              const modal = new bootstrap.Modal(document.getElementById('mensajeModal'));
              modal.show();
              // Después de 3 segundos redirige a la pantalla de backoffice
              setTimeout(function() {
                window.location.href = 'backoffice.php';
              }, 3000);
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
          <div class="modal-body" id="mensajeModalBody"></div>
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
                  <th>Apellido</th>
                  <th>Apellido2</th>
                  <th>Acción</th>

                </tr>
              </thead>
              <tbody id="tablaResultados"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Sección principal: Formulario de alumnos -->
    <div class="section">
      <form id="formAlumno" action="../../mainpage.php?route=storeAlumno" method="post">
        <fieldset>
          <legend class="mb-3">
            <span class="titulo"> - Datos personales del alumno- </span>
          </legend>

          <!-- Campos del formulario -->
          <div class="row mb-3">
            <div class="col-md-3">
              <label for="nombre" class="form-label">Nombre</label>
              <input id="nombre" type="text" name="nombre" class="form-control" maxlength="75" required
                onchange="comprueba(this)" onblur="comprueba(this)">
            </div>
            <div class="col-md-3">
              <label for="apellido1" class="form-label">Apellido 1</label>
              <input id="apellido1" type="text" name="apellido1" class="form-control" maxlength="75" required
                onchange="comprueba(this)" onblur="comprueba(this)">
            </div>
            <div class="col-md-3">
              <label for="apellido2" class="form-label">Apellido 2</label>
              <input id="apellido2" type="text" name="apellido2" class="form-control" maxlength="75" required
                onchange="comprueba(this)" onblur="comprueba(this)">
            </div>
            <div class="col-md-1">
              <label for="id_alumno" class="form-label">DNI/NIE</label>
              <input id="id_alumno" type="text" name="id_alumno" class="form-control" size="9" maxlength="9"
                pattern="[0-9]{8}[A-Z]{1}" onchange="comprueba(this)" onblur="comprueba(this)">
            </div>
          </div>

          <div class="mb-3">
            <div class="col-md-9">
              <label for="direccion" class="form-label">Dirección (opcional)</label>
              <input id="direccion" type="text" name="direccion" class="form-control" maxlength="200"
                onchange="comprueba(this)" onblur="comprueba(this)">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4">
              <label for="poblacion" class="form-label">Población</label>
              <input id="poblacion" type="text" name="poblacion" class="form-control" maxlength="75" required
                onchange="comprueba(this)" onblur="comprueba(this)">
            </div>
            <div class="col-md-4">
              <label for="provincia" class="form-label">Provincia</label>
              <input id="provincia" type="text" name="provincia" class="form-control" maxlength="75" required
                onchange="comprueba(this)" onblur="comprueba(this)">
            </div>

            <div class="col-md-2">
              <label for="cpostal" class="form-label">Código Postal</label>
              <input id="cpostal" type="text" name="cpostal" class="form-control" maxlength="5" required
                onchange="comprueba(this)" onblur="comprueba(this)">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-2">
              <label for="fechanac" class="form-label">Fecha de Nacimiento</label>
              <input id="fechanac" type="date" name="fechanac" class="form-control" onchange="comprueba(this)"
                onblur="comprueba(this)">
            </div>
            <div class="col-md-2">
            </div>
            <div class="col-md-2">
              <label for="Phone" class="form-label">Teléfono</label>
              <input type="tel" id="Phone" name="Phone" class="form-control" title="formato 999999999"
                pattern="[0-9]{9}" required onblur="comprueba(this)">
            </div>
            <div class="col-md-2">

            </div>
            <div class="col-md-2">
              <label for="mail" class="form-label">E-mail</label>
              <input id="mail" type="email" name="mail" class="form-control" title="formato mail@server.com"
                onchange="return ValidarMail(this)" required>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4">
              <label for="estudios" class="form-label">Nivel de Estudios</label>
              <select class="form-select" name="estudios" id="estudios">
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
            <div class="col-md-4">
              <label for="fechalta" class="form-label">Fecha de Alta</label>
              <input id="fechalta" type="date" name="fechalta" class="form-control">
            </div>
            <div class="col-md-4 d-flex align-items-center">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="estado" value="alta" onchange="toggleEstado()">
                <label class="form-check-label" for="estado" id="estado-label">Alta</label>
              </div>
            </div>
          </div>

          <!-- Campos ocultos -->
          <input type="hidden" name="id_usuario" value="4">
          <input type="hidden" name="fechabaja" id="fechabaja">
          <input type="hidden" id="accion" name="accion" value="insert">

          <!-- Botones de envío y de volver -->
          <div class="d-flex justify-content-end">
            <!-- Botón para Insertar: se muestra solo en modo insertar -->
            <button id="btnInsertar" type="submit" name="accion" value="insert" class="btn btn-success me-2"
              style="display:none;">Insertar Alumno</button>
            <!-- Botón para Modificar: se muestra solo en modo modificar -->
            <button id="btnModificar" type="submit" name="accion" value="update" class="btn btn-warning me-2"
              style="display:none;">Modificar Alumno</button>
            <button type="button" class="btn-volver" onclick="location.href='../../mainpage.php'">Volver</button>
          </div>
        </fieldset>
      </form>
    </div>
  </div>

  <script src="../../js/estado.js"></script>
</body>

</html>