$(document).ready(function () {
    /**
     * Carga los cursos al cargar la página.
     */
    $.get('/controllers/getCursos.php', function (response) {
        console.log('📌 Cursos recibidos:', response);

        if (Array.isArray(response)) {
            response.forEach(curso => {
                $('#id_curso').append(`<option value="${curso.ID_Curso}">${curso.Nombre}</option>`);
            });
        } else {
            console.error('❌ Respuesta inesperada de getCursos.php:', response);
            alert('Error al cargar los cursos.');
        }
    }, 'json').fail(function (jqXHR, textStatus, errorThrown) {
        console.error('❌ Error en la solicitud AJAX para obtener cursos:', textStatus, errorThrown);
        alert('No se pudieron cargar los cursos.');
    });

    /**
     * Evento "change" del select de cursos para cargar alumnos matriculados.
     */
    $('#id_curso').change(function () {
        let idCurso = $(this).val();
        let url = '/mainpage.php?route=getAlumnosMatriculados&id_curso=' + encodeURIComponent(idCurso);

        console.log('🔍 ID del curso seleccionado:', idCurso);
        console.log('🔍 URL de la solicitud AJAX:', url);

        if (!idCurso) {
            alert('Por favor, selecciona un curso.');
            return;
        }

        $.get(url, function (response) {
            console.log('📌 Respuesta del servidor:', response);

            if (response && response.success && Array.isArray(response.data)) {
                let html = '';
                response.data.forEach(alumno => {
                    html += `<tr>
                        <td>${alumno.Nombre}</td>
                        <td>${alumno.Apellido1}</td>
                        <td>${alumno.Apellido2}</td>
                        <td>${alumno.Fecha_Matricula}</td>
                    </tr>`;
                });

                // Insertamos los datos en la tabla
                $('#alumnoslistaTableBody').html(html);

                // ✅ Asegurar que el contenedor sea visible
                $('#alumnoslistaContainer').removeClass('d-none').show();
            } else {
                console.error('❌ Formato inesperado en la respuesta:', response);
                alert('Error al obtener alumnos.');
            }
        }, 'json').fail(function (jqXHR, textStatus, errorThrown) {
            console.error('❌ Error al obtener alumnos:', textStatus, errorThrown);
            alert('No se pudieron cargar los alumnos.');
        });
    });
});
