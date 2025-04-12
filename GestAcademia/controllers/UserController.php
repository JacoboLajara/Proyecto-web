<?php
/**
 * UserController Class
 *
 * Este controlador gestiona las operaciones relacionadas con los usuarios, incluyendo la visualización de la lista de usuarios,
 * la búsqueda de usuarios por DNI/NIE, la actualización de contraseñas y la eliminación de contraseñas.
 *
 * @package YourPackageName
 */
require_once __DIR__ . '/../models/UserModel.php';

class UserController
{
    /**
     * Instancia del modelo de usuario.
     *
     * @var UserModel
     */
    private $userModel;

    /**
     * Constructor del controlador.
     *
     * Incluye init.php para iniciar la sesión y verificar que el usuario esté autenticado.
     * Si el usuario no está autenticado, redirige a la página de login.
     * Luego, crea una instancia de UserModel.
     *
     * @return void
     */
    public function __construct()
    {
        // Incluir init.php para iniciar la sesión y verificar si el usuario está autenticado
        require_once __DIR__ . '/../init.php';  // Ruta ajustada según tu estructura

        // Depuración: Verificar valores de $_SESSION (comentado para producción)
        /* echo "<pre>";
           var_dump($_SESSION);
           echo "</pre>"; */

        // Verificar si el usuario no está autenticado
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
            header('Location: /login.php');
            exit();
        }
        $this->userModel = new UserModel();
    }

    /**
     * Muestra la vista principal de gestión de usuarios.
     *
     * Obtiene la lista de usuarios utilizando el modelo y carga la vista "usuarios.php".
     *
     * @return void
     */
    public function index()
    {
        $users = $this->userModel->getAllUsers();
        require_once __DIR__ . '/../views/users/usuarios.php';
    }

    /**
     * Busca un usuario por DNI/NIE y devuelve los datos en formato JSON.
     *
     * Recibe una solicitud POST con el campo "dni", utiliza el modelo para buscar el usuario y
     * retorna los datos del usuario en formato JSON. Si el usuario no es encontrado, retorna un mensaje de error.
     *
     * @return void
     */
    public function searchUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dni = $_POST['dni'];
            $user = $this->userModel->getUserByDNI($dni);

            // Verificar si se encontró el usuario
            if ($user) {
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode($user);
            } else {
                // Devolver un error en formato JSON
                echo json_encode(["error" => "Usuario no encontrado"]);
            }
            exit;
        }
    }

    /**
     * Actualiza la contraseña de un usuario.
     *
     * Recibe una solicitud POST con los campos "dni" y "new_password", y utiliza el modelo para actualizar la contraseña.
     * Devuelve una respuesta en formato JSON indicando si la actualización fue exitosa.
     *
     * @return void
     */
    public function updatePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dni = $_POST['dni'];
            $newPassword = $_POST['new_password'];

            if ($this->userModel->updatePassword($dni, $newPassword)) {
                echo json_encode(["success" => true, "message" => "Contraseña actualizada correctamente."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al actualizar la contraseña."]);
            }
            exit;
        }
    }

    /**
     * Elimina la contraseña de un usuario.
     *
     * Recibe una solicitud POST con el campo "dni" y utiliza el modelo para establecer la contraseña en NULL.
     * Devuelve una respuesta en formato JSON indicando si la operación fue exitosa.
     *
     * @return void
     */
    public function deletePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dni = $_POST['dni'];
            if ($this->userModel->deletePassword($dni)) {
                echo json_encode(["success" => true, "message" => "Contraseña eliminada correctamente."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al eliminar la contraseña."]);
            }
            exit;
        }
    }
}
