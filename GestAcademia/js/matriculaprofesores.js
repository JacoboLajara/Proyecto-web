/**
 * @fileoverview Este script gestiona la carga de cursos y profesores, la asignación de profesores a cursos y la actualización
 * de la URL del enlace de agignaciones. Utiliza jQuery para realizar solicitudes AJAX, manipular el DOM y gestionar eventos.
 */

// Cargar cursos y agregarlos al select con id "id_curso"
$.get('controllers/getCursos.php', function (response) {
    console.log("Respuesta recibida:", response);
    // No es necesario utilizar JSON.parse si jQuery ya devuelve un objeto
    response.forEach(curso => {
        $('#id_curso').append(`<option value="${curso.ID_Curso}">${curso.Nombre}</option>`);
    });
}, 'json')
    .fail(function (jqXHR, textStatus, errorThrown) {
        console.error("Error en la solicitud AJAX:", textStatus, errorThrown);
    });

// Evento "change" del select "id_curso" para cargar los alumnos asociados al curso seleccionado
$('#id_curso').change(function () {
    let idCurso = $(this).val();

    $.get('../controllers/getProfesoresAsignacion.php', function (response) {
        try {
            // Verificar si la respuesta es un string; si lo es, parsearla a objeto
            let profesores = typeof response === "string" ? JSON.parse(response) : response;
            let html = '';
            profesores.forEach(profesor => {
                html += `<tr>
                    <td><input type="checkbox" class="profesor-checkbox" value="${profesor.ID_Profesor}"></td>
                    <td>${profesor.Nombre}</td>
                    <td>${profesor.Apellido1}</td>
                    <td>${profesor.Apellido2}</td>
                    <td>${profesor.ID_Profesor}</td>
                </tr>`;
            });
            $('#profesorTableBody').html(html);
            $('#profesorContainer').removeClass('d-none');
        } catch (error) {
            console.error("Error al parsear JSON:", error);
            console.error("Respuesta del servidor:", response);
        }
    });
});

// Evento "submit" del formulario de matriculación para enviar los alumnos seleccionados
$('#matriculaForm').on('submit', function (e) {
    e.preventDefault();

    let idCurso = $('#id_curso').val();
    let profesoresSeleccionados = [];

    $('.profesor-checkbox:checked').each(function () {
        profesoresSeleccionados.push($(this).val());
    });

    console.log("ID del Curso:", idCurso);
    console.log("Profesores seleccionados:", profesoresSeleccionados);


    $.ajax({
        url: 'mainpage.php?route=storeAsignacion',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id_curso: idCurso, profesores: profesoresSeleccionados }),
        success: function (response) {
            console.log("Contenido de la respuesta antes de parsear:", response);

            let res;
            try {
                res = typeof response === 'string' ? JSON.parse(response) : response;
            } catch (error) {
                console.error("Error al parsear la respuesta JSON:", error);
                console.log("Contenido de la respuesta que no se pudo parsear:", response);
                return;
            }

            console.log("Mensaje del servidor:", res.message);

            const mensajeModal = document.getElementById('mensajeModalBody');
            mensajeModal.className = 'modal-body';
            mensajeModal.classList.add(res.success ? 'alert-success' : 'alert-danger');
            mensajeModal.textContent = res.message;

            const modal = new bootstrap.Modal(document.getElementById('mensajeModal'));
            modal.show();

            // setTimeout(function () {
            //     window.location.href = '/views/users/backoffice.php';
            // }, 3000);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("Error en la solicitud:", textStatus, errorThrown);
            $('#responseMessage').html('<div class="alert alert-danger">Ocurrió un error al procesar la solicitud.</div>');
        }
    });
});

// Actualiza el enlace para mostrar el listado de matriculaciones cuando se cambia el curso
document.getElementById('id_curso').addEventListener('change', function () {
    let cursoId = this.value;
    let link = document.getElementById('linkMatriculas');
    if (cursoId !== "") {
        // Actualiza la URL con el parámetro idCurso
        link.href = "../listados/listadoAsignacionCurso.php?idCurso=" + encodeURIComponent(cursoId);
    } else {
        // Si no hay curso seleccionado, deshabilita el enlace
        link.href = "#";
    }
});
