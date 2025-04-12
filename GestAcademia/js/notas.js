/**
 * @file notas.js
 * @description Este script gestiona la funcionalidad relacionada con la asignación y gestión de notas.
 * Incluye la obtención de cursos y alumnos, la visualización de módulos, unidades formativas y notas,
 * así como el envío y actualización de las calificaciones mediante peticiones AJAX.
 */

$(document).ready(function () {
    // Variable para determinar el modo de operación: "store" para insertar nuevas notas o "update" para actualizar existentes.
    let modoNotas = "store";
    console.log("✅ Cargando notas.js...");


    $(document).on("input", ".nota-input", function () {
        let valor = parseFloat(this.value);
    
        if (isNaN(valor) || valor < 0) {
            this.value = 0;  // Si es menor que 0, lo pone en 0
        } else if (valor > 10) {
            this.value = 10; // Si es mayor que 10, lo pone en 10
        }
    });
    
    /**
     * Evento click para el botón de editar notas.
     * Permite quitar la propiedad readonly de los inputs de nota y deshabilita el botón de editar.
     */
    $(document).on('click', '#editarNotasBtn', function (e) {
        e.preventDefault();
        $('.nota-input').prop('readonly', false);
        $(this).prop('disabled', true);
    });

    // Evitar ejecución duplicada del script.
    if (window.notaScriptEjecutado) {
        console.log("⚠️ notas.js ya se ejecutó, deteniendo ejecución duplicada.");
        return;
    }
    window.notaScriptEjecutado = true;

    /**
     * Obtener la lista de cursos y agregarlos al select "id_curso".
     */
    $.get('../../controllers/getCursos.php', function (response) {
        console.log("📌 Cursos recibidos:", response);
        $('#id_curso').empty().append('<option value="">Seleccione un curso</option>');
        response.forEach(curso => {
            $('#id_curso').append(`<option value="${curso.ID_Curso}">${curso.Nombre}</option>`);
        });
    }, 'json').fail(function (jqXHR, textStatus, errorThrown) {
        console.error("❌ Error al obtener cursos:", textStatus, errorThrown);
    });

    /**
 * Evento "change" del select "id_curso" para cargar la lista de alumnos del curso seleccionado.
 */
    $('#id_curso').change(function () {
        let idCurso = $(this).val();
        if (!idCurso) {
            console.log("🛑 Ningún curso seleccionado, limpiando tabla de alumnos.");
            $('#alumnosTableBody').empty();
            $('#alumnosContainer').hide();
            return;
        }
        console.log(`🔎 Obteniendo alumnos para el curso ${idCurso}...`);

        $.get(base_path + 'mainpage.php?route=getAlumnosPorCurso', { idCurso }, function (response) {
            console.log("📌 Alumnos recibidos:", response);
            let html = '';
            if (response.length > 0) {
                response.forEach(alumno => {
                    html += `<tr>
                    <td><input type="radio" name="alumno" class="alumno-radio" value="${alumno.ID_Alumno}"></td>
                    <td>${alumno.Nombre}</td>
                    <td>${alumno.Apellido1}</td>
                    <td>${alumno.Apellido2}</td>
                    <td>${alumno.ID_Alumno}</td>
                    <td>
                        <button class="gestionarNotas btn btn-primary btn-sm" data-id="${alumno.ID_Alumno}" disabled>
                            Gestionar Notas
                        </button>
                    </td>
                </tr>`;
                });
                $('#alumnosTableBody').html(html);
                $('#alumnosContainer').fadeIn().css('display', 'block');

                // Agregar evento para habilitar/deshabilitar los botones
                $('.alumno-radio').change(function () {
                    // Deshabilitar todos los botones
                    $('.gestionarNotas').prop('disabled', true);

                    // Habilitar solo el botón de la fila seleccionada
                    let selectedAlumnoId = $('input[name="alumno"]:checked').val();
                    $(`button[data-id="${selectedAlumnoId}"]`).prop('disabled', false);
                    // Ocultar las notas al cambiar de alumno
                    $('#notasContainer').hide();
                });
            } else {
                $('#alumnosTableBody').html('<tr><td colspan="6" class="text-center">No hay alumnos en este curso</td></tr>');
                $('#alumnosContainer').fadeIn().css('display', 'block');
            }
        }, 'json').fail(function (jqXHR, textStatus, errorThrown) {
            console.error("❌ Error al obtener alumnos:", textStatus, errorThrown);
        });
    });

    /**
     * Evento click para gestionar las notas de un alumno.
     * Al hacer clic en el botón "Gestionar Notas", se obtiene el ID del alumno y se solicitan
     * los módulos y unidades formativas del curso, así como las notas existentes para el alumno.
     */
    $(document).on('click', '.gestionarNotas', function (e) {
        e.preventDefault();
        let idAlumno = $(this).data('id');
        let idCurso = $('#id_curso').val();
        console.log(`📌 Gestionando notas para el alumno ${idAlumno} en el curso ${idCurso}`);

        // Solicitar módulos y unidades formativas para el curso
        $.get(base_path + 'mainpage.php?route=getModulosUnidadesPorCurso', { idCurso }, function (response) {
            console.log("📌 Respuesta cruda del servidor:", response);
            try {
                let datos = Array.isArray(response) ? response : JSON.parse(response);
                if (datos.length > 0) {
                    let html = '';
                    let modulos = {};
                    // Agrupar unidades formativas en sus módulos basándose en ID_Modulo
                    datos.forEach(item => {
                        if (item.Tipo === 'Modulo') {
                            modulos[item.ID] = {
                                nombre: item.Nombre,
                                unidades: []
                            };
                        } else if (item.Tipo === 'Unidad_Formativa' && item.ID_Modulo) {
                            if (modulos[item.ID_Modulo]) {
                                modulos[item.ID_Modulo].unidades.push(item);
                            } else {
                                console.warn(`⚠️ Unidad sin módulo: ${item.Nombre} (ID_Modulo: ${item.ID_Modulo})`);
                            }
                        }
                    });
                    // Construir la tabla de módulos y unidades
                    Object.keys(modulos).forEach(idModulo => {
                        let modulo = modulos[idModulo];
                        html += `<tr class="table-primary">
                            <td colspan="3"><strong>ID Módulo: ${idModulo} - ${modulo.nombre}</strong></td>
                        </tr>`;
                        if (modulo.unidades.length > 0) {
                            modulo.unidades.forEach(unidad => {
                                html += `<tr data-modulo="${idModulo}">
                                    <td>↳ ID Unidad: ${unidad.ID} - ${unidad.Nombre}</td>
                                    <td>Unidad Formativa</td>
                                    <td>
                                        <input type="number" class="form-control nota-input" 
                                            data-id="${unidad.ID}" 
                                            data-tipo="Unidad_Formativa" 
                                            step="0.01" min="0" max="10" >
                                    </td>
                                </tr>`;
                            });
                        } else {
                            html += `<tr>
                                <td colspan="3" class="text-center text-muted">No hay unidades formativas</td>
                            </tr>`;
                        }
                    });
                    $('#notasTableBody').html(html);
                    $('#notasContainer').fadeIn().css('display', 'block');
                    $('#id_alumno').val(idAlumno);
                } else {
                    $('#notasTableBody').html('<tr><td colspan="3" class="text-center">No hay módulos ni unidades formativas disponibles</td></tr>');
                }
            } catch (error) {
                console.error("❌ Error al parsear JSON:", error);
                console.log("⚠️ Respuesta del servidor:", response);
            }
        }, 'json').fail(function (jqXHR, textStatus, errorThrown) {
            console.error("❌ Error al obtener módulos y unidades formativas:", textStatus, errorThrown);
            console.log("⚠️ Respuesta del servidor con error:", jqXHR.responseText);
        });

        // Solicitar las notas existentes para el alumno en el curso
        $.get(base_path + 'mainpage.php?route=getNotasPorAlumno', { idAlumno: idAlumno, idCurso: idCurso }, function (responseNotas) {
            console.log("📌 Notas existentes:", responseNotas);
            if (responseNotas.length > 0) {
                modoNotas = "update";
                // Asignar los valores de las notas a los inputs correspondientes y ponerlos en readonly
                responseNotas.forEach(nota => {
                    if (nota.Tipo_Nota === 'Unidad_Formativa') {
                        let $input = $(`input.nota-input[data-id="${nota.ID_Unidad_Formativa}"][data-tipo="Unidad_Formativa"]`);
                        $input.val(nota.Calificación);
                        $input.prop('readonly', true);
                    }
                    // Se puede agregar lógica similar para notas de tipo "Modulo"
                });
                $('#editarNotasBtn').show();
            } else {
                modoNotas = "store";
            }
        }, 'json').fail(function (jqXHR, textStatus, errorThrown) {
            console.error("❌ Error al obtener las notas del alumno:", textStatus, errorThrown);
        });
    });

    function validarNota(input) {
        let valor = parseFloat(input.value);
    
        if (isNaN(valor) || valor < 0) {
            input.value = 0;  // Si es menor que 0, lo pone en 0
        } else if (valor > 10) {
            input.value = 10; // Si es mayor que 10, lo pone en 10
        }
    }
    

    /**
     * Evento click para guardar las notas.
     *
     * Recopila las calificaciones ingresadas para cada unidad formativa, valida que se haya seleccionado
     * un alumno y que se hayan ingresado calificaciones, y envía los datos al servidor mediante una petición AJAX.
     * El endpoint se determina según si se están actualizando notas o insertando nuevas.
     *
     * @returns {void}
     */
    $('#guardarNotaBtn').click(function (e) {
        e.preventDefault();
        let idAlumno = $('#alumnosTableBody').find('input[type="radio"]:checked').val();
        let idCurso = $('#id_curso').val();
        let notas = [];

        if (!idAlumno) {
            alert("Debe seleccionar un alumno.");
            return;
        }

        // Recorrer cada input de nota para recopilar las calificaciones
        $('.nota-input').each(function () {
            let idUnidadFormativa = $(this).data('id');
            let tipoNota = $(this).data('tipo');
            let calificacion = $(this).val();
            let idModuloAsociado = $(this).closest('tr').data('modulo');

            if (tipoNota === "Unidad_Formativa" && (!idModuloAsociado || idModuloAsociado === "null")) {
                console.error(`❌ No se encontró el módulo para la unidad formativa ${idUnidadFormativa}`);
                return;
            }

            if (calificacion !== "" && calificacion !== undefined) {
                let nota = {
                    id_alumno: idAlumno,
                    id_curso: idCurso,
                    tipo_nota: tipoNota,
                    calificacion: parseFloat(calificacion),
                    id_modulo: (tipoNota === "Unidad_Formativa") ? idModuloAsociado : null,
                    id_unidad_formativa: (tipoNota === "Unidad_Formativa") ? idUnidadFormativa : null
                };
                notas.push(nota);
            }
        });

        if (notas.length === 0) {
            alert("Debe ingresar al menos una calificación.");
            return;
        }

        console.log("📌 Datos enviados al servidor:", JSON.stringify({ notas }));

        // Determinar el endpoint según el modo de operación
        let endpoint = (modoNotas === "update") ? 'updateNota' : 'storeNota';

        $.ajax({
            url: base_path + 'mainpage.php?route=' + endpoint,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ notas }),
            success: function (response) {
                console.log("📌 Respuesta del servidor:", response);
                if (response.success) {
                    alert("✅ Notas guardadas correctamente");
                    if (modoNotas === "update") {
                        $('.nota-input').prop('readonly', true);
                        $('#editarNotasBtn').prop('disabled', false);
                    }
                    $('#notasContainer').hide();
                } else {
                    alert("❌ No se pudieron guardar algunas notas, errores: " + response.errors.length);
                    console.error(response.errors);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("❌ Error al guardar las notas:", textStatus, errorThrown);
                alert("❌ Error al enviar los datos.");
            }
        });
    });

    /**
     * Actualiza el enlace para ver el listado de notas de un alumno en un curso.
     *
     * Obtiene los valores del curso y del alumno seleccionado y actualiza el atributo href
     * del enlace con id "linkNotas" para redirigir al listado correspondiente.
     *
     * @returns {void}
     */
    function actualizarLinkNotas() {
        let idCurso = document.getElementById('id_curso').value;
        let idAlumno = $('input[name="alumno"]:checked').val();
        let linkNotas = document.getElementById('linkNotas');

        if (idCurso !== "" && idAlumno !== undefined && idAlumno !== "") {
            linkNotas.href = "/listados/listadoNotasAlumnoCurso.php?idCurso=" +
                encodeURIComponent(idCurso) +
                "&idAlumno=" + encodeURIComponent(idAlumno);
        } else {
            linkNotas.href = "#";
        }
    }

    // Actualizar el enlace cuando se cambia el curso o se selecciona un alumno
    $('#id_curso').change(actualizarLinkNotas);
    $(document).on('change', 'input[name="alumno"]', actualizarLinkNotas);
});
