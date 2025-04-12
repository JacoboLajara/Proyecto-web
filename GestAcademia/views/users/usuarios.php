<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../js/usuarios.js" defer></script>
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="../../CSS/estilohojacontacto.css" />
    <link rel="stylesheet" href="../../CSS/formularios.css" />
</head>

<body >
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Panel de control de usuarios</h2>
        <ul>
            <li><a href="./views/users/backoffice.php"><i class="fas fa-home"></i> Volver al panel central</a></li>
            <!-- <li>
                <a href="#" onclick="document.getElementById('userList').style.display='block'">
                    <i class="fas fa-search"></i> Buscar Usuarios
                </a>
            </li> -->
            <li><a href="/listados/listadoUsuarios.php"><i class="fas fa-list"></i> Listado de todos los usuarios</a></li>
            <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <div class="header">
            <h1>Gestión de Usuarios</h1>
        </div>

        <!-- Listado de usuarios -->
        <div id="userList" class="container mt-4">
            <h2 class="text-center">Seleccionar Usuario</h2>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>DNI/NIE</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['DNI_NIE']); ?></td>
                            <td>
                                <button class="btn btn-primary open-modal"
                                    data-dni="<?php echo htmlspecialchars($user['DNI_NIE']); ?>">
                                    Seleccionar
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Edición de Usuario -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updatePasswordForm">
                        <input type="hidden" name="dni" id="modalDni">
                        <div class="mb-3">
                            <label for="new_password" class="form-label"><b>Nueva Contraseña:</b></label>
                            <input type="password" name="new_password" id="new_password" class="form-control" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success">Actualizar Contraseña</button>
                        </div>
                    </form>
                    <hr>
                    <form id="deletePasswordForm">
                        <input type="hidden" name="dni" id="modalDniDelete">
                        <div class="text-center">
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('¿Estás seguro de eliminar la contraseña?');">
                                Eliminar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    

</body>

</html>