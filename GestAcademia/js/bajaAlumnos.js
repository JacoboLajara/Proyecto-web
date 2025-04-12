/**
 * @fileoverview Este script utiliza jQuery para cargar cursos, alumnos y gestionar la baja de alumnos.
 * Se ejecuta cuando el DOM está listo.
 */

$(document).ready(function () {
    /**
     * Carga los cursos al cargar la página.
     *
     * Realiza una solicitud GET al endpoint '/../../controllers/getCursos.php' y por cada curso recibido,
     * lo agrega como una opción al elemento select con id "id_curso".
     *
     * @function
     */
    $.get('/../../controllers/getCursos.php', function (response) {
        response.forEach(curso => {
            $('#id_curso').append(`<option value="${curso.ID_Curso}">${curso.Nombre}</option>`);
        });
    }, 'json');

    /**
     * Evento "change" del select de cursos.
     *
     * Cuando se selecciona un curso, se construye la URL para obtener los alumnos matriculados
     * en ese curso y se realiza una solicitud GET. Si la respuesta es exitosa, se actualiza la tabla
     * de alumnos; de lo contrario, se muestra un mensaje de error.
     *
     * @function
     */
    $('#id_curso').change(function () {
        let idCurso = $(this).val();
        let url = 'mainpage.php?route=getAlumnosMatriculados&id_curso=' + idCurso;

        console.log('ID del curso seleccionado:', idCurso);
        console.log('URL de la solicitud AJAX:', url);  // Verifica que la URL sea correcta

        if (!idCurso) {
            alert('Por favor, selecciona un curso.');
            return;
        }

        $.get(url, function (response) {
            console.log('Respuesta del servidor:', response);  // Depuración de la respuesta

            if (response.success) {
                let html = '';
                response.data.forEach(alumno => {
                    html += `<tr>
                        <td><input type="checkbox" class="alumno-checkbox" value="${alumno.ID_Alumno}"></td>
                        <td>${alumno.Nombre}</td>
                        <td>${alumno.Apellido1}</td>
                        <td>${alumno.Apellido2}</td>
                        <td>${alumno.Fecha_Matricula}</td>
                    </tr>`;
                });
                $('#alumnosTableBody').html(html);
                $('#alumnosContainer').removeClass('d-none');
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json').fail(function (jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
        });
    });

    /**
     * Envía los alumnos seleccionados para darles de baja.
     *
     * Al enviar el formulario con id "bajaAlumnosForm", se previene el envío por defecto,
     * se recopilan los IDs de los alumnos seleccionados y se envía una solicitud AJAX POST en formato JSON
     * al endpoint encargado de procesar la baja de alumnos. Si la operación es exitosa, se recarga la página;
     * de lo contrario, se muestra un mensaje de error.
     *
     * @function
     */
    $('#bajaAlumnosForm').on('submit', function (e) {
        e.preventDefault();

        let idCurso = $('#id_curso').val();
        let alumnosSeleccionados = [];

        $('.alumno-checkbox:checked').each(function () {
            alumnosSeleccionados.push($(this).val());
        });

        if (alumnosSeleccionados.length === 0) {
            alert('Debes seleccionar al menos un alumno para dar de baja.');
            return;
        }

        $.ajax({
            url: '../../mainpage.php?route=procesarBajaAlumnos',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id_curso: idCurso, alumnos: alumnosSeleccionados }),
            success: function (response) {
                if (response.success) {
                    alert('Alumnos dados de baja correctamente.');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud:', textStatus, errorThrown);
                alert('Ocurrió un error al procesar la solicitud.');
            }
        });
    });
});
