<?php
// Configurar la ruta para guardar las sesiones
ini_set('session.save_path', realpath(__DIR__ . '/sesiones'));
ini_set('session.cookie_path', '/');
session_start();
require './config/conexion.php';

// Si el usuario no ha iniciado sesión, redirigir a login.php
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario_actual = $_SESSION['usuario'];

// Procesar el formulario de cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva_password = $_POST['nueva_password'] ?? '';
    $confirmar_password = $_POST['confirmar_password'] ?? '';

    if ($nueva_password !== $confirmar_password) {
        $error = "Las contraseñas no coinciden.";
    } elseif (strlen($nueva_password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        // Encriptar la nueva contraseña
        $password_hash = password_hash($nueva_password, PASSWORD_BCRYPT);

        // Actualizar la contraseña en la base de datos
        $stmt = $conn->prepare("UPDATE Usuario SET password = ? WHERE DNI_NIE = ?");
        $stmt->bind_param("ss", $password_hash, $usuario_actual);

        if ($stmt->execute()) {
            // Redirigir a mainpage.php después del cambio
            session_unset();
            session_destroy();
            $_SESSION['usuario'] = $usuario_actual;
            header("Location: mainpage.php");
            exit();
        } else {
            $error = "Error al actualizar la contraseña.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .login-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 320px;
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .login-container input {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .login-container button {
            width: calc(100% - 20px);
            padding: 10px;
            background-color: #007BFF;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Cambiar Contraseña</h2>
        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
        <form action="cambiar_password.php" method="POST">
            <input type="password" name="nueva_password" placeholder="Nueva Contraseña" required>
            <input type="password" name="confirmar_password" placeholder="Confirmar Contraseña" required>
            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</body>

</html>
