$(document).ready(function () {
    const usuario = window.ID_USUARIO; // se define en el HTML

    const hoy = new Date().toISOString().slice(0, 10); // YYYY-MM-DD
    $('#fechaActual').text(hoy.split('-').reverse().join('/')); // DD/MM/YYYY

    if (!usuario) {
        $('#respuesta').html('<div class="alert alert-danger">No se ha identificado al usuario.</div>');
        return;
    }

    // Consultar si ya existe registro
    $.get(`/mainpage.php?route=listarPorUsuario&idUsuario=${usuario}&inicio=${hoy}&fin=${hoy}`, function (response) {
        console.log("📌 Respuesta de listarPorUsuario:", response);

        if (!response.success) {
            $('#respuesta').html(`<div class="alert alert-danger">${response.message}</div>`);
            return;
        }

        const datos = response.datos.length > 0 ? response.datos[0] : null;

        let botonesHtml = '';
        if (!datos || !datos.Hora_Entrada_Mañana) {
            botonesHtml += `<button class="btn-fichar" data-turno="mañana" data-accion="entrada">Fichar entrada mañana</button>`;
        } else {
            $('#entradaM').text(datos.Hora_Entrada_Mañana);
            if (!datos.Hora_Salida_Mañana) {
                botonesHtml += `<button class="btn-fichar" data-turno="mañana" data-accion="salida">Fichar salida mañana</button>`;
            } else {
                $('#salidaM').text(datos.Hora_Salida_Mañana);
            }
        }

        if (!datos || !datos.Hora_Entrada_Tarde) {
            botonesHtml += `<button class="btn-fichar" data-turno="tarde" data-accion="entrada">Fichar entrada tarde</button>`;
        } else {
            $('#entradaT').text(datos.Hora_Entrada_Tarde);
            if (!datos.Hora_Salida_Tarde) {
                botonesHtml += `<button class="btn-fichar" data-turno="tarde" data-accion="salida">Fichar salida tarde</button>`;
            } else {
                $('#salidaT').text(datos.Hora_Salida_Tarde);
            }
        }

        $('#botonesTurno').html(botonesHtml);
    }, 'json');

    // Fichar entrada o salida
    $(document).on('click', '.btn-fichar', function () {
        const turno = $(this).data('turno');
        const accion = $(this).data('accion');
        const campo = (accion === 'entrada' ? 'Hora_Entrada_' : 'Hora_Salida_') + (turno === 'mañana' ? 'Mañana' : 'Tarde');

        const horaSistema = new Date().toTimeString().slice(0, 5); // HH:MM

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
