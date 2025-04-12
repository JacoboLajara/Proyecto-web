<?php
require_once __DIR__ . '/../models/EdicionCursosModel.php';

class EdicionCursosController {
    private $model;

    public function __construct() {
        require_once __DIR__ . '/../init.php'; // Cargar sesión
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
            header('Location: /login.php');
            exit();
        }
        $this->model = new EdicionCursosModel();
    }

    public function listarEdiciones() {
        $idCurso = $_GET['idCurso'] ?? null;
        if (!$idCurso) {
            echo json_encode(["error" => "ID del curso no proporcionado"]);
            exit;
        }
        $ediciones = $this->model->getEdicionesPorCurso($idCurso);
        echo json_encode($ediciones);
        exit;
    }

    public function guardarEdicion() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['idCurso'], $data['fechaInicio'], $data['fechaFin'], $data['estado'])) {
            echo json_encode(["error" => "Datos incompletos"]);
            exit;
        }
        $resultado = $this->model->crearEdicion($data['idCurso'], $data['fechaInicio'], $data['fechaFin'], $data['estado']);
        echo json_encode(["success" => $resultado]);
        exit;
    }

    public function actualizarEdicion() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['idEdicion'], $data['fechaInicio'], $data['fechaFin'], $data['estado'])) {
            echo json_encode(["error" => "Datos incompletos"]);
            exit;
        }
        $resultado = $this->model->actualizarEdicion($data['idEdicion'], $data['fechaInicio'], $data['fechaFin'], $data['estado']);
        echo json_encode(["success" => $resultado]);
        exit;
    }

    public function eliminarEdicion() {
        $idEdicion = $_GET['idEdicion'] ?? null;
        if (!$idEdicion) {
            echo json_encode(["error" => "ID de la edición no proporcionado"]);
            exit;
        }
        $resultado = $this->model->eliminarEdicion($idEdicion);
        echo json_encode(["success" => $resultado]);
        exit;
    }

    public function consultaCursos() {
        require_once __DIR__ . '/../models/CursosModel.php';
        $model = new CursosModel();
        
        $cursos = $model->getTodosLosCursos();
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($cursos);
        exit;
    }
    
}
?>
