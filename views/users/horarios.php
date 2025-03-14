<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Bootstrap y otros recursos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="../../js/horario.js" defer></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Mantenimiento de horarios</title>
    <link rel="stylesheet" href="../../CSS/estilohojacontacto.css" />
    <link rel="stylesheet" href="../../CSS/formularios.css" />
</head>


<body>
    <?php include __DIR__ . '/../../PHP/navbar3.php'; ?>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Panel de control de horarios</h2>
        <ul>
            <li><a href="./views/users/backoffice.php"><i class="fas fa-home"></i> Volver al panel central</a></li>
            <li> <a href="/listados/ListadoHorarios.php" target="_blank">
                    <i class="fas fa-list"></i> Listar Todos los Horarios ocupados
                </a>
            </li>
            <!-- <li><a href="#"><i class="fas fa-bell"></i> Ver Notificaciones</a></li> -->
            <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </div>
    <div class="content">
        <div class="header">
            <h1>Mantenimiento de Horarios</h1>
        </div>

        <div class="container mt-4">
            <form id="formAsignarHorario" method="POST" action="procesar_asignacion.php">
                <fieldset>
                    <legend>- Asignar Horario -</legend>

                    <!-- Selección de aula -->
                    <div class="row mb-3">
                        <label for="aula" class="col-sm-2 col-form-label"><b>Seleccionar Aula</b></label>
                        <div class="col-sm-4">
                            <select class="form-select" name="aula" id="aula" required>
                                <option value="">-- Seleccionar Aula --</option>
                                <?php foreach ($aulas as $aula) { ?>
                                    <option value="<?= $aula['ID_Aula'] ?>"><?= $aula['Nombre'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- Selección de curso -->
                    <div class="row mb-3">
                        <label for="curso" class="col-sm-2 col-form-label"><b>Seleccionar Curso</b></label>
                        <div class="col-sm-4">
                            <select class="form-select" name="curso" id="curso" required>
                                <option value="">-- Seleccionar Curso --</option>
                                <?php foreach ($cursos as $curso) { ?>
                                    <option value="<?= $curso['ID_Curso'] ?>"><?= $curso['Nombre'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- Botón para cargar horarios ocupados -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label"><b>Horarios Ocupados</b></label>
                        <div class="col-sm-4">
                            <button type="button" id="btnMostrarHorarios" class="btn btn-info" data-bs-toggle="modal"
                                data-bs-target="#modalHorarios">
                                Ver Horarios Ocupados
                            </button>
                        </div>
                    </div>

                    <!-- Modal de Horarios Ocupados -->
                    <div class="modal fade" id="modalHorarios" tabindex="-1" aria-labelledby="modalHorariosLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalHorariosLabel">Horarios Ocupados del Aula</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Día</th>
                                                    <th>Inicio mañana</th>
                                                    <th>Fin mañana</th>
                                                    <th>Inicio tarde</th>
                                                    <th>Fin tarde</th>
                                                    <th>Curso</th>
                                                </tr>
                                            </thead>
                                            <tbody id="horariosOcupadosBody">
                                                <!-- Aquí se cargarán los horarios ocupados dinámicamente -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- Tabla de horarios -->
                    <div id="horariosContainer">
                        <h3 class="mb-3">Jornadas de formación presencial</h3>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Día</th>
                                        <th>Hora de inicio (Mañana)</th>
                                        <th>Hora de fin (Mañana)</th>
                                        <th>Hora de inicio (Tarde)</th>
                                        <th>Hora de fin (Tarde)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $dias = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
                                    foreach ($dias as $dia) {
                                        ?>
                                        <tr>
                                            <td><b><?= $dia ?></b></td>
                                            <td><input type="time" name="horario[<?= $dia ?>][manana_inicio]"
                                                    class="form-control"></td>
                                            <td><input type="time" name="horario[<?= $dia ?>][manana_fin]"
                                                    class="form-control">
                                            </td>
                                            <td><input type="time" name="horario[<?= $dia ?>][tarde_inicio]"
                                                    class="form-control"></td>
                                            <td><input type="time" name="horario[<?= $dia ?>][tarde_fin]"
                                                    class="form-control">
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="text-center mt-4">
                        <button type="submit" id="btnAsignarHorario" class="btn btn-success">Asignar Horario</button>

                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</body>

</html>