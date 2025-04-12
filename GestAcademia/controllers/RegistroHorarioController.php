<?php
require_once __DIR__ . '/../models/RegistroHorarioModel.php';

class RegistroHorarioController
{
    private $model;

    public function __construct()
    {
        require_once __DIR__ . '/../init.php';
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
            header('Location: /login.php');
            exit();
        }

        $this->model = new RegistroHorarioModel();
    }

    // Muestra la vista principal
    public function create()
    {
        require_once __DIR__ . '/../views/users/controlHorario.php';
    }

    // Inserta un nuevo registro
    public function store()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        file_put_contents('debug.log', "🟨 Datos recibidos para insertar horario: " . json_encode($data) . "\n", FILE_APPEND);

        // Validar campos requeridos
        $camposRequeridos = ['ID_Usuario', 'Fecha', 'Tipo_Jornada', 'Tipo_Dia'];
        foreach ($camposRequeridos as $campo) {
            if (!isset($data[$campo])) {
                echo json_encode(['success' => false, 'message' => "Falta el campo $campo"]);
                exit;
            }
        }

        $resultado = $this->model->insertarRegistro($data);

        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Registro guardado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar el registro']);
        }
        exit;
    }

    // Obtener registros por usuario y rango
    public function listarPorUsuario()
    {
        header('Content-Type: application/json; charset=utf-8');

        $idUsuario = $_GET['idUsuario'] ?? null;
        $inicio = $_GET['inicio'] ?? null;
        $fin = $_GET['fin'] ?? null;

        if (!$idUsuario || !$inicio || !$fin) {
            echo json_encode(['success' => false, 'message' => 'Parámetros incompletos']);
            exit;
        }

        $resultados = $this->model->obtenerPorUsuarioYRango($idUsuario, $inicio, $fin);

        echo json_encode(['success' => true, 'datos' => $resultados]);
        exit;
    }

    // Obtener todos los registros por rango
    public function listarTodos()
    {
        header('Content-Type: application/json; charset=utf-8');

        $inicio = $_GET['inicio'] ?? null;
        $fin = $_GET['fin'] ?? null;

        if (!$inicio || !$fin) {
            echo json_encode(['success' => false, 'message' => 'Parámetros incompletos']);
            exit;
        }

        $resultados = $this->model->obtenerTodosPorRango($inicio, $fin);

        echo json_encode(['success' => true, 'datos' => $resultados]);
        exit;
    }

    public function listarSemanaActualPorUsuario()
    {
        header('Content-Type: application/json; charset=utf-8');

        $idUsuario = $_GET['idUsuario'] ?? null;
        if (!$idUsuario) {
            echo json_encode(['success' => false, 'message' => 'ID de usuario no proporcionado']);
            exit;
        }

        // Calcular lunes de esta semana
        $hoy = new DateTime();
        $lunes = clone $hoy;
        $lunes->modify('monday this week');

        $inicio = $lunes->format('Y-m-d');
        $fin = $hoy->format('Y-m-d');

        $resultados = $this->model->obtenerPorUsuarioYRango($idUsuario, $inicio, $fin);

        echo json_encode(['success' => true, 'datos' => $resultados]);
        exit;
    }

    public function exportarHorarioPDF()
    {
        require_once __DIR__ . '/../exportadores/ExportadorHorarioPDF.php';
        exit; // El script genera y descarga el PDF directamente
    }
    
    public function exportarHorarioExcel()
    {
        require_once __DIR__ . '/../exportadores/ExportadorHorarioExcel.php';
        exit; // El script genera y descarga el Excel directamente
    }
    
private function getLunesActual()
{
    $diaSemana = date('w'); // 0 (domingo) a 6 (sábado)
    $offset = $diaSemana === '0' ? 6 : $diaSemana - 1;
    return date('Y-m-d', strtotime("-$offset days"));
}


}
