<?php
// Configurar la ruta para guardar las sesiones
require_once __DIR__ . '/init.php';
// Guardar el session_id() en un archivo al llegar a mainpage.php
/*file_put_contents("session_debug.txt", "Después de redirigir:\n" . session_id() . "\n", FILE_APPEND);*/

require './config/conexion.php';

// 📌 DEBUG: Verificar que la sesión sigue activa
/*echo "<pre>";
echo "DEBUG - session_id(): " . session_id() . "<br>";
echo "DEBUG - Usuario en sesión: " . ($_SESSION['usuario'] ?? 'No definido') . "<br>";
echo "DEBUG - Rol en sesión: " . ($_SESSION['rol'] ?? 'No definido') . "<br>";
echo "</pre>";*/

// Definir si hay usuario autenticado y su rol
$usuarioAutenticado = isset($_SESSION['usuario']);
$rol = $usuarioAutenticado ? $_SESSION['rol'] : null;

// 1️⃣ Verificar si la base de datos está vacía (sin roles creados)
$result = $conn->query("SELECT COUNT(*) as total FROM Rol");
$row = $result->fetch_assoc();
if ($row['total'] == 0) {
    header("Location: setup.php");
    exit();
}

// Incluir router.php
require_once 'router.php';

// 3️⃣ Si hay sesión iniciada y el usuario es el admin por defecto, forzarlo a cambiar sus credenciales
if ($usuarioAutenticado && $_SESSION['usuario'] === '00000000A') {
    header("Location: cambiar_admin.php");
    exit();
}

// 2️⃣ Si NO hay sesión iniciada, NO redirigir a login; se muestra la página principal con el botón "Iniciar Sesión"
// El siguiente bloque se comenta para que la página principal sea accesible sin estar logueado

//if (!isset($_SESSION['usuario'])) {
//  header("Location: login.php");
// exit();
//}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a GestAcademia</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #4a90e2, #9013fe);
            color: #fff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            text-align: center;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        .btn {
            background-color: #ff6f61;
            color: white;
            padding: 15px 30px;
            font-size: 1rem;
            text-transform: uppercase;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #e5574b;
        }
        .container {
            text-align: center;
            margin-bottom: 40px;
        }
        .image-gallery {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            padding: 20px;
        }
        .image-gallery img {
            width: 200px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }
        .image-gallery img:hover {
            transform: scale(1.05);
        }
        .footer {
            text-align: center;
            font-size: 0.9rem;
            margin-top: auto;
            padding: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Bienvenido a GestAcademia</h1>
        <p>Gestión eficiente y moderna para tu centro de formación.</p>
    </header>

    <div class="container">
        <img src="./IMG/logoselect.png" alt="Logo Select Logical" style="max-width: 300px; margin-bottom: 20px;">
        <br>
        <a href="login.php" class="btn">Iniciar Sesión</a>
    </div>

    <div class="image-gallery">
        <img src="./IMG/animales1.jpg" alt="Animales">
        <img src="./IMG/animales2.jpg" alt="Animales">
        <img src="./IMG/gente1.jpg" alt="Gente">
        <img src="./IMG/gentes2.jpg" alt="Gente">
        <img src="./IMG/paisaje.jpg" alt="Paisaje">
        <img src="./IMG/paisaje2.jpg" alt="Paisaje">
    </div>

    <footer class="footer">
        &copy; 2025 GestAcademia - Todos los derechos reservados.
    </footer>
</body>
</html>
