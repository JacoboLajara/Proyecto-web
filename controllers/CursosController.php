<?php
require_once __DIR__ . '/../models/CursosModel.php';

/**
 * Controlador para la gestión de cursos.
 *
 * Este controlador se encarga de manejar las solicitudes relacionadas con la creación, actualización
 * y consulta de cursos, módulos y unidades formativas. Además, se encarga de validar las solicitudes,
 * formatear las respuestas en JSON y registrar información para depuración.
 *
 * @package YourPackageName
 */
class CursosController
{
    /**
     * Instancia del modelo de cursos.
     *
     * @var CursosModel
     */
    private $model;

    /**
     * Constructor del controlador.
     *
     * Incluye el archivo init.php para iniciar la sesión y verificar la autenticación del usuario.
     * Si el usuario no está autenticado, se redirige al login. Finalmente, se instancia el modelo de cursos.
     */
    public function __construct()
    {
        // Incluir init.php para iniciar la sesión y verificar si el usuario está autenticado
        require_once __DIR__ . '/../init.php';  // Ruta ajustada según la estructura del proyecto

        // Depuración: Verificar valores de $_SESSION (comentado para producción)
        /* echo "<pre>";
         var_dump($_SESSION);
         echo "</pre>";*/

        // Verificar si el usuario no está autenticado
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
            header('Location: /login.php');
            exit();
        }
        $this->model = new CursosModel();
    }

    /**
     * Muestra el formulario para la gestión de cursos.
     *
     * Carga la vista correspondiente para la creación y gestión de cursos, y registra en el log
     * que se está cargando el formulario.
     *
     * @return void
     */
    public function create()
    {
        // Depuración para confirmar que se carga el formulario
        file_put_contents('debug.log', "Cargando el formulario de cursos\n", FILE_APPEND);
        require_once __DIR__ . '/../views/users/cursos.php';
    }

    /**
     * Procesa la solicitud para almacenar un nuevo curso.
     *
     * Lee y decodifica el cuerpo de la solicitud en formato JSON, valida los campos obligatorios,
     * y llama al modelo para insertar el curso en la base de datos. Responde con JSON indicando
     * el éxito o el fallo de la operación.
     *
     * @return void
     */
    public function storeCurso()
    {
        // Registro del método de solicitud para depuración
        file_put_contents('debug.log', "Método de solicitud storeCurso: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);

        // Asegurar que el encabezado JSON se envíe correctamente
        header('Content-Type: application/json; charset=utf-8');
        ob_clean(); // Limpia cualquier salida previa

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        // Obtención y decodificación de los datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);
        file_put_contents('debug.log', print_r($data, true), FILE_APPEND);

        if (!$data) {
            echo json_encode(['success' => false, 'message' => 'No se recibieron datos válidos']);
            exit;
        }

        // Extraer valores asegurando que existan
        $codigo     = isset($data['codigo']) ? trim($data['codigo']) : null;
        $nombre     = isset($data['nombre']) ? trim($data['nombre']) : null;
        $duracion   = isset($data['duracion']) ? (int) $data['duracion'] : null;
        $tipo_curso = isset($data['tipo_curso']) ? trim($data['tipo_curso']) : null;
        $tipo_cuota = isset($data['tipo_cuota']) ? trim($data['tipo_cuota']) : null;
        $precio     = isset($data['precio']) ? (float) $data['precio'] : null;

        // Validación de los campos obligatorios
        if (empty($codigo) || empty($nombre) || empty($tipo_curso) || empty($tipo_cuota) || $duracion <= 0 || (!is_numeric($precio) && $precio !== 0.0)) {
            echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios y deben ser válidos.']);
            exit;
        }

        // Llamada al modelo para insertar el curso en la base de datos
        $codigo_curso = $this->model->insertCurso($nombre, $codigo, $duracion, $tipo_curso, $tipo_cuota, $precio);

        // Verificación de resultado y respuesta en JSON
        if ($codigo_curso) {
            echo json_encode(['success' => true, 'idcurso' => $codigo_curso]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar el curso']);
        }
        exit;
    }

    /**
     * Procesa la solicitud para almacenar un nuevo módulo.
     *
     * Valida que la solicitud sea POST, obtiene y decodifica los datos JSON, y valida que
     * se hayan recibido todos los campos obligatorios. Además, verifica que el curso exista
     * en la base de datos antes de insertar el módulo y su relación con el curso.
     *
     * @return void
     */
    public function storeModulo()
    {
        ob_start();  // Captura cualquier salida accidental antes de limpiar
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Metodo en store no permitido']);
            exit;
        }

        // Obtener los datos JSON de la petición
        $data = json_decode(file_get_contents('php://input'), true);

        // Verificar que los datos se reciban correctamente
        if (!$data) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud']);
            exit;
        }

        // Extraer y validar los valores recibidos
        $idcurso  = isset($data['id_curso']) ? trim($data['id_curso']) : null;
        $codigo   = isset($data['codigo']) ? trim($data['codigo']) : null;
        $nombre   = isset($data['nombre']) ? trim($data['nombre']) : null;
        $duracion = isset($data['duracion']) ? (int) $data['duracion'] : null;

        if (!$codigo || !$nombre || !$idcurso || $duracion <= 0) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
            exit;
        }
        // Pausa para simular latencia o espera antes de verificar la existencia del curso
        sleep(2);

        // Comprobar si el curso existe en la base de datos
        $conn = $this->model->getConnection();
        if (!$conn) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Error de conexión']);
            exit;
        }

        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM Curso WHERE ID_Curso = ?");
        if (!$stmt) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Error en la consulta']);
            exit;
        }

        $stmt->bind_param("s", $idcurso);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $cursoExiste = $row['total'] ?? 0;
        $stmt->close();

        if (!$cursoExiste) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'El curso no existe en la base de datos.']);
            exit;
        }

        // Llamada al modelo para insertar el módulo
        $success = $this->model->insertModulo($codigo, $nombre, $idcurso, $duracion);
        if (ob_get_length()) {
            ob_end_clean();
        }

        if ($success) {
            // Insertar la relación en la tabla curso_modulo
            $relacionSuccess = $this->model->insertCursoWithModulo($codigo, $idcurso);
            if ($relacionSuccess) {
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Módulo y relación insertados correctamente']);
            } else {
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => 'Módulo guardado, pero error en la relación curso-módulo']);
            }
        } else {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Error al guardar el módulo']);
        }
        exit;
    }

    /**
     * Procesa la solicitud para almacenar unidades formativas.
     *
     * Lee los datos JSON enviados en la solicitud, valida cada unidad formativa y llama al modelo
     * para insertarla. Se recopilan los errores ocurridos durante la inserción y se devuelve una respuesta
     * en formato JSON.
     *
     * @return void
     */
    public function storeUnidadFormativa()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !is_array($data)) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos recibidos']);
            exit;
        }

        $errores = [];
        foreach ($data as $unidad) {
            $id_modulo    = $unidad['id_modulo'] ?? null;
            $denominacion = $unidad['denominacion'] ?? null;
            $codigo       = $unidad['codigo'] ?? null;
            $duracion     = isset($unidad['duracion']) ? (int) $unidad['duracion'] : null;

            if (!$id_modulo || !$denominacion || !$codigo || !$duracion) {
                $errores[] = "Error en la unidad con código {$codigo}: campos inválidos.";
                continue;
            }

            $success = $this->model->insertUnidadFormativa($id_modulo, $denominacion, $codigo, $duracion);
            if (!$success) {
                $errores[] = "Error al guardar la unidad con código {$codigo}.";
            }
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        if (count($errores) > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Algunos errores ocurrieron',
                'errors' => $errores
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Todas las unidades formativas guardadas correctamente'
            ]);
        }
        exit;
    }

    /**
     * Obtiene los módulos y unidades formativas asociados a un curso.
     *
     * Lee el parámetro GET 'id_curso', consulta los módulos del curso y para cada módulo obtiene
     * las unidades formativas correspondientes. Devuelve la información en formato JSON.
     *
     * @return void
     */
    public function getModulosYUnidadesPorCurso()
    {
        header('Content-Type: application/json; charset=utf-8');

        $idCurso = isset($_GET['id_curso']) ? trim($_GET['id_curso']) : null;
        if (!$idCurso) {
            echo json_encode(['success' => false, 'message' => 'ID del curso no proporcionado']);
            exit;
        }

        require_once __DIR__ . '/../models/CursosModel.php';
        $model = new CursosModel();
        $conn = $model->getConnection();

        // Obtener módulos del curso
        $modulosQuery = "SELECT m.ID_Modulo, m.Nombre, m.Duracion_Horas 
                     FROM Modulo m 
                     JOIN Curso_Modulo cm ON m.ID_Modulo = cm.ID_Modulo 
                     WHERE cm.ID_Curso = ?";
        $stmt = $conn->prepare($modulosQuery);
        $stmt->bind_param("s", $idCurso);
        $stmt->execute();
        $modulosResult = $stmt->get_result();

        $modulos = [];
        while ($modulo = $modulosResult->fetch_assoc()) {
            // Obtener unidades formativas del módulo
            $unidadesQuery = "SELECT ID_Unidad_Formativa, Nombre, Duracion_Unidad 
                          FROM Unidad_Formativa 
                          WHERE ID_Modulo = ?";
            $stmtUnidad = $conn->prepare($unidadesQuery);
            $stmtUnidad->bind_param("s", $modulo['ID_Modulo']);
            $stmtUnidad->execute();
            $unidadesResult = $stmtUnidad->get_result();

            $unidades = [];
            while ($unidad = $unidadesResult->fetch_assoc()) {
                $unidades[] = $unidad;
            }
            $stmtUnidad->close();

            // Agregar el módulo con sus unidades al array
            $modulo['unidades'] = $unidades;
            $modulos[] = $modulo;
        }
        $stmt->close();

        echo json_encode(['success' => true, 'modulos' => $modulos]);
        exit;
    }

    /**
     * Actualiza un curso, sus módulos y unidades formativas.
     *
     * Recibe datos en formato JSON que incluyen el ID del curso y la lista de módulos con sus unidades.
     * Se inicia una transacción para eliminar módulos y unidades que ya no existen, actualizar o insertar
     * nuevos registros según corresponda. Se confirma la transacción si todo es correcto o se revierte en caso de error.
     *
     * @return void
     */
    public function updateCurso()
    {
        header('Content-Type: application/json; charset=utf-8');

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id_curso']) || !isset($data['modulos'])) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            exit;
        }

        $idCurso = $data['id_curso'];
        $conn = $this->model->getConnection();
        $conn->begin_transaction();

        try {
            // Obtener módulos existentes antes de la actualización
            $modulosExistentes = $this->model->getModulosPorCurso($idCurso);
            $modulosNuevos = array_column($data['modulos'], 'id_modulo');

            // Eliminar módulos que no están en los datos enviados
            foreach ($modulosExistentes as $modulo) {
                if (!in_array($modulo['ID_Modulo'], $modulosNuevos)) {
                    $this->model->deleteModulo($modulo['ID_Modulo']);
                }
            }

            // Procesar cada módulo enviado
            foreach ($data['modulos'] as $modulo) {
                $idModulo = $modulo['id_modulo'];
                $nombreModulo = $modulo['nombre'];
                $duracionModulo = $modulo['duracion'];

                if ($this->model->existeModulo($idModulo)) {
                    $this->model->updateModulo($idModulo, $nombreModulo, $duracionModulo);
                } else {
                    $this->model->insertModulo($idModulo, $nombreModulo, $idCurso, $duracionModulo);
                    $this->model->insertCursoWithModulo($idModulo, $idCurso);
                }

                // Obtener unidades existentes para el módulo
                $unidadesExistentes = $this->model->getUnidadesPorModulo($idModulo);
                $unidadesNuevas = array_column($modulo['unidades'], 'id_unidad');

                // Eliminar unidades formativas que no están en los datos enviados
                foreach ($unidadesExistentes as $unidad) {
                    if (!in_array($unidad['ID_Unidad_Formativa'], $unidadesNuevas)) {
                        $this->model->deleteUnidadFormativa($unidad['ID_Unidad_Formativa']);
                    }
                }

                // Procesar cada unidad formativa enviada
                foreach ($modulo['unidades'] as $unidad) {
                    $idUnidad = $unidad['id_unidad'];
                    $nombreUnidad = $unidad['nombre'];
                    $duracionUnidad = $unidad['duracion'];

                    if ($this->model->existeUnidad($idUnidad)) {
                        $this->model->updateUnidadFormativa($idUnidad, $nombreUnidad, $duracionUnidad);
                    } else {
                        $this->model->insertUnidadFormativa($idModulo, $nombreUnidad, $idUnidad, $duracionUnidad);
                    }
                }
            }

            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Curso actualizado correctamente']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()]);
        }
        exit;
    }
    
}
