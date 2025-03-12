<?php
session_start();
require './config/conexion.php';

// Verificar si el usuario '00000000A' sigue sin credenciales
$sql = "SELECT DNI_NIE, password FROM Usuario WHERE DNI_NIE = '00000000A'";
$result = $conn->query($sql);
$admin = $result->fetch_assoc();

if (!$admin || $admin['password'] !== NULL) {
    header("Location: login.php");
    exit();
}

// Procesar el formulario de cambio de credenciales
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_usuario = $_POST['nuevo_usuario'] ?? '';
    $nueva_password = $_POST['nueva_password'] ?? '';
    $confirmar_password = $_POST['confirmar_password'] ?? '';

    if ($nueva_password !== $confirmar_password) {
        $error = "Las contraseñas no coinciden.";
    } elseif (strlen($nueva_password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        $password_hash = password_hash($nueva_password, PASSWORD_BCRYPT);

        // Actualizar las credenciales del administrador
        $stmt = $conn->prepare("UPDATE Usuario SET DNI_NIE = ?, password = ? WHERE DNI_NIE = '00000000A'");
        $stmt->bind_param("ss", $nuevo_usuario, $password_hash);
        if ($stmt->execute()) {
            // Redirigir al login con nuevas credenciales
            header("Location: mainpage.php");
            exit();
        } else {
            $error = "Error al actualizar el usuario.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Usuario y Contraseña</title>
    <link rel="stylesheet" href="CSS/estilos.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: rgb(33, 69, 230);
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
        <h2>Cambiar Usuario y Contraseña</h2>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <form action="cambiar_admin.php" method="POST">
            <input type="text" name="nuevo_usuario" placeholder="Nuevo DNI/NIE" required>
            <input type="password" name="nueva_password" placeholder="Nueva Contraseña" required>
            <input type="password" name="confirmar_password" placeholder="Confirmar Contraseña" required>
            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>
