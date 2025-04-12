$(document).ready(function () {
    cargarCursos(); // Cargar cursos al iniciar la página
    $("#cursoSelect").change(cargarEdiciones); // Cargar ediciones cuando se seleccione un curso
    $("#formNuevaEdicion").submit(crearEdicion); // Manejar la creación de nuevas ediciones
});

// 📌 Cargar cursos en el selector
function cargarCursos() {
    $.get('/mainpage.php?route=consultaCursos', function (data) {
        try {
            let cursos = JSON.parse(data); // Parsear JSON
            $("#cursoSelect").empty().append('<option value="">Seleccione un curso</option>');
            cursos.forEach(curso => {
                $("#cursoSelect").append(`<option value="${curso.ID_Curso}">${curso.Nombre}</option>`);
            });
        } catch (error) {
            console.error("Error al procesar la lista de cursos:", error);
            alert("Error al cargar los cursos.");
        }
    }).fail(function () {
        alert("No se pudo conectar con el servidor para obtener los cursos.");
    });
}

// 📌 Cargar ediciones cuando se selecciona un curso
function cargarEdiciones() {
    let idCurso = $("#cursoSelect").val();
    if (!idCurso) {
        $("#tablaEdiciones").html(""); // Limpiar la tabla si no hay curso seleccionado
        return;
    }

    $.get(`/mainpage.php?route=listarEdiciones&idCurso=${idCurso}`, function (data) {
        let html = "";
        if (Array.isArray(data)) {
            data.forEach(ed => {
                html += `<tr>
                    <td>${ed.ID_Edicion}</td>
                    <td>${ed.Fecha_Inicio}</td>
                    <td>${ed.Fecha_Fin}</td>
                    <td>${ed.Estado}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editarEdicion(${ed.ID_Edicion}, '${ed.Fecha_Inicio}', '${ed.Fecha_Fin}', '${ed.Estado}')">Editar</button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarEdicion(${ed.ID_Edicion})">Eliminar</button>
                    </td>
                </tr>`;
            });
        } else {
            html = `<tr><td colspan="5" class="text-center">No hay ediciones disponibles</td></tr>`;
        }
        $("#tablaEdiciones").html(html);
    }).fail(function () {
        alert("Error al cargar las ediciones.");
    });
}

// 📌 Crear una nueva edición
function crearEdicion(event) {
    event.preventDefault();
    
    let idCurso = $("#cursoSelect").val();
    let fechaInicio = $("#fechaInicio").val();
    let fechaFin = $("#fechaFin").val();
    let estado = $("#estadoEdicion").val();

    if (!idCurso) {
        alert("Debes seleccionar un curso antes de crear una edición.");
        return;
    }

    $.ajax({
        url: "/mainpage.php?route=guardarEdicion",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({
            idCurso: idCurso,
            fechaInicio: fechaInicio,
            fechaFin: fechaFin,
            estado: estado
        }),
        success: function (response) {
            if (response.success) {
                alert("Edición creada con éxito.");
                cargarEdiciones();
                $("#formNuevaEdicion")[0].reset();
            } else {
                alert("Error al crear la edición.");
            }
        },
        error: function () {
            alert("No se pudo conectar con el servidor.");
        }
    });
}

// 📌 Eliminar una edición
function eliminarEdicion(idEdicion) {
    if (!confirm("¿Estás seguro de que deseas eliminar esta edición?")) {
        return;
    }

    $.ajax({
        url: `/mainpage.php?route=eliminarEdicion&idEdicion=${idEdicion}`,
        type: "GET",
        success: function (response) {
            if (response.success) {
                alert("Edición eliminada con éxito.");
                cargarEdiciones();
            } else {
                alert("Error al eliminar la edición.");
            }
        },
        error: function () {
            alert("No se pudo conectar con el servidor.");
        }
    });
}

// 📌 Editar una edición (Cargar datos en el formulario)
function editarEdicion(idEdicion, fechaInicio, fechaFin, estado) {
    $("#fechaInicio").val(fechaInicio);
    $("#fechaFin").val(fechaFin);
    $("#estadoEdicion").val(estado);

    $("#formNuevaEdicion").off("submit").on("submit", function (event) {
        event.preventDefault();
        actualizarEdicion(idEdicion);
    });
}

// 📌 Actualizar una edición
function actualizarEdicion(idEdicion) {
    let fechaInicio = $("#fechaInicio").val();
    let fechaFin = $("#fechaFin").val();
    let estado = $("#estadoEdicion").val();

    $.ajax({
        url: "/mainpage.php?route=actualizarEdicion",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({
            idEdicion: idEdicion,
            fechaInicio: fechaInicio,
            fechaFin: fechaFin,
            estado: estado
        }),
        success: function (response) {
            if (response.success) {
                alert("Edición actualizada con éxito.");
                cargarEdiciones();
                $("#formNuevaEdicion")[0].reset();
                $("#formNuevaEdicion").off("submit").on("submit", crearEdicion);
            } else {
                alert("Error al actualizar la edición.");
            }
        },
        error: function () {
            alert("No se pudo conectar con el servidor.");
        }
    });
}
