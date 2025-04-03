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
}
