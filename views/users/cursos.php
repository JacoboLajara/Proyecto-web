<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="../../js/cursos.js" defer></script>
    <link rel="stylesheet" href="../../CSS/estilohojacontacto.css">
    <link rel="stylesheet" href="../../CSS/formularios.css" />

    <title>Gestión de Cursos</title>

    <style>
       
        .division1 {
            width: 95%;
            margin: 2.5% auto;
        }

        .hidden {
            display: none;
        }

        table input[type="text"] {
            width: 100%;
            box-sizing: border-box;
        }
    </style>
</head>


<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Panel de control de Cursos</h2>
        <ul>
            <li><a href="./views/users/backoffice.php"><i class="fas fa-home"></i> Volver al panel central</a></li>
            <li>
                <a href="router.php?route=consultaCursos">
                    <i class="fas fa-eye"></i> Consultar/Modificar Cursos
                </a>
            </li>
            <!-- 📌 Listado de todos los cursos -->
            <li>
                <a href="listados/listadoCursos.php" target="_blank">
                    📄 Listado de Cursos
                </a>
            </li>

            <!-- 📌 Listado de cursos con módulos y unidades formativas -->
            <li>
                <a href="listados/listadoCursoDetalle.php" target="_blank">
                    📄 Listado de Cursos con Módulos y Unidades
                </a>
            </li>
            <!-- <li><a href="#"><i class="fas fa-bell"></i> Ver Notificaciones</a></li> -->
            <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <div class="header">
            <h1>Mantenimiento de Cursos</h1>
        </div>
        <form method="post">
            <div class="division1">
                <fieldset>
                    <!-- Campos iniciales -->
                    <div class="row g-5 align-items-right">
                        <legend class="mb-3">
                            <span class="titulo"> - Gestión de cursos - </span>
                        </legend>
                    </div>


                    <!-- Cuerpo general de cursos  -->
                    <div class="row g-5 align-items-center">
                        <div class="col-3 align-items-baseline"></div>
                        <div class="col-3 align-items-baseline">
                            <label for="idcurso" class="form-label me-3"><b>Código</b></label>
                            <input id="idcurso" type="text" name="idcurso" class="w-25" maxlength="75" required>
                        </div>
                        <div class="col-3 align-items-baseline">
                            <label for="nombrecurso" class="form-label me-3"><b>Nombre del Curso</b></label>
                            <input id="nombrecurso" type="text" name="nombrecurso" class="w-50" maxlength="75" required>
                        </div>
                        <div class="col-3 align-items-baseline"></div>
                    </div>
                    <div class="row g-5 align-items-center">
                        <div class="col-4 d-flex align-items-center">
                            <label for="tipoCurso" class="me-2"><b>Tipo de curso:</b></label>
                            <select class="form-select w-35" id="tipoCurso">
                                <option value="Oficial">Oficial</option>
                                <option value="Privado">Privado</option>
                            </select>
                        </div>
                        <div class="col-4 align-items-baseline">
                            <label for="duracioncurso" class="form-label me-3"><b>Duración:</b></label>
                            <input id="duracioncurso" type="text" name="duracioncurso" class="w-50" maxlength="75"
                                required>
                        </div>
                        <div class="col-4 align-items-baseline">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="esModular"
                                    onclick="toggleModulosTable()">
                                <label class="form-check-label" for="esModular">Es modular</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-5 align-items-center" id="precioContainer" style="display: none;">
                        <div class="col-6 d-flex align-items-left">
                            <label for="tipoCuota" class="me-2"><b>Precio por:</b></label>
                            <select class="form-select w-25" id="tipoCuota">
                                <option value="Hora">Hora</option>
                                <option value="Mensual">Mensual</option>
                                <option value="Total">Total</option>
                            </select>
                        </div>
                        <div class="col-6 align-items-right">
                            <label for="preciocurso" class="form-label me-3"><b>Precio:</b></label>
                            <input id="preciocurso" type="text" name="preciocurso" class="w-25" maxlength="75" required>
                        </div>
                    </div>

                    <button type="button" class="btn btn-success mt-3" onclick="saveCurso()">Guardar Curso</button>

                    <!-- Tabla de módulos -->
                    <div id="modulosContainer" class="hidden">
                        <h3 class="mb-3">Módulos</h3>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle table-sm" id="modulosTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col">Código</th>
                                        <th scope="col">Nombre del Módulo</th>
                                        <th scope="col">Duración</th>
                                        <th scope="col">¿Añadir Unidades Formativas?</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Filas dinámicas de módulos -->
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary mt-3" onclick="addModuloRow()">Añadir
                                Módulo</button>
                        </div>
                    </div>

                    <!-- Tabla de unidades formativas -->
                    <div id="unidadesFormativasContainer" class="hidden">
                        <h3 class="mb-3">Unidades Formativas</h3>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle table-sm"
                                id="unidadesFormativasTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col">Código</th>
                                        <th scope="col">Denominación</th>
                                        <th scope="col">Duración</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Filas dinámicas de unidades formativas -->
                                </tbody>
                            </table>

                            <button type="button" class="btn btn-primary mt-3" onclick="addUnidadRow()">Añadir
                                Unidad</button>
                            <button type="button" class="btn btn-success mt-3"
                                onclick="saveUnidadesFormativas()">Guardar
                                Unidades</button>
                        </div>
                    </div>
                </fieldset>
            </div>
        </form>



</body>

</html>