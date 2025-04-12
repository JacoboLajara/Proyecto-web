<?php
require_once __DIR__ . '/../models/MatriculasModel.php';

/**
 * Controlador para la gestión de matrículas.
 *
 * Este controlador se encarga de manejar las solicitudes relacionadas con la matriculación de alumnos
 * en cursos, la consulta de alumnos matriculados y el procesamiento de bajas de alumnos. Además,
 * verifica que el usuario esté autenticado antes de permitir el acceso a sus operaciones.
 *
 * @package YourPackageName
 */
class MatriculasController
{
    /**
     * Instancia del modelo de matrículas.
     *
     * @var MatriculasModel
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
        $this->model = new MatriculasModel();
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
        require_once __DIR__ . '/../views/users/matriculas.php';
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
     * Muestra la vista para listado de  alumnos.
     *
     * Carga la vista correspondiente para el proceso de baja de alumnos.
     *
     * @return void
     */
    public function mostrarVistaListaAlumnos()
    {
        require_once __DIR__ . '/../views/users/alumnosCurso.php';
    }

    /**
     * Procesa la matriculación de alumnos en un curso.
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

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            file_put_contents('debug.log', "Contenido de data: " . print_r($data, true) . "\n", FILE_APPEND);

            // Verificar que se hayan recibido los datos obligatorios
            if (!isset($data['id_curso']) || !isset($data['alumnos'])) {
                throw new Exception('Datos incompletos');
            }

            $idCurso = trim($data['id_curso']);
            $alumnos = $data['alumnos'];

            // Procesar cada alumno de la lista
            foreach ($alumnos as $idAlumno) {
                file_put_contents('debug.log', "Insertando matrícula para el alumno: $idAlumno en curso: $idCurso\n", FILE_APPEND);
                if (empty($idAlumno)) {
                    throw new Exception("ID de alumno no válido");
                }

                // Verificar si la matrícula ya existe para evitar duplicados
                if ($this->model->existeMatricula($idAlumno, $idCurso)) {
                    throw new Exception("El alumno $idAlumno ya está matriculado en el curso $idCurso.");
                }

                // Insertar la matrícula para el alumno
                $this->model->insertMatricula($idAlumno, $idCurso);
            }

            ob_end_clean();
            echo json_encode(['success' => true, 'message' => 'Alumnos matriculados correctamente']);
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
     * Obtiene los alumnos matriculados en un curso.
     *
     * Recibe el ID del curso vía GET, consulta en el modelo los alumnos matriculados y devuelve
     * la información en formato JSON. En caso de error, responde con un mensaje descriptivo.
     *
     * @return void
     */
    public function getAlumnosMatriculados()
    {
        header('Content-Type: application/json');
    
        try {
            $idCurso = $_GET['id_curso'] ?? null;
            if (!$idCurso) {
                throw new Exception('ID del curso no proporcionado.');
            }
    
            $alumnos = $this->model->getAlumnosMatriculadosPorCurso($idCurso);
    
            echo json_encode(['success' => true, 'data' => $alumnos]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        die();  // Asegura que no haya salida adicional
    }

    /**
     * Procesa la baja de alumnos matriculados en un curso.
     *
     * Lee los datos en formato JSON que deben incluir el ID del curso y una lista de alumnos.
     * Llama al modelo para dar de baja a los alumnos indicados y devuelve una respuesta en JSON
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
            if (!isset($data['id_curso']) || !isset($data['alumnos'])) {
                throw new Exception('Datos incompletos.');
            }

            $idCurso = $data['id_curso'];
            $alumnos = $data['alumnos'];

            // Procesar la baja de los alumnos en el curso
            $this->model->darDeBajaAlumnos($idCurso, $alumnos);

            echo json_encode(['success' => true, 'message' => 'Alumnos dados de baja correctamente.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
