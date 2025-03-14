/**
 * @fileoverview Este script gestiona la carga de cursos y alumnos, la matriculación de alumnos y la actualización
 * de la URL del enlace de matriculaciones. Utiliza jQuery para realizar solicitudes AJAX, manipular el DOM y gestionar eventos.
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

    $.get('../controllers/getAlumnos.php', function (response) {
        try {
            // Verificar si la respuesta es un string; si lo es, parsearla a objeto
            let alumnos = typeof response === "string" ? JSON.parse(response) : response;
            let html = '';
            alumnos.forEach(alumno => {
                html += `<tr>
                    <td><input type="checkbox" class="alumno-checkbox" value="${alumno.ID_Alumno}"></td>
                    <td>${alumno.Nombre}</td>
                    <td>${alumno.Apellido1}</td>
                    <td>${alumno.Apellido2}</td>
                    <td>${alumno.ID_Alumno}</td>
                </tr>`;
            });
            $('#alumnosTableBody').html(html);
            $('#alumnosContainer').removeClass('d-none');
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
    let alumnosSeleccionados = [];

    $('.alumno-checkbox:checked').each(function () {
        alumnosSeleccionados.push($(this).val());
    });

    $.ajax({
        url: 'mainpage.php?route=storeMatricula',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id_curso: idCurso, alumnos: alumnosSeleccionados }),
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

            setTimeout(function () {
                window.location.href = '/views/users/backoffice.php';
            }, 3000);
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
        link.href = "/listados/listadoMatriculas.php?idCurso=" + encodeURIComponent(cursoId);
        link.setAttribute("target", "_blank"); // Abre en nueva pestaña
    } else {
        // Si no hay curso seleccionado, deshabilita el enlace
        link.href = "#";
        link.removeAttribute("target"); // Evita que enlaces vacíos se abran en otra pestaña
    }
});

