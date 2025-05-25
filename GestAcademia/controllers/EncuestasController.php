<?php
class EncuestasController {
    private $model;

    public function __construct() {
        require_once __DIR__ . '/../init.php';  // Sesión y auth
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login.php');
            exit();
        }
        require_once __DIR__ . '/../models/EncuestasModel.php';
        $this->model = new EncuestasModel();
    }

    // Muestra el formulario con datos precargados
    public function index() {
        $idAlumno = $_SESSION['usuario'];
        $datos = $this->model->getDatosFormulario($idAlumno);
        require_once __DIR__ . '/../views/encuestas/encuesta1.php';
    }

    // Procesa el envío del formulario (POST)
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Método no permitido.";
            return;
        }

        $idAlumno = $_SESSION['usuario'];
        // Mapear texto a puntuación numérica:
        $map = ['Excelente'=>10,'Bueno'=>7,'Correcto'=>5,'Regular'=>3,'Deficiente'=>1];
        // Recoger datos comunes
        $dataC = [
            'id_alumno'  => $idAlumno,
            'id_curso'   => $_POST['courseId'],
            'q1'         => $map[$_POST['q1_valoracion_global']] ?? 0,
            // ... idéntico para q2_1…q4 y comentarios
            'q5_coment'  => $_POST['q5_mejorar_select'],
            'q6_coment'  => $_POST['q6_sugerencias_select']
        ];
        $ok1 = $this->model->guardarEncuestaCentro($dataC);

        $dataP = [
            'id_alumno'     => $idAlumno,
            'id_profesor'   => $_POST['profesorId'],
            'pq1_1'         => $map[$_POST['pq1_1_claridad']] ?? 0,
            // ... hasta pq1_7
            'pq2_coment'    => $_POST['pq2_mejorar_profesor']
        ];
        $ok2 = $this->model->guardarEncuestaProfesor($dataP);

        if ($ok1 && $ok2) {
            header('Location: /encuestas?success=Encuesta guardada');
        } else {
            header('Location: /encuestas?error=Fallo al guardar');
        }
    }
}
