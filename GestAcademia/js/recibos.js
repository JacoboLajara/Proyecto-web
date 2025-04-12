$(document).ready(function () {
    console.log("✅ Cargando recibos.js...");

    /**
     * Base path para las peticiones AJAX.
     * Se asume que el archivo router.php se encuentra en la raíz.
     * @type {string}
     */
    var base_path = '../router.php?route=';

    /**
     * Realiza una petición AJAX para cargar y actualizar la tabla de recibos.
     * Utiliza los datos serializados del formulario de filtros (#filtroRecibos) y
     * actualiza el contenido HTML del elemento #tablaRecibos con la respuesta recibida.
     *
     * @function cargarRecibos
     * @returns {void}
     */
    function cargarRecibos() {
        $.ajax({
            url: base_path + 'recibos',
            type: 'GET',
            data: $('#filtroRecibos').serialize(),
            dataType: 'html',
            success: function (response) {
                $('#tablaRecibos').html(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("❌ Error al recargar recibos:", textStatus, errorThrown);
            }
        });
    }

    // Manejar el envío del formulario de filtrado de recibos.
    // Previene el comportamiento por defecto y recarga la tabla de recibos.
    $('#filtroRecibos').submit(function (e) {
        e.preventDefault();
        cargarRecibos();
    });

    /**
     * Delegado de evento para el checkbox que marca un recibo como pagado.
     * Al detectar un cambio en el checkbox (.chkPagado) dentro de #tablaRecibos,
     * envía una petición AJAX para actualizar el estado del recibo.
     *
     * @event change
     */
    $('#tablaRecibos').on('change', '.chkPagado', function () {
        var reciboId = $(this).data('id');
        $.ajax({
            url: base_path + 'actualizarPagoRecibo',
            type: 'POST',
            data: { id: reciboId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Recargar la tabla de recibos en vez de mostrar un alert.
                    cargarRecibos();
                } else {
                    console.error("Error al actualizar el recibo: " + response.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("❌ Error en la solicitud AJAX:", textStatus, errorThrown);
            }
        });
    });

    /**
     * Manejador de evento para abrir el modal de edición de un recibo.
     * Extrae los datos del recibo (ID, estado y fecha de pago) y los asigna
     * a los campos correspondientes en el modal de edición antes de mostrarlo.
     *
     * @event click
     */
    $('#tablaRecibos').on('click', '.editarReciboBtn', function () {
        var idRecibo = $(this).data('id');
        var estado = $(this).data('estado');
        var fechaPago = $(this).data('fecha_pago');
        $('#editarReciboId').val(idRecibo);
        $('#estadoRecibo').val(estado);
        $('#fechaPago').val(fechaPago || '');
        $('#editarReciboModal').modal('show');
    });

    /**
     * Manejador de evento para guardar los cambios realizados en el modal de edición del recibo.
     * Envía una petición AJAX con los datos actualizados y, si la actualización es exitosa,
     * cierra el modal y recarga la tabla de recibos.
     *
     * @event click
     */
    $('#guardarEdicionReciboBtn').click(function () {
        var id = $('#editarReciboId').val();
        var estado = $('#estadoRecibo').val();
        var fecha_pago = $('#fechaPago').val();

        $.ajax({
            url: base_path + 'updateRecibo',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id: id, estado: estado, fecha_pago: fecha_pago }),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#editarReciboModal').modal('hide');
                    cargarRecibos();
                } else {
                    console.error("Error al actualizar el recibo: " + response.message);
                    $('#editarReciboModal .modal-body').prepend('<div class="alert alert-danger" role="alert">Error: ' + response.message + '</div>');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("❌ Error en la solicitud de actualización:", textStatus, errorThrown);
                $('#editarReciboModal').modal('hide');
                cargarRecibos();
            }
        });
    });

    /**
     * Evento que gestiona el cambio en el estado del recibo.
     * Si el estado seleccionado es "Pendiente", limpia el campo de fecha de pago.
     *
     * @event change
     */
    document.getElementById('estadoRecibo').addEventListener('change', function () {
        const fechaPagoInput = document.getElementById('fechaPago');
        if (this.value === 'Pendiente') {
            // Borra el valor del input fechaPago
            fechaPagoInput.value = "";
            // Opcional: Oculta el input si lo prefieres
            // fechaPagoInput.style.display = 'none';
        } else {
            // Opcional: Si se oculta, restaurar la visibilidad
            // fechaPagoInput.style.display = 'block';
        }
    });

    /**
     * Actualiza el enlace para listar los recibos en función de los filtros aplicados.
     * Serializa los datos del formulario de filtros y construye la URL para el listado.
     *
     * @function actualizarLinkListadoRecibos
     * @returns {void}
     */
    function actualizarLinkListadoRecibos() {
        // Serializa los datos del formulario de filtros
        var query = $('#filtroRecibos').serialize();
        console.log("Query string:", query);
        // Construir la URL al fichero listadoRecibos.php (ajusta la ruta según la estructura de tu proyecto)
        var url = "/listados/listadoRecibos.php?" + query;
        // Asignar la URL al enlace en el sidebar
        $('#linkListadoRecibos').attr('href', url);
    }

    // Actualiza el enlace cada vez que se cambie algún filtro.
    $('#filtroRecibos input, #filtroRecibos select').on('change', actualizarLinkListadoRecibos);

    // Llama a la función al cargar la página para actualizar el enlace.
    $(document).ready(function () {
        actualizarLinkListadoRecibos();
    });
});
