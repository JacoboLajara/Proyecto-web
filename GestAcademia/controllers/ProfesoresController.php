<?php
/**
 * Controlador para la gestión de profesores.
 *
 * Este controlador se encarga de manejar las solicitudes relacionadas con la creación y actualización
 * de profesores. Verifica que el usuario esté autenticado antes de permitir el acceso a sus operaciones,
 * y se comunica con el modelo de profesores para realizar las operaciones en la base de datos.
 *
 * @package YourPackageName
 */
class ProfesoresController
{
    /**
     * Instancia del modelo de profesores.
     *
     * @var ProfesoresModel
     */
    private $model;

    /**
     * Constructor del controlador.
     *
     * Incluye el archivo init.php para iniciar la sesión y verificar la autenticación del usuario.
     * Si el usuario no está autenticado, se redirige a la página de login. Luego, incluye el modelo de
     * profesores y lo instancia.
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
        
        // Incluir el modelo de profesores y crear su instancia
        require_once __DIR__ . '/../models/ProfesoresModel.php';
        $this->model = new ProfesoresModel();
    }

    /**
     * Muestra la vista para la gestión de profesores.
     *
     * Carga la vista correspondiente donde se pueden ingresar o editar los datos de un profesor.
     *
     * @return void
     */
    public function create()
    {
        require_once __DIR__ . '/../views/users/profesor.php';
    }

    /**
     * Procesa la solicitud para almacenar un profesor.
     *
     * Dependiendo del valor del campo 'accion' enviado mediante POST, llama al método
     * correspondiente para insertar o actualizar un profesor. Si el método HTTP no es POST,
     * se muestra un mensaje de error.
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
                $this->insertProfesor();
            } elseif ($accion === 'update') {
                $this->updateProfesor();
            } else {
                echo "Acción no válida.";
            }
        } else {
            echo "Método no permitido.";
        }
    }

    /**
     * Inserta un nuevo profesor en la base de datos.
     *
     * Recoge y valida los datos enviados desde el formulario. Si faltan datos obligatorios,
     * muestra un mensaje de error. También verifica que el profesor no exista previamente para
     * evitar duplicados. Si la inserción es exitosa, redirige a la vista con un mensaje de éxito;
     * de lo contrario, redirige con un mensaje de error.
     *
     * @return void
     */
    private function insertProfesor()
    {
        // Recoger los datos del formulario
        $id_profesor = $_POST['id_profesor'] ?? null;
        $id_usuario  = $_POST['id_usuario'] ?? '2'; // Valor predeterminado si no se envía
        $nombre      = $_POST['nombre'] ?? null;
        $apellido1   = $_POST['apellido1'] ?? null;
        $apellido2   = $_POST['apellido2'] ?? null;
        $direccion   = $_POST['direccion'] ?? null;
        $poblacion   = $_POST['poblacion'] ?? null;
        $cpostal     = $_POST['cpostal'] ?? null;
        $fechanac    = $_POST['fechanac'] ?? null;
        $estudios    = $_POST['estudios'] ?? null;
        $fechalta    = empty($_POST['fechalta']) ? null : $_POST['fechalta'];
        $fechabaja   = empty($_POST['fechabaja']) ? '2000-01-01' : $_POST['fechabaja'];
        $Phone       = $_POST['Phone'] ?? null;
        $mail        = $_POST['mail'] ?? null;
        $provincia   = $_POST['provincia'] ?? null;
        $especialidad= $_POST['especialidad'] ?? null;

        // Validar campos obligatorios
        $errores = [];
        if (!$id_profesor) {
            $errores[] = "El campo 'ID Alumno' es obligatorio.";
        }
        if (!$id_usuario) {
            $errores[] = "El campo 'ID Usuario' es obligatorio.";
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
        if (!$especialidad) {
            $errores[] = "El campo 'especialidad' viene vacío.";
        }

        // Si existen errores, mostrarlos y salir
        if (!empty($errores)) {
            echo "Error: " . implode('<br>', $errores);
            return;
        }

        // Verificar si el profesor ya existe para evitar duplicados
        if ($this->model->existeProfesor($id_profesor)) {
            header("Location: /views/users/profesor.php?error=" . urlencode("El profesor con DNI {$id_profesor} ya existe."));
            exit;
        }

        // Insertar el profesor mediante el modelo
        $success = $this->model->insertProfesor(
            $id_profesor,
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
            $fechabaja  = empty($_POST['fechabaja']) ? '2000-01-01' : $_POST['fechabaja'],
            $Phone,
            $mail,
            $provincia,
            $especialidad
        );

        // Redirigir según el resultado de la inserción
        if ($success) {
            header("Location: /views/users/profesor.php?success=" . urlencode("Profesor creado correctamente."));
            exit;
        } else {
            header("Location: /views/users/profesor.php?error=" . urlencode("Error al crear el profesor."));
            exit;
        }
    }

    /**
     * Actualiza los datos de un profesor existente.
     *
     * Recoge los datos enviados desde el formulario, valida los campos obligatorios y llama al modelo
     * para actualizar el registro. Redirige a la vista con un mensaje de éxito o error, según corresponda.
     *
     * @return void
     */
    private function updateProfesor()
    {
        // Recoger los datos del formulario
        $id_profesor = $_POST['id_profesor'] ?? null;
        $id_usuario  = $_POST['id_usuario'] ?? '3'; // Valor predeterminado si no se envía
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
        $especialidad= $_POST['especialidad'] ?? null;

        // Validar campos obligatorios
        $errores = [];
        if (!$id_profesor) {
            $errores[] = "El campo 'ID profesor' es obligatorio.";
        }
        if (!$nombre) {
            $errores[] = "El campo 'Nombre' es obligatorio.";
        }
        if (!$apellido1) {
            $errores[] = "El campo 'Primer Apellido' es obligatorio.";
        }
        if (!empty($errores)) {
            echo "Error: " . implode('<br>', $errores);
            return;
        }

        // Actualizar el registro del profesor a través del modelo
        $success = $this->model->updateProfesor(
            $id_profesor,
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
            $especialidad
        );

        // Redirigir según el resultado de la actualización
        if ($success) {
            header("Location: /views/users/profesor.php?success=" . urlencode("Profesor modificado correctamente."));
            exit;
        } else {
            header("Location: /views/users/profesor.php?error=" . urlencode("Error al crear el profesor."));
            exit;
        }
    }
}
