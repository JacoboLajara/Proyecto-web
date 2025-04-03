<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Horas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Registro de Horas</h1>
        <form action="controlador_registro.php" method="POST">
            <div class="form-group">
                <label for="entrada_manana">Entrada Mañana:</label>
                <input type="time" class="form-control" id="entrada_manana" name="entrada_manana">
            </div>
            <div class="form-group">
                <label for="salida_manana">Salida Mañana:</label>
                <input type="time" class="form-control" id="salida_manana" name="salida_manana">
            </div>
            <div class="form-group">
                <label for="entrada_tarde">Entrada Tarde:</label>
                <input type="time" class="form-control" id="entrada_tarde" name="entrada_tarde">
            </div>
            <div class="form-group">
                <label for="salida_tarde">Salida Tarde:</label>
                <input type="time" class="form-control" id="salida_tarde" name="salida_tarde">
            </div>
            <button type="submit" class="btn btn-primary">Registrar</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>