<?php
// views/users/recibos.php
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Recibos</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="../../CSS/colores.css" />
  <link rel="stylesheet" href="../../CSS/estilohojacontacto.css" />
  <link rel="stylesheet" href="../../CSS/global.css">
  <link rel="stylesheet" href="../../CSS/formularios.css" />

  <script src="../../js/recibos.js" defer></script>


</head>

<body>
  <div class="sidebar">
    <h2>Panel de control de Recibos</h2>
    <ul>
    <li><a href="./views/users/backoffice.php"><i class="fas fa-home"></i> Volver al panel central</a></li>

      <li>
        <a href="/listados/listadoRecibos.php" id="linkListadoRecibos" data-bs-toggle="tooltip"
          title="Generar PDF de recibos según criterios seleccionados">
          <i class="fas fa-file-pdf-o"></i> Generar listado de Recibos
        </a>
      </li>


      <li><a href="#"><i class="fas fa-bell"></i> Ver Notificaciones</a></li>
      <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
    </ul>
  </div>
  <div class="content">
    <div class="header">
      <h1>Mantenimiento de Recibos</h1>
    </div>
    <div class="container mt-4">
      <h2>Gestión de Recibos</h2>
      <form id="filtroRecibos" class="form-inline mb-3">
        <div class="form-group mr-2">
          <label for="apellido1" class="mr-2">Apellido 1:</label>
          <input type="text" class="form-control" id="apellido1" name="apellido1" placeholder="Apellido">
        </div>

        <!--carga el año actual y los cinco anteriores -->
        <div class="form-group mr-2">
          <label for="anio" class="mr-2">Año:</label>
          <select class="form-control" id="anio" name="anio">
            <option value="">Todos</option>
            <?php
            $currentYear = date("Y");
            for ($i = 0; $i <= 5; $i++) {
              $year = $currentYear - $i;
              echo '<option value="' . $year . '">' . $year . '</option>';
            }
            ?>
          </select>
        </div>

        <div class="form-group mr-2">
          <label for="mes" class="mr-2">Mes:</label>
          <select class="form-control" id="mes" name="mes">
            <option value="">Todos</option>
            <option value="01">Enero</option>
            <option value="02">Febrero</option>
            <option value="03">Marzo</option>
            <option value="04">Abril</option>
            <option value="05">Mayo</option>
            <option value="06">Junio</option>
            <option value="07">Julio</option>
            <option value="08">Agosto</option>
            <option value="09">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
          </select>
        </div>

        <!-- Carga los cursos dinamicamente según los que esten en base de datos -->
        <div class="form-group mr-2">
          <label for="curso" class="mr-2">Curso:</label>
          <select class="form-control" id="curso" name="curso">
            <option value="">Todos</option>
            <?php
            require_once __DIR__ . '/../../models/RecibosModel.php';
            $reciboModel = new RecibosModel();
            $recibos = $reciboModel->getCursosRecibo();
            foreach ($recibos as $recibo) {
              // Utilizamos ID_Curso como valor y Nombre como texto
              echo '<option value="' . htmlspecialchars($recibo['ID_Curso']) . '">' . htmlspecialchars($recibo['Nombre']) . '</option>';
            }
            ?>
          </select>
        </div>

        <div class="form-group mr-2">
          <label for="pendientes" class="mr-2">Pendientes:</label>
          <input type="checkbox" id="pendientes" name="pendientes" value="1">
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
      </form>

      <div id="tablaRecibos"></div>
    </div>

    <!-- Modal para editar recibo -->
    <div class="modal fade" id="editarReciboModal" tabindex="-1" role="dialog" aria-labelledby="editarReciboModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editarReciboModalLabel">Editar Recibo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="formEditarRecibo">
              <input type="hidden" id="editarReciboId" name="id">
              <div class="form-group">
                <label for="estadoRecibo">Estado</label>
                <select class="form-control" id="estadoRecibo" name="estado">
                  <option value="Pendiente">Pendiente</option>
                  <option value="Cobrado">Cobrado</option>
                </select>
              </div>
              <div class="form-group">
                <label for="fechaPago">Fecha de Pago</label>
                <input type="date" class="form-control" id="fechaPago" name="fecha_pago">
                <small class="form-text text-muted">Si el estado es "Pendiente", dejar vacío.</small>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="guardarEdicionReciboBtn">Guardar Cambios</button>
          </div>
        </div>
      </div>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        // Inicializar todos los tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
        });
      });
    </script>
</body>

</html>