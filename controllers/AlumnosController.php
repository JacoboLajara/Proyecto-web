<?php
/**
 * Controlador para la gestión de alumnos.
 *
 * Este controlador se encarga de manejar las solicitudes relacionadas con los alumnos,
 * como la creación, actualización y generación de listados. Verifica que el usuario esté
 * autenticado antes de permitir el acceso a las operaciones.
 *
 * @package YourPackageName
 */
class AlumnosController
{
    /**
     * Instancia del modelo de alumnos.
     *
     * @var AlumnosModel
     */
    private $model;

    /**
     * Constructor del controlador.
     *
     * Incluye el archivo init.php para iniciar la sesión y verificar la autenticación del usuario.
     * Si el usuario no está autenticado, se redirige al login. Finalmente, se instancia el modelo de alumnos.
     */
    public function __construct()
    {
        // Incluir init.php para iniciar la sesión y verificar si el usuario está autenticado
        require_once __DIR__ . '/../init.php';  // Ruta ajustada según tu estructura

        // Depuración: Verificar valores de $_SESSION (comentado para producción)
        /* echo "<pre>";
         var_dump($_SESSION);
         echo "</pre>";*/

        // Verificar si el usuario no está autenticado
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
            header('Location: /login.php');
            exit();
        }

        // Incluir el modelo de alumnos y crear la instancia correspondiente
        require_once __DIR__ . '/../models/AlumnosModel.php';
        $this->model = new AlumnosModel();
    }

    /**
     * Muestra la vista para la creación o gestión de alumnos.
     *
     * Incluye la vista correspondiente y registra en el log que se está ejecutando el método create.
     */
    public function create()
    {
        require_once __DIR__ . '/../views/users/alumnos.php';
        file_put_contents('debug.log', "Ejecutando AlumnosController::create()\n", FILE_APPEND);
    }

    /**
     * Procesa el formulario para crear o actualizar un alumno.
     *
     * Dependiendo del valor del campo 'accion' (insert o update) en el formulario enviado,
     * llama al método correspondiente para insertar o actualizar un alumno.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger el valor del campo 'accion' para diferenciar entre inserción o actualización
            $accion = $_POST['accion'] ?? 'insert';

            // Llamar a los métodos correspondientes según el valor de 'accion'
            if ($accion === 'insert') {
                $this->insertAlumno();
            } elseif ($accion === 'update') {
                $this->updateAlumno();
            } else {
                echo "Acción no válida.";
            }
        } else {
            echo "Método no permitido.";
        }
    }

    /**
     * Inserta un nuevo alumno en el sistema.
     *
     * Recoge y valida los datos enviados por el formulario. Si hay errores de validación,
     * se muestran. En caso de que el alumno ya exista, se redirige con un mensaje de error.
     * Si la inserción es exitosa, se redirige con un mensaje de éxito.
     */
    private function insertAlumno()
    {
        // Validar y recoger los datos del formulario
        $id_alumno  = $_POST['id_alumno']  ?? null;
        $id_usuario = 4; // Valor predeterminado para el usuario
        $nombre     = $_POST['nombre']     ?? null;
        $apellido1  = $_POST['apellido1']  ?? null;
        $apellido2  = $_POST['apellido2']  ?? null;
        $direccion  = $_POST['direccion']  ?? null;
        $poblacion  = $_POST['poblacion']  ?? null;
        $cpostal    = $_POST['cpostal']    ?? null;
        $fechanac   = $_POST['fechanac']   ?? null;
        $estudios   = $_POST['estudios']   ?? null;
        $fechalta   = $_POST['fechalta']   ?? null;
        $fechabaja  = empty($_POST['fechabaja']) ? '2000-01-01' : $_POST['fechabaja'];
        $Phone      = $_POST['Phone']      ?? null;
        $mail       = $_POST['mail']       ?? null;
        $provincia  = $_POST['provincia']  ?? null;

        // Validar campos obligatorios
        $errores = [];
        if (!$id_alumno)
            $errores[] = "El campo 'ID Alumno' es obligatorio.";
        if (!$nombre)
            $errores[] = "El campo 'Nombre' es obligatorio.";
        if (!$apellido1)
            $errores[] = "El campo 'Primer Apellido' es obligatorio.";
        if (!$Phone)
            $errores[] = "El campo 'Teléfono' es obligatorio.";
        if (!$mail)
            $errores[] = "El campo 'Email' es obligatorio.";

        if (!empty($errores)) {
            echo "Error: " . implode('<br>', $errores);
            return;
        }

        // Verificar si el alumno ya existe
        if ($this->model->existeAlumno($id_alumno)) {
            header("Location: /views/users/alumnos.php?error=" . urlencode("El alumno con ID {$id_alumno} ya existe."));
            exit;
        }

        // Llamar al modelo para insertar el alumno
        $success = $this->model->insertAlumno(
            $id_alumno,
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
            // Redirigir a alumnos.php con un mensaje de éxito
            header("Location: /views/users/alumnos.php?success=" . urlencode("Alumno creado correctamente."));
            exit;
        } else {
            header("Location: /views/users/alumnos.php?error=" . urlencode("Error al crear el alumno."));
            exit;
        }
    }  // <-- Esta llave cierra insertAlumno

    /**
     * Actualiza los datos de un alumno existente.
     *
     * Recoge los datos enviados por el formulario, valida los campos obligatorios y
     * llama al modelo para actualizar la información del alumno. Redirige con mensajes
     * de éxito o error según el resultado.
     */
    private function updateAlumno()
    {
        // Recoger los datos del formulario
        $id_alumno  = $_POST['id_alumno']  ?? null;
        $nombre     = $_POST['nombre']     ?? null;
        $apellido1  = $_POST['apellido1']  ?? null;
        $apellido2  = $_POST['apellido2']  ?? null;
        $direccion  = $_POST['direccion']  ?? null;
        $poblacion  = $_POST['poblacion']  ?? null;
        $cpostal    = $_POST['cpostal']    ?? null;
        $fechanac   = $_POST['fechanac']   ?? null;
        $estudios   = $_POST['estudios']   ?? null;
        $fechalta   = $_POST['fechalta']   ?? null;
        $fechabaja  = empty($_POST['fechabaja']) ? '2000-01-01' : $_POST['fechabaja'];
        $Phone      = $_POST['Phone']      ?? null;
        $mail       = $_POST['mail']       ?? null;
        $provincia  = $_POST['provincia']  ?? null;

        // Validar campos obligatorios
        $errores = [];
        if (!$id_alumno)
            $errores[] = "El campo 'ID Alumno' es obligatorio.";
        if (!$nombre)
            $errores[] = "El campo 'Nombre' es obligatorio.";
        if (!$apellido1)
            $errores[] = "El campo 'Primer Apellido' es obligatorio.";

        if (!empty($errores)) {
            // Si hay errores, redirige con mensaje de error (puedes ajustar la ruta)
            header("Location: /views/users/alumnos.php?error=" . urlencode("Error: " . implode('<br>', $errores)));
            exit;
        }

        // Llamar al modelo para actualizar el alumno
        $success = $this->model->updateAlumno(
            $id_alumno,
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
            header("Location: /views/users/alumnos.php?success=" . urlencode("Alumno actualizado correctamente."));
            exit;
        } else {
            // Redirigir con mensaje de error
            header("Location: /views/users/alumnos.php?error=" . urlencode("Error al actualizar el alumno."));
            exit;
        }
    }

    /**
     * Genera un listado detallado del alumno, incluyendo los cursos en los que está matriculado.
     *
     * Utiliza la librería TCPDF para generar un PDF con la información detallada del alumno.
     * Si no se encuentra información para el alumno, se termina la ejecución con un mensaje de error.
     *
     * @param string $idAlumno Identificador del alumno.
     */
    public function generarListadoAlumno($idAlumno)
    {
        // Incluir el modelo y la librería TCPDF
        require_once __DIR__ . '/../models/AlumnosModel.php';
        require_once __DIR__ . '/../libs/tcpdf/tcpdf.php';

        $model = new AlumnosModel();
        $datos = $model->getAlumnoDetalleConCursos($idAlumno);

        if (!$datos) {
            die("Error: No se encontró información para el alumno.");
        }

        // Generar el PDF con los datos obtenidos
        require_once __DIR__ . '/../listados/listadoDetalleAlumno.php';
    }
}
