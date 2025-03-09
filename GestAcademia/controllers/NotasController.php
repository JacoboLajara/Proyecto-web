<?php
require_once __DIR__ . '/../models/NotasModel.php';

/**
 * Controlador para la gestión de notas.
 *
 * Este controlador se encarga de manejar las solicitudes relacionadas con las notas de los alumnos,
 * incluyendo la inserción, actualización y consulta de notas, así como la obtención de alumnos
 * y módulos/unidades formativas asociados a cursos.
 *
 * @package YourPackageName
 */
class NotasController
{
    /**
     * Instancia del modelo de notas.
     *
     * @var NotasModel
     */
    private $model;

    /**
     * Constructor del controlador.
     *
     * Inicia la sesión e incluye el archivo init.php para verificar que el usuario esté autenticado.
     * Si el usuario no está autenticado, se redirige a la página de login.
     * Finalmente, se instancia el modelo de notas.
     */
    public function __construct()
    {
        // Incluir init.php para iniciar la sesión y verificar la autenticación
        require_once __DIR__ . '/../init.php';  // Ruta ajustada según la estructura

        // Depuración: Verificar valores de $_SESSION (comentado para producción)
        /* echo "<pre>";
           var_dump($_SESSION);
           echo "</pre>"; */

        // Verificar si el usuario no está autenticado
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
            header('Location: /login.php');
            exit();
        }
        $this->model = new NotasModel();
    }

    /**
     * Muestra la vista para la gestión de notas.
     *
     * Incluye la vista correspondiente para que el usuario pueda interactuar con las notas.
     *
     * @return void
     */
    public function create()
    {
        require_once __DIR__ . '/../views/users/notas.php';
    }

    /**
     * Inserta notas en la base de datos.
     *
     * Procesa una solicitud POST que envía datos JSON con un array de notas a insertar.
     * Valida los campos obligatorios para cada nota (como id_alumno, id_curso, tipo_nota y calificacion).
     * Para las notas de tipo 'Modulo' o 'Unidad_Formativa' se verifica que se proporcione el ID correspondiente.
     * Se registra cualquier error ocurrido durante la inserción y se devuelve una respuesta JSON.
     *
     * @return void
     */
    public function storeNota()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        // Depuración: Registrar los datos recibidos en el log
        file_put_contents('debug.log', "📌 Datos recibidos en storeNota: " . json_encode($data, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

        if (!isset($data['notas']) || !is_array($data['notas'])) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            exit;
        }

        // Limpiar buffer de salida si es necesario
        if (ob_get_length()) {
            ob_end_clean();
        }

        $errores = [];
        // Procesar cada nota recibida
        foreach ($data['notas'] as $nota) {
            if (!isset($nota['id_alumno'], $nota['id_curso'], $nota['tipo_nota'], $nota['calificacion'])) {
                $errores[] = "Faltan datos obligatorios en una nota.";
                continue;
            }
            // Verificar que se proporcione el ID correspondiente según el tipo de nota
            if ($nota['tipo_nota'] === 'Modulo' && empty($nota['id_modulo'])) {
                $errores[] = "Falta el ID del módulo en una nota.";
                continue;
            }
            if ($nota['tipo_nota'] === 'Unidad_Formativa' && empty($nota['id_unidad_formativa'])) {
                $errores[] = "Falta el ID de la unidad formativa en una nota.";
                continue;
            }

            // Insertar la nota llamando al método del modelo
            $notaInsertada = $this->model->insertNota(
                $nota['id_alumno'],
                $nota['id_curso'],
                $nota['id_modulo'] ?? null,
                $nota['id_unidad_formativa'] ?? null,
                $nota['tipo_nota'],
                $nota['calificacion']
            );

            if (!$notaInsertada) {
                $errores[] = "Error al insertar una nota en la base de datos.";
            }
        }

        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudieron guardar algunas notas.',
                'errors' => $errores
            ]);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Notas registradas correctamente']);
        exit;
    }

    /**
     * Obtiene los alumnos matriculados en un curso.
     *
     * Lee el parámetro GET 'idCurso' para identificar el curso y consulta el modelo para
     * obtener los alumnos matriculados en ese curso. Devuelve la respuesta en formato JSON.
     *
     * @return void
     */
    public function getAlumnosPorCurso()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_GET['idCurso'])) {
            echo json_encode(['success' => false, 'message' => 'Curso no especificado']);
            exit;
        }

        $idCurso = trim($_GET['idCurso']);
        $alumnos = $this->model->getAlumnosPorCurso($idCurso);

        if ($alumnos === false) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener los alumnos']);
            exit;
        }

        ob_clean();
        echo json_encode($alumnos);
        exit;
    }

    /**
     * Obtiene las notas de un alumno en un curso.
     *
     * Se espera que se pasen los parámetros GET 'idAlumno' e 'idCurso'. El método consulta
     * el modelo para obtener las notas y devuelve la respuesta en formato JSON. Se registra
     * la salida en el log para depuración.
     *
     * @return void
     */
    public function getNotasPorAlumno()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_GET['idAlumno']) || !isset($_GET['idCurso'])) {
            echo json_encode(['success' => false, 'message' => 'Alumno o curso no especificado']);
            exit;
        }

        $idAlumno = trim($_GET['idAlumno']);
        $idCurso = trim($_GET['idCurso']);

        if (ob_get_length()) {
            ob_clean();
        }

        $notas = $this->model->getNotasPorAlumno($idAlumno, $idCurso);

        if ($notas === false) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener notas']);
            exit;
        }

        file_put_contents('debug.log', "Salida JSON: " . json_encode($notas) . "\n", FILE_APPEND);
        echo json_encode($notas);
        exit;
    }

    /**
     * Obtiene los módulos y unidades formativas asociados a un curso.
     *
     * Lee el parámetro GET 'idCurso' y consulta el modelo para obtener los módulos y unidades formativas
     * relacionados con ese curso. Devuelve la respuesta en formato JSON.
     *
     * @return void
     */
    public function getModulosUnidadesPorCurso()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_GET['idCurso'])) {
            echo json_encode(['success' => false, 'message' => 'Curso no especificado']);
            exit;
        }

        $idCurso = trim($_GET['idCurso']);

        ob_clean();
        $datos = $this->model->getModulosUnidadesPorCurso($idCurso);

        echo json_encode($datos);
        exit;
    }

    /**
     * Actualiza las notas de un alumno en un curso.
     *
     * Procesa una solicitud POST en la que se envían datos JSON con un array de notas para actualizar.
     * Valida la existencia de los campos obligatorios y llama al modelo para actualizar cada nota.
     * Devuelve una respuesta en formato JSON indicando el éxito o error de la operación.
     *
     * @return void
     */
    public function updateNota()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        file_put_contents('debug.log', "📌 Datos recibidos en updateNota: " . json_encode($data, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

        if (!isset($data['notas']) || !is_array($data['notas'])) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            exit;
        }

        $errores = [];
        foreach ($data['notas'] as $nota) {
            if (!isset($nota['id_alumno'], $nota['id_curso'], $nota['tipo_nota'], $nota['calificacion'])) {
                $errores[] = "Faltan datos obligatorios en una nota.";
                continue;
            }
            // Para notas de unidad formativa se espera que se incluya el id correspondiente
            if ($nota['tipo_nota'] === 'Unidad_Formativa' && empty($nota['id_unidad_formativa'])) {
                $errores[] = "Falta el ID de la unidad formativa en una nota.";
                continue;
            }
            // Llamar al método del modelo para actualizar la nota
            $actualizado = $this->model->updateNota(
                $nota['id_alumno'],
                $nota['id_curso'],
                $nota['tipo_nota'],
                $nota['calificacion'],
                $nota['id_unidad_formativa'] ?? null,
                $nota['id_modulo'] ?? null
            );

            if (!$actualizado) {
                $errores[] = "Error al actualizar la nota para la unidad/módulo con ID " . ($nota['id_unidad_formativa'] ?? $nota['id_modulo']);
            }
        }

        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudieron actualizar algunas notas.',
                'errors' => $errores
            ]);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Notas actualizadas correctamente']);
        exit;
    }
}
