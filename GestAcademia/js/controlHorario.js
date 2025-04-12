// controlHorario.js
$(document).ready(function () {
    const usuario = window.ID_USUARIO;
    const hoy = new Date();
    const hoyStr = hoy.toISOString().slice(0, 10); // YYYY-MM-DD
    $('#fechaActual').text(hoyStr.split('-').reverse().join('/')); // DD/MM/YYYY

    if (!usuario) {
        $('#respuesta').html('<div class="alert alert-danger">No se ha identificado al usuario.</div>');
        return;
    }

    // Calcular el lunes de esta semana
    const diaSemana = hoy.getDay(); // 0 domingo, 1 lunes, ..., 6 sábado
    const offset = diaSemana === 0 ? 6 : diaSemana - 1;
    const lunes = new Date(hoy);
    lunes.setDate(hoy.getDate() - offset);
    const lunesStr = lunes.toISOString().slice(0, 10);

    // Obtener todos los registros desde el lunes hasta hoy
    $.get(`/mainpage.php?route=listarPorUsuario&idUsuario=${usuario}&inicio=${lunesStr}&fin=${hoyStr}`, function (response) {
        if (!response.success) {
            $('#respuesta').html(`<div class="alert alert-danger">${response.message}</div>`);
            return;
        }

        const datosSemana = response.datos;
        let tablaHtml = '';
        const minutoMs = 60000;
        const horaActual = new Date().toTimeString().slice(0, 5);
        const esManana = horaActual < '15:00';
        const fechaHoyStr = hoy.toISOString().slice(0, 10);

        datosSemana.forEach(reg => {
            const fecha = reg.Fecha.split('-').reverse().join('/');
            const entradaM = reg.Hora_Entrada_Manana ?? '--:--';
            const salidaM = reg.Hora_Salida_Manana ?? '--:--';
            const entradaT = reg.Hora_Entrada_Tarde ?? '--:--';
            const salidaT = reg.Hora_Salida_Tarde ?? '--:--';

            const horasM = calcularHoras(entradaM, salidaM);
            const horasT = calcularHoras(entradaT, salidaT);

            const nombreDia = new Date(reg.Fecha).toLocaleDateString('es-ES', { weekday: 'long' });

            tablaHtml += `
    <tr>
        <td rowspan="2">${nombreDia.charAt(0).toUpperCase() + nombreDia.slice(1)}</td>
        <td>Mañana</td>
        <td>${entradaM}</td>
        <td>${salidaM}</td>
        <td>${horasM}</td>
    </tr>
    <tr>
        <td>Tarde</td>
        <td>${entradaT}</td>
        <td>${salidaT}</td>
        <td>${horasT}</td>
    </tr>`;

        });

        $('#tablaSemanal tbody').html(tablaHtml);

        // Buscar el registro del día actual para generar botones
        const registroHoy = datosSemana.find(d => d.Fecha === fechaHoyStr) || null;

        let botonesHtml = '';
        const ahora = new Date();
        const ultimaEntradaM = registroHoy?.Hora_Entrada_Manana ? new Date(`${registroHoy.Fecha}T${registroHoy.Hora_Entrada_Manana}`) : null;
        const ultimaEntradaT = registroHoy?.Hora_Entrada_Tarde ? new Date(`${registroHoy.Fecha}T${registroHoy.Hora_Entrada_Tarde}`) : null;

        function puedeFicharSalida(entrada) {
            return entrada && (ahora - entrada >= minutoMs);
        }

        if (!registroHoy) {
            botonesHtml += esManana
                ? `<button class="btn-fichar" data-turno="manana" data-accion="entrada">Fichar entrada mañana</button>`
                : `<button class="btn-fichar" data-turno="tarde" data-accion="entrada">Fichar entrada tarde</button>`;
        } else {
            if (registroHoy.Hora_Entrada_Manana && !registroHoy.Hora_Salida_Manana && puedeFicharSalida(ultimaEntradaM)) {
                botonesHtml += `<button class="btn-fichar" data-turno="manana" data-accion="salida">Fichar salida mañana</button>`;
            }
            if (registroHoy.Hora_Entrada_Tarde && !registroHoy.Hora_Salida_Tarde && puedeFicharSalida(ultimaEntradaT)) {
                botonesHtml += `<button class="btn-fichar" data-turno="tarde" data-accion="salida">Fichar salida tarde</button>`;
            }
            if (!registroHoy.Hora_Entrada_Manana && esManana) {
                botonesHtml += `<button class="btn-fichar" data-turno="manana" data-accion="entrada">Fichar entrada mañana</button>`;
            }
            if (!registroHoy.Hora_Entrada_Tarde && !esManana) {
                botonesHtml += `<button class="btn-fichar" data-turno="tarde" data-accion="entrada">Fichar entrada tarde</button>`;
            }
        }

        $('#botonesTurno').html(botonesHtml);
    }, 'json');

    function calcularHoras(inicio, fin) {
        if (inicio === '--:--' || fin === '--:--') return '--:--';
        const [h1, m1] = inicio.split(':').map(Number);
        const [h2, m2] = fin.split(':').map(Number);
        const totalMin = (h2 * 60 + m2) - (h1 * 60 + m1);
        if (totalMin <= 0) return '--:--';
        const horas = Math.floor(totalMin / 60);
        const minutos = totalMin % 60;
        return `${horas.toString().padStart(2, '0')}:${minutos.toString().padStart(2, '0')}`;
    }

    $(document).on('click', '.btn-fichar', function () {
        const turno = $(this).data('turno');
        const accion = $(this).data('accion');
        const campo = (accion === 'entrada' ? 'Hora_Entrada_' : 'Hora_Salida_') + (turno === 'manana' ? 'Manana' : 'Tarde');
        const horaSistema = new Date().toTimeString().slice(0, 5);

        const datos = {
            ID_Usuario: usuario,
            Fecha: hoyStr,
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

        // Botones de exportación
        $('#exportPDF').click(function () {
            window.location.href = '/mainpage.php?route=exportarHorarioPDF';
        });
    
        $('#exportExcel').click(function () {
            const diaSemana = hoy.getDay() === 0 ? 6 : hoy.getDay() - 1; // Lunes = 0
            const lunes = new Date(hoy);
            lunes.setDate(hoy.getDate() - diaSemana);
    
            const formato = d => d.toISOString().slice(0, 10);
            const inicio = formato(lunes);
            const fin = formato(hoy);
    
            window.location.href = `/exportadores/ExportadorHorarioExcel.php?inicio=${inicio}&fin=${fin}`;
        });
    
});