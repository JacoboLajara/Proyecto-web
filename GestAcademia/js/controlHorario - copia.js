// controlHorario.js
$(document).ready(function () {
    const usuario = window.ID_USUARIO;
    const hoy = new Date().toISOString().slice(0, 10); // YYYY-MM-DD
    $('#fechaActual').text(hoy.split('-').reverse().join('/')); // DD/MM/YYYY

    if (!usuario) {
        $('#respuesta').html('<div class="alert alert-danger">No se ha identificado al usuario.</div>');
        return;
    }

    $.get(`/mainpage.php?route=listarPorUsuario&idUsuario=${usuario}&inicio=${hoy}&fin=${hoy}`, function (response) {
        console.log("📌 Respuesta de listarPorUsuario:", response);
        console.log("🔁 Respuesta completa del backend:", response);
        alert("✅ Datos del backend: " + JSON.stringify(response));


        if (!response.success) {
            $('#respuesta').html(`<div class="alert alert-danger">${response.message}</div>`);
            return;
        }

        const datosArray = response.datos.length > 0 ? response.datos[0] : null;
        const datos = datosArray
            ? Object.fromEntries(Object.entries(datosArray))
            : null;

            console.log("📦 Datos recuperados:", datos);

            // ⬇️ Añade esto para mostrar los horarios en pantalla
            if (datos) {
                if (datos.Hora_Entrada_Manana) {
                    $('#entradaM').text(datos.Hora_Entrada_Manana);
                }
                if (datos.Hora_Salida_Manana) {
                    $('#salidaM').text(datos.Hora_Salida_Manana);
                }
                if (datos.Hora_Entrada_Tarde) {
                    $('#entradaT').text(datos.Hora_Entrada_Tarde);
                }
                if (datos.Hora_Salida_Tarde) {
                    $('#salidaT').text(datos.Hora_Salida_Tarde);
                }
            }
            
        let botonesHtml = '';
        const ahora = new Date();
        const horaActual = ahora.toTimeString().slice(0, 5);
        const horaLimite = '15:00';
        const esManana = horaActual < horaLimite;

        const minutoMs = 60000;
        const ultimaEntradaManana = datos?.Hora_Entrada_Manana ? new Date(`${hoy}T${datos.Hora_Entrada_Manana}`) : null;
        const ultimaEntradaTarde = datos?.Hora_Entrada_Tarde ? new Date(`${hoy}T${datos.Hora_Entrada_Tarde}`) : null;

        function puedeFicharSalida(ultimaEntrada) {
            return ultimaEntrada && (ahora - ultimaEntrada >= minutoMs);
        }

        if (!datos) {
            if (esManana) {
                botonesHtml += `<button class="btn-fichar" data-turno="manana" data-accion="entrada">Fichar entrada mañana</button>`;
            } else {
                botonesHtml += `<button class="btn-fichar" data-turno="tarde" data-accion="entrada">Fichar entrada tarde</button>`;
            }
        } else {
            if (datos.Hora_Entrada_Manana && !datos.Hora_Salida_Manana && puedeFicharSalida(ultimaEntradaManana)) {
                botonesHtml += `<button class="btn-fichar" data-turno="manana" data-accion="salida">Fichar salida mañana</button>`;
            }
            if (datos.Hora_Entrada_Tarde && !datos.Hora_Salida_Tarde && puedeFicharSalida(ultimaEntradaTarde)) {
                botonesHtml += `<button class="btn-fichar" data-turno="tarde" data-accion="salida">Fichar salida tarde</button>`;
            }
            if (!datos.Hora_Entrada_Manana && esManana) {
                botonesHtml += `<button class="btn-fichar" data-turno="manana" data-accion="entrada">Fichar entrada mañana</button>`;
            }
            if (!datos.Hora_Entrada_Tarde && !esManana) {
                botonesHtml += `<button class="btn-fichar" data-turno="tarde" data-accion="entrada">Fichar entrada tarde</button>`;
            }
        }

        $('#botonesTurno').html(botonesHtml);
    }, 'json');

    $(document).on('click', '.btn-fichar', function () {
        const turno = $(this).data('turno');
        const accion = $(this).data('accion');
        const campo = (accion === 'entrada' ? 'Hora_Entrada_' : 'Hora_Salida_') + (turno === 'manana' ? 'Manana' : 'Tarde');

        const horaSistema = new Date().toTimeString().slice(0, 5);

        const datos = {
            ID_Usuario: usuario,
            Fecha: hoy,
            Tipo_Jornada: 'Completa',
            Tipo_Dia: 'Ordinario',
            Observaciones: '',
            Justificante_URL: null
        };
        datos[campo] = horaSistema;

        $.ajax({
            url: '/mainpage.php?route=storeHorarioEmpleado',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(datos),
            success: function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    $('#respuesta').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },
            error: function () {
                $('#respuesta').html(`<div class="alert alert-danger">Error al registrar hora.</div>`);
            }
        });
    });
});
