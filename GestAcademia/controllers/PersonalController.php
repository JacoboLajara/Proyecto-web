<?php
/**
 * Controlador para la gestión del personal.
 *
 * Este controlador se encarga de manejar las solicitudes relacionadas con el personal (empleados),
 * como la creación y actualización de registros. Verifica que el usuario esté autenticado antes de
 * permitir el acceso a sus operaciones.
 *
 * @package YourPackageName
 */
class PersonalController
{
    /**
     * Instancia del modelo de personal.
     *
     * @var PersonalModel
     */
    private $model;

    /**
     * Constructor del controlador.
     *
     * Incluye el archivo init.php para iniciar la sesión y verificar la autenticación del usuario.
     * Si el usuario no está autenticado, se redirige a la página de login. Luego, se incluye el modelo
     * de personal y se instancia.
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

        // Incluir el modelo de personal y crear su instancia
        require_once __DIR__ . '/../models/PersonalModel.php';
        $this->model = new PersonalModel();
    }

    /**
     * Muestra la vista para la gestión del personal.
     *
     * Carga la vista correspondiente para que el usuario pueda gestionar los datos del personal.
     *
     * @return void
     */
    public function create()
    {
        require_once __DIR__ . '/../views/users/personal.php';
    }

    /**
     * Procesa la solicitud para insertar o actualizar un registro de personal.
     *
     * Dependiendo del valor del campo 'accion' enviado mediante POST, llama a los métodos
     * correspondientes para insertar o actualizar un registro. Si el método HTTP no es POST,
     * muestra un mensaje de error.
     *
     * @return void
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger el valor del campo 'accion' para diferenciar entre inserción o actualización
            $accion = $_POST['accion'] ?? 'insert';

            // Llamar a los métodos correspondientes según el valor de 'accion'
            if ($accion === 'insert') {
                $this->insertPersonal();
            } elseif ($accion === 'update') {
                $this->updatePersonal();
            } else {
                echo "Acción no válida.";
            }
        } else {
            echo "Método no permitido.";
        }
    }

    /**
     * Inserta un nuevo registro de personal.
     *
     * Recoge y valida los datos enviados desde el formulario. Si faltan campos obligatorios,
     * muestra un mensaje de error. Verifica que el personal no exista previamente y, de ser así,
     * llama al modelo para insertar el registro. Finalmente, redirige con un mensaje de éxito o error.
     *
     * @return void
     */
    private function insertPersonal()
    {
        // Validar y recoger los datos del formulario
        $id_personal = $_POST['id_personal'] ?? null;
        $id_usuario  = 2; // Valor predeterminado si no se envía
        $nombre      = $_POST['nombre'] ?? null;
        $apellido1   = $_POST['apellido1'] ?? null;
        $apellido2   = $_POST['apellido2'] ?? null;
        $direccion   = $_POST['direccion'] ?? null;
        $poblacion   = $_POST['poblacion'] ?? null;
        $cpostal     = $_POST['cpostal'] ?? null;
        $fechanac    = $_POST['fechanac'] ?? null;
        $estudios    = $_POST['estudios'] ?? null;
        $fechalta    = $_POST['fechalta'] ?? null;
        $fechabaja   = empty($_POST['fechabaja']) ? '2000-01-01' : $_POST['fechabaja'];
        $Phone       = $_POST['Phone'] ?? null;
        $mail        = $_POST['mail'] ?? null;
        $provincia   = $_POST['provincia'] ?? null;

        // Validar campos obligatorios
        $errores = [];
        if (!$id_personal) {
            $errores[] = "El campo 'DNI/NIE' es obligatorio.";
        }
        if (!$nombre) {
            $errores[] = "El campo 'Nombre' es obligatorio.";
        }
        if (!$apellido1) {
            $errores[] = "El campo 'Primer Apellido' es obligatorio.";
        }
        if (!$Phone) {
            $errores[] = "El campo 'Teléfono' es obligatorio.";
        }
        if (!$mail) {
            $errores[] = "El campo 'Email' es obligatorio.";
        }

        // Mostrar errores si los hay
        if (!empty($errores)) {
            echo "Error: " . implode('<br>', $errores);
            return;
        }

        // Verificar si el personal ya existe antes de insertar
        if ($this->model->existePersonal($id_personal)) {
            // Redirigir con el mensaje de error
            header("Location: /views/users/personal.php?error=" . urlencode("El empleado con ID {$id_personal} ya existe."));
            exit;
        }

        // Llamar al modelo para insertar el registro de personal
        $success = $this->model->insertPersonal(
            $id_personal,
            $id_usuario,
            $nombre,
            $apellido1,
            $apellido2,
            $direccion,
            $poblacion,
            $cpostal,
            $fechanac,
            $estudios,
            $fechalta,
            $fechabaja,
            $Phone,
            $mail,
            $provincia
        );

        if ($success) {
            // Redirigir con mensaje de éxito
            header("Location: /views/users/personal.php?success=" . urlencode("Empleado creado correctamente."));
            exit;
        } else {
            // Redirigir con mensaje de error genérico
            header("Location: /views/users/personal.php?error=" . urlencode("Error al crear el empleado."));
            exit;
        }
    }

