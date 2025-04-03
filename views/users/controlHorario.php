<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control de Horario</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../../CSS/formularios.css" />
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1>Registro de Horario Diario</h1>

        <form id="formHorario">
            <input type="hidden" name="ID_Usuario" id="ID_Usuario" value="<?= $_SESSION['usuario']['ID_Usuario'] ?>">

            <div>
                <label>Fecha:</label>
                <input type="date" name="Fecha" id="Fecha" required>
            </div>

            <div>
                <label>Hora Entrada Mañana:</label>
                <input type="time" name="Hora_Entrada_Mañana">
            </div>

            <div>
                <label>Hora Salida Mañana:</label>
                <input type="time" name="Hora_Salida_Mañana">
            </div>

            <div>
                <label>Hora Entrada Tarde:</label>
                <input type="time" name="Hora_Entrada_Tarde">
            </div>

            <div>
                <label>Hora Salida Tarde:</label>
                <input type="time" name="Hora_Salida_Tarde">
            </div>

            <div>
                <label>Tipo de Jornada:</label>
                <select name="Tipo_Jornada" required>
                    <option value="Completa">Completa</option>
                    <option value="Parcial_Mañana">Parcial Mañana</option>
                    <option value="Parcial_Tarde">Parcial Tarde</option>
                    <option value="Turno">Turno</option>
                </select>
            </div>

            <div>
                <label>Tipo de Día:</label>
                <select name="Tipo_Dia" required>
                    <option value="Ordinario">Ordinario</option>
                    <option value="Vacaciones">Vacaciones</option>
                    <option value="Baja">Baja</option>
                    <option value="Asuntos_Propios">Asuntos Propios</option>
                    <option value="Permiso">Permiso</option>
                </select>
            </div>

            <div>
                <label>Observaciones:</label>
                <textarea name="Observaciones"></textarea>
            </div>

            <div>
                <label>URL del justificante (PDF, imagen, etc):</label>
                <input type="url" name="Justificante_URL" placeholder="https://...">
            </div>

            <button type="submit">Guardar Registro</button>
        </form>

        <div id="respuesta" class="mt-3"></div>
    </div>

    <script>
        $('#formHorario').submit(function (e) {
            e.preventDefault();

            const datos = Object.fromEntries(new FormData(this).entries());

            $.ajax({
                url: '../../mainpage.php?route=storeHorarioEmpleado',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(datos),
                success: function (response) {
                    $('#respuesta').html(`<div class="alert alert-${response.success ? 'success' : 'danger'}">${response.message}</div>`);
                },
                error: function () {
                    $('#respuesta').html('<div class="alert alert-danger">❌ Error al enviar los datos</div>');
                }
            });
        });
    </script>
</body>
</html>
