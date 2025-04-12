<?php
// controllers/RecibosController.php

require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../models/RecibosModel.php';

/**
 * Controlador para la gestión de recibos.
 *
 * Este controlador se encarga de manejar las solicitudes relacionadas con los recibos,
 * incluyendo la visualización, filtrado y actualización (por ejemplo, marcar recibos como pagados).
 * Verifica que la sesión esté activa antes de procesar cualquier solicitud.
 *
 * @package YourPackageName
 */
class RecibosController
{
    /**
     * Instancia del modelo de recibos.
     *
     * @var RecibosModel
     */
    private $model;

    /**
     * Constructor del controlador.
     *
     * Inicializa la instancia del modelo de recibos.
     */
    public function __construct()
    {
        $this->model = new RecibosModel();
    }

    /**
     * Muestra la vista de recibos o la vista parcial de la tabla de recibos filtrados.
     *
     * Si se reciben parámetros de filtrado mediante GET (como apellido, año, mes, curso o estado pendiente),
     * se obtiene la lista filtrada de recibos y se carga la vista parcial correspondiente (tabla_recibos.php).
     * De lo contrario, se carga la vista principal de recibos (recibos.php).
     *
     * @return void
     */
    public function create()
    {
        if (!isset($_SESSION['usuario'])) {
            header("Location: login.php");
            exit;
        }
        if (
            $_SERVER['REQUEST_METHOD'] === 'GET' &&
            (isset($_GET['apellido1']) || isset($_GET['anio']) || isset($_GET['mes']) || isset($_GET['curso']) || isset($_GET['pendientes']))
        ) {
            $apellido1 = $_GET['apellido1'] ?? '';
            $anio = $_GET['anio'] ?? '';
            $mes = $_GET['mes'] ?? '';
            $curso = $_GET['curso'] ?? '';
            $pendientes = isset($_GET['pendientes']) && $_GET['pendientes'] == '1';

            $recibos = $this->model->getRecibos($apellido1, $anio, $mes, $curso, $pendientes);
            require_once __DIR__ . '/../views/users/tabla_recibos.php';
        } else {
            require_once __DIR__ . '/../views/users/recibos.php';
        }
    }

    /**
     * Actualiza el estado de un recibo para marcarlo como pagado.
     *
     * Este método procesa una solicitud POST, verifica que la sesión esté activa y que se haya enviado
     * un ID válido, y luego llama al modelo para actualizar el recibo (estableciendo la fecha de pago al día actual
     * y el estado a 'Cobrado'). La respuesta se devuelve en formato JSON.
     *
     * @return void
     */
    public function actualizarPago()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (!isset($_SESSION['usuario'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no especificado']);
            exit;
        }
        $resultado = $this->model->actualizarPago($id);
        echo json_encode(['success' => $resultado]);
        exit;
    }

    /**
     * Actualiza un recibo con nuevos datos (estado y fecha de pago).
     *
     * Este método procesa una solicitud POST en la que se envían datos en formato JSON para modificar
     * un recibo. Se valida que se hayan recibido los datos necesarios (ID y estado). Si el nuevo estado es 'Pendiente',
     * la fecha de pago se establece a NULL; de lo contrario, si no se proporciona una fecha se asigna la fecha actual.
     * Se registra la información para depuración y se devuelve la respuesta en JSON.
     *
     * @return void
     */
    public function updateRecibo()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (!isset($_SESSION['usuario'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        if (ob_get_length()) {
            ob_clean();
        }
        if (!isset($data['id']) || !isset($data['estado'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }
        $id = $data['id'];
        $estado = $data['estado'];
        $fecha_pago = $data['fecha_pago'] ?? null;
        if ($estado == 'Pendiente') {
            $fecha_pago = null;
        } else {
            if (empty($fecha_pago)) {
                $fecha_pago = date('Y-m-d');
            }
        }
        // Registrar para depuración (opcional)
        file_put_contents('debug.log', "updateRecibo - Datos recibidos: " . json_encode($data) . "\n", FILE_APPEND);
        $resultado = $this->model->updateRecibo($id, $estado, $fecha_pago);
        echo json_encode(['success' => $resultado]);
        exit;
    }
}