    /**
     * Actualiza un registro de personal existente.
     *
     * Recoge y valida los datos enviados desde el formulario. Si faltan campos obligatorios,
     * muestra un mensaje de error. Llama al modelo para actualizar el registro y redirige
     * con un mensaje de éxito o error.
     *
     * @return void
     */
    private function updatePersonal()
    {
        // Validar y recoger los datos del formulario
        $id_personal = $_POST['id_personal'] ?? null;
        $id_usuario  = 2; // Valor predeterminado si no se envía
        $nombre      = $_POST['nombre'] ?? null;
        $apellido1   = $_POST['apellido1'] ?? null;
        $apellido2   = $_POST['apellido2'] ?? null;
        $direccion   = $_POST['direccion'] ?? null;
        $poblacion   = $_POST['poblacion'] ?? null;
        $cpostal     = $_POST['cpostal'] ?? null;
        $fechanac    = $_POST['fechanac'] ?? null;
        $estudios    = $_POST['estudios'] ?? null;
        $fechalta    = $_POST['fechalta'] ?? null;
        $fechabaja   = empty($_POST['fechabaja']) ? '2000-01-01' : $_POST['fechabaja'];
        $Phone       = $_POST['Phone'] ?? null;
        $mail        = $_POST['mail'] ?? null;
        $provincia   = $_POST['provincia'] ?? null;

        // Validar campos obligatorios
        $errores = [];
        if (!$id_personal) {
            $errores[] = "El campo 'DNI/NIE' es obligatorio.";
        }
        if (!$nombre) {
            $errores[] = "El campo 'Nombre' es obligatorio.";
        }
        if (!$apellido1) {
            $errores[] = "El campo 'Primer Apellido' es obligatorio.";
        }
        if (!$Phone) {
            $errores[] = "El campo 'Teléfono' es obligatorio.";
        }
        if (!$mail) {
            $errores[] = "El campo 'Email' es obligatorio.";
        }

        // Mostrar errores si existen
        if (!empty($errores)) {
            echo "Error: " . implode('<br>', $errores);
            return;
        }

        // Llamar al modelo para actualizar el registro de personal
        $success = $this->model->updatePersonal(
            $id_personal,
            $nombre,
            $apellido1,
            $apellido2,
            $direccion,
            $poblacion,
            $cpostal,
            $fechanac,
            $estudios,
            $fechalta,
            $fechabaja,
            $Phone,
            $mail,
            $provincia,
            $id_personal
        );

        if ($success) {
            // Redirigir con mensaje de éxito
            header("Location: /views/users/personal.php?success=" . urlencode("Empleado modificado correctamente."));
            exit;
        } else {
            // Redirigir con mensaje de error genérico
            header("Location: /views/users/personal.php?error=" . urlencode("Error al modificar el registro emp."));
            exit;
        }
    }
}
