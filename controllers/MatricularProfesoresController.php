<?php
require_once __DIR__ . '/../models/MatriculaProfesorModel.php';

/**
 * Controlador para la gestión de matrículas.
 *
 * Este controlador se encarga de manejar las solicitudes relacionadas con la matriculación de alumnos
 * en cursos, la consulta de alumnos matriculados y el procesamiento de bajas de alumnos. Además,
 * verifica que el usuario esté autenticado antes de permitir el acceso a sus operaciones.
 *
 * @package YourPackageName
 */
class MatricularProfesoresController
{
    /**
     * Instancia del modelo de matrículas.
     *
     * @var MatriculaProfesorModel
     */
    private $model;

    /**
     * Constructor del controlador.
     *
     * Incluye el archivo init.php para iniciar la sesión y verificar si el usuario está autenticado.
     * Si el usuario no está autenticado, se redirige a la página de login.
     * Luego, se instancia el modelo de matrículas.
     */
    public function __construct()
    {
        // Incluir init.php para iniciar la sesión y verificar la autenticación del usuario
        require_once __DIR__ . '/../init.php';  // Ruta ajustada según la estructura

        // Depuración: Verificar valores de $_SESSION (comentado para producción)
        /*  
        echo "<pre>";
        var_dump($_SESSION);
        echo "</pre>";
        */

        // Verificar si el usuario no está autenticado
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
            header('Location: /login.php');
            exit();
        }
        $this->model = new MatriculaProfesorModel();
    }

    /**
     * Muestra la vista para la gestión de matrículas.
     *
     * Carga la vista de matrículas para que el usuario pueda interactuar con el sistema.
     *
     * @return void
     */
    public function create()
    {
        require_once __DIR__ . '/../views/users/matriculaprofesores.php';
    }

    /**
     * Muestra la vista para dar de baja a alumnos.
     *
     * Carga la vista correspondiente para el proceso de baja de alumnos.
     *
     * @return void
     */
    public function mostrarVistaBajaAlumnos()
    {
        require_once __DIR__ . '/../views/users/bajaAlumnos.php';
    }

    /**
     * Procesa la asignación de profesor/es en un curso.
     *
     * Lee los datos enviados en formato JSON desde la solicitud HTTP, valida que se reciban
     * el ID del curso y la lista de alumnos, y para cada alumno verifica si ya está matriculado.
     * Si no lo está, inserta la matrícula. Se responde en formato JSON indicando éxito o error.
     *
     * @return void
     */
    public function store()
    {
        ob_start();
        ob_end_clean();
        header('Content-Type: application/json');

        file_put_contents('debug.log', "Datos recibidos en storeAsignacion: " . print_r($_POST, true) . "\n", FILE_APPEND);
        file_put_contents('debug.log', "JSON recibido: " . file_get_contents("php://input") . "\n", FILE_APPEND);


        try {
            $data = json_decode(file_get_contents('php://input'), true);
            file_put_contents('debug.log', "Contenido de data: " . print_r($data, true) . "\n", FILE_APPEND);

            // Verificar que se hayan recibido los datos obligatorios
            if (!isset($data['id_curso']) || !isset($data['profesores'])) {
                throw new Exception('Datos incompletos');
            }

            $idCurso = trim($data['id_curso']);
            $profesores = $data['profesores'];

            // Procesar cada alumno de la lista
            foreach ($profesores as $idProfesor) {
                file_put_contents('debug.log', "Insertando asignación para el profesor: $idProfesor en curso: $idCurso\n", FILE_APPEND);
                if (empty($idProfesor)) {
                    throw new Exception("ID de profesor no válido");
                }

                // Verificar si la matrícula ya existe para evitar duplicados
                if ($this->model->existeAsignacion($idProfesor, $idCurso)) {
                    throw new Exception("El profesor $idProfesor ya tiene asignación en el  curso $idCurso.");
                }

                // Insertar la matrícula para el alumno
                $this->model->insertAsignacion($idProfesor, $idCurso);
            }

            ob_end_clean();
            echo json_encode(['success' => true, 'message' => 'Curso asignado a profesor correctamente']);
            exit;

        } catch (mysqli_sql_exception $e) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            exit;
        } catch (Exception $e) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * Obtiene las asignaciones de profesores en un curso.
     *
     * Recibe el ID del curso vía GET, consulta en el modelo los profesores asiganadoss y devuelve
     * la información en formato JSON. En caso de error, responde con un mensaje descriptivo.
     *
     * @return void
     */
    public function getProfesoresMatriculados()
    {
        header('Content-Type: application/json');

        try {
            $idCurso = $_GET['id_curso'] ?? null;
            if (!$idCurso) {
                throw new Exception('ID del curso no proporcionado.');
            }

            $alumnos = $this->model->getProfesoresMatriculadosPorCurso($idCurso);

            echo json_encode(['success' => true, 'data' => $alumnos]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        die();  // Asegura que no haya salida adicional
    }

    /**
     * Procesa retirada de asignación de profesores en un curso.
     *
     * Lee los datos en formato JSON que deben incluir el ID del curso y una lista de profesores.
     * Llama al modelo para dar de baja a los profesores indicados y devuelve una respuesta en JSON
     * indicando el éxito o el fallo de la operación.
     *
     * @return void
     */
    public function procesarBajaAlumnos()
    {
        file_put_contents('debug.log', "Entrando en bajaAlumnos()\n", FILE_APPEND);

        ob_start();
        ob_end_clean();
        header('Content-Type: application/json');

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Verificar que se hayan recibido los datos necesarios
            if (!isset($data['id_curso']) || !isset($data['profesores'])) {
                throw new Exception('Datos incompletos.');
            }

            $idCurso = $data['id_curso'];
            $profesores = $data['alumnos'];

            // Procesar la desasignación  en el curso
            $this->model->darDeBajaAsignacion($idCurso, $profesores);

            echo json_encode(['success' => true, 'message' => 'Alumnos dados de baja correctamente.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
