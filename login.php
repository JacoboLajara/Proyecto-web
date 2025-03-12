<?php
require_once __DIR__ . '/init.php';
require './config/conexion.php';

// Depuración inicial
file_put_contents('debug.log', "DEBUG (login.php) - Inicio - session_id(): " . session_id() . "\n", FILE_APPEND);
file_put_contents('debug.log', "DEBUG (login.php) - Usuario en sesión: " . ($_SESSION['usuario'] ?? 'No definido') . "\n", FILE_APPEND);
file_put_contents('debug.log', "DEBUG (login.php) - Rol en sesión: " . ($_SESSION['rol'] ?? 'No definido') . "\n", FILE_APPEND);

if (isset($_SESSION['usuario'])) {
    file_put_contents('debug.log', "DEBUG (login.php) - Usuario ya autenticado, redirigiendo a backoffice\n", FILE_APPEND);
    header("Location: views/users/backoffice.php");
    exit();
}

$_SESSION['auth_token'] = bin2hex(random_bytes(32));

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;

    file_put_contents('debug.log', "DEBUG (login.php) - Procesando inicio de sesión para usuario: $nombre_usuario\n", FILE_APPEND);
    file_put_contents('debug.log', "DEBUG (login.php) - Procesando usuario con : $password\n", FILE_APPEND);

    if ($nombre_usuario && $password !== null) {
        $stmt = $conn->prepare("
            SELECT Usuario.DNI_NIE, Usuario.password, Rol.Nombre AS NombreRol
            FROM Usuario
            JOIN Rol ON Usuario.ID_Rol = Rol.ID_Rol
            WHERE Usuario.DNI_NIE = ?
        ");
        $stmt->bind_param("s", $nombre_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();

        if ($usuario) {
            session_regenerate_id(true);
            $_SESSION['usuario'] = $usuario['DNI_NIE'];
            $_SESSION['rol'] = $usuario['NombreRol'];
            $_SESSION['password'] = $usuario['password'];

            if($usuario['DNI_NIE'] === '00000000A'){
                header("Location: cambiar_admin.php");
            }

            if (empty($usuario['password'])) {
                file_put_contents('debug.log', "DEBUG (login.php) - El usuario tiene una contraseña vacía. Redirigiendo a cambiar_password.php\n", FILE_APPEND);
                header("Location: cambiar_password.php");
                exit();
            }
            

            file_put_contents('debug.log', "DEBUG (login.php) - Inicio de sesión exitoso para usuario: " . $_SESSION['usuario'] . "\n", FILE_APPEND);
            file_put_contents('debug.log', "DEBUG (login.php) - el password guardado es: " . $_SESSION['password'] . "\n", FILE_APPEND);

            // **2️⃣ Verificamos la contraseña ingresada con la encriptada en la BD**
            if (password_verify($password, $usuario['password'])) {
                // **✅ Contraseña correcta, iniciamos sesión**
                session_regenerate_id(true);
                $_SESSION['usuario'] = $usuario['DNI_NIE'];
                $_SESSION['rol'] = $usuario['NombreRol'];

                file_put_contents('debug.log', "DEBUG (login.php) - Inicio de sesión exitoso para usuario: " . $_SESSION['usuario'] . "\n", FILE_APPEND);
                if ($_SESSION['rol'] === 'Alumno') {
                    // Obtener el ID del Alumno y guardarlo en la sesión
                    $stmt = $conn->prepare("SELECT ID_Alumno FROM Alumno WHERE ID_Alumno = ?");
                    $stmt->bind_param("s", $_SESSION['usuario']);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    $alumno = $resultado->fetch_assoc();

                    if ($alumno) {
                        $_SESSION['idAlumno'] = $alumno['ID_Alumno'];
                        file_put_contents('debug.log', "DEBUG (login.php) - ID_Alumno encontrado: " . $_SESSION['idAlumno'] . "\n", FILE_APPEND);
                        header("Location: views/users/backAlumnos.php");
                    } else {
                        file_put_contents('debug.log', "ERROR (login.php) - No se pudo encontrar el ID del alumno para DNI_NIE: " . $_SESSION['usuario'] . "\n", FILE_APPEND);
                        $error = "❌ No se pudo identificar al alumno.";
                    }
                } elseif ($usuario['DNI_NIE'] === '00000000A') {
                    header("Location: cambiar_admin.php");

                } else {
                    header("Location: views/users/backoffice.php");
                }
                exit();
            } else {
                file_put_contents('debug.log', "DEBUG (login.php) - Usuario o contraseña incorrectos\n", FILE_APPEND);
                $error = "❌ Usuario o contraseña incorrectos.";
            }
        } else {
            file_put_contents('debug.log', "DEBUG (login.php) - Campos incompletos\n", FILE_APPEND);
            $error = "❌ Debes completar todos los campos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login - GestAcademia</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(to bottom right, #2145e6, #76a8f7);
            font-family: 'Arial', sans-serif;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.85);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 360px;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }

        .login-container input {
            width: calc(100% - 20px);
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .login-container button {
            width: calc(100% - 20px);
            padding: 12px;
            background-color: #007BFF;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 8px;
            transition: background-color 0.3s ease;
            margin-top: 15px;
        }

        .login-container button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <?php if ($error): ?>
            <p class="error-message"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>

</html>