<?php require_once __DIR__ . '/../../controllers/EdicionCursosController.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ediciones de Cursos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Gestión de Ediciones de Cursos</h2>

        <!-- Selector de curso -->
        <label for="cursoSelect" class="form-label">Selecciona un Curso</label>
        <select id="cursoSelect" class="form-control mb-3">
            <option value="">Seleccione un curso</option>
        </select>

        <!-- Formulario para agregar ediciones -->
        <h4>Crear Nueva Edición</h4>
        <form id="formNuevaEdicion">
            <label for="fechaInicio" class="form-label">Fecha de Inicio</label>
            <input type="date" id="fechaInicio" class="form-control mb-2" required>

            <label for="fechaFin" class="form-label">Fecha de Fin</label>
            <input type="date" id="fechaFin" class="form-control mb-2" required>

            <label for="estadoEdicion" class="form-label">Estado</label>
            <select id="estadoEdicion" class="form-control mb-3">
                <option value="Abierto">Abierto</option>
                <option value="En Curso">En Curso</option>
                <option value="Cerrado">Cerrado</option>
            </select>

            <button type="submit" class="btn btn-primary">Crear Edición</button>
        </form>

        <!-- Tabla para mostrar ediciones -->
        <h4 class="mt-4">Ediciones Existentes</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaEdiciones"></tbody>
        </table>
    </div>

    <!-- Cargar los scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/js/edicioncursos.js"></script>
</body>
</html>
