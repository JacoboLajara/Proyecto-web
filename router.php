<?php
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/AlumnosController.php';
require_once __DIR__ . '/controllers/ProfesoresController.php';
require_once __DIR__ . '/controllers/PersonalController.php';
require_once __DIR__ . '/controllers/CursosController.php';
require_once __DIR__ . '/controllers/AulasController.php';
require_once __DIR__ . '/controllers/MatriculasController.php';
require_once __DIR__ . '/controllers/MatricularProfesoresController.php';
require_once __DIR__ . '/controllers/NotasController.php';
require_once __DIR__ . '/controllers/HorariosController.php';
require_once __DIR__ . '/controllers/RecibosController.php';
require_once __DIR__ . '/controllers/NotificacionController.php';
require_once __DIR__ . '/controllers/RegistroHorarioController.php';
require_once __DIR__ . '/init.php';

// Registrar la solicitud en debug.log
file_put_contents('debug.log', "Método: {$_SERVER['REQUEST_METHOD']} - URI: {$_SERVER['REQUEST_URI']} - IP: {$_SERVER['REMOTE_ADDR']}\n", FILE_APPEND);

// Normalizar la variable $route
$route = isset($_GET['route']) ? trim($_GET['route']) : 'home';
file_put_contents('debug.log', "Valor de route : $route\n", FILE_APPEND);

// Verificación del rol para redirección específica
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Alumno') {
    header('Location: /views/alumnos/backAlumnos.php');  // Redirige al panel de alumno
    exit();
}

// Manejo de rutas
switch ($route) {
    case 'backoffice':
        require_once __DIR__ . '/views/users/backoffice.php';
        exit;

    case 'usuarios':
        $controller = new UserController();
        $controller->index();
        exit;

    case 'searchUser':
        $controller = new UserController();
        $controller->searchUser();
        exit;

    case 'updatePassword':
        $controller = new UserController();
        $controller->updatePassword();
        exit;

    case 'deletePassword':
        $controller = new UserController();
        $controller->deletePassword();
        exit;


    case 'createAlumno':
        $controller = new AlumnosController();
        $controller->create();
        exit;

    case 'storeAlumno':
        $controller = new AlumnosController();
        $controller->store();
        exit;

    case 'createProfesor':
        $controller = new ProfesoresController();
        $controller->create();
        exit;

    case 'storeProfesor':
        $controller = new ProfesoresController();
        $controller->store();
        exit;

    case 'createPersonal':
        $controller = new PersonalController();
        $controller->create();
        exit;

    case 'storePersonal':
        $controller = new PersonalController();
        $controller->store();
        exit;

    case 'createCurso':
        $controller = new CursosController();
        $controller->create();
        exit;

    case 'storeCurso':
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        $controller = new CursosController();
        $controller->storeCurso();
        exit;

    case 'storeUnidadFormativa':
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        $controller = new CursosController();
        $controller->storeUnidadFormativa();
        exit;

    case 'storeModulo':
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        $controller = new CursosController();
        $controller->storeModulo();
        exit;

    case 'createAula':
        file_put_contents('debug.log', "Entrando al caso createAula\n", FILE_APPEND);
        $controller = new AulasController();
        $controller->create();
        exit;

    case 'storeAulas':
        file_put_contents('debug.log', "Entrando al caso storeCurso\n", FILE_APPEND);
        header('Content-Type: application/json; charset=utf-8');
        $controller = new AulasController();
        $controller->storeAulas();
        exit;

    case 'createHorario':
        $controller = new HorariosController();
        $controller->create();
        exit;

    case 'storeHorario':
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        $controller = new HorariosController();
        $controller->storeHorario();
        exit;

    case 'getHorariosOcupados':
        if (ob_get_length()) {
            ob_clean();
        }
        $controller = new HorariosController();
        $controller->getHorariosOcupados();
        exit;

    case 'getHorariosPorCurso':
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        $controller = new HorariosController();
        $controller->getHorariosPorCurso();
        exit;

    case 'createNotas':
        if (ob_get_length()) {
            ob_clean();
        }
        $controller = new NotasController();
        $controller->create();
        exit;

    case 'getAlumnosPorCurso':
        header('Content-Type: application/json; charset=utf-8');
        if (ob_get_length()) {
            ob_clean();
        }
        $controller = new NotasController();
        $controller->getAlumnosPorCurso();
        exit;

    case 'getNotasPorAlumno':
        header('Content-Type: application/json; charset=utf-8');
        if (ob_get_length()) {
            ob_clean();
        }
        $controller = new NotasController();
        $controller->getNotasPorAlumno();
        exit;

    case 'updateNota':
        if (ob_get_length()) {
            ob_clean();
        }
        $controller = new NotasController();
        $controller->updateNota();
        exit;

    case 'storeNota':
        if (ob_get_length()) {
            ob_clean();
        }
        $controller = new NotasController();
        $controller->storeNota();
        exit;

    case 'getModulosUnidadesPorCurso':
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        $controller = new NotasController();
        $controller->getModulosUnidadesPorCurso();
        exit;

    case 'storeMatricula':
        header('Content-Type: application/json; charset=utf-8');
        $controller = new MatriculasController();
        $controller->store();
        exit;

    case 'createMatricula':
        $controller = new MatriculasController();
        $controller->create();
        exit;

    case 'createMatriculaprofesor':
        $controller = new MatricularProfesoresController();
        $controller->create();
        exit;

    case 'storeAsignacion':
        header('Content-Type: application/json; charset=utf-8');
        $controller = new MatricularProfesoresController();
        $controller->store();
        exit;

    case 'getAlumnosMatriculados':
        if (ob_get_length()) {
            ob_clean();
        }
        $controller = new MatriculasController();
        $controller->getAlumnosMatriculados();
        exit;
    case 'listaAlumnos':
        $controller = new MatriculasController();
        $controller->mostrarVistaListaAlumnos();
        exit;

    case 'bajaAlumnos':
        $controller = new MatriculasController();
        $controller->mostrarVistaBajaAlumnos();
        exit;

    case 'procesarBajaAlumnos':
        if (ob_get_length()) {
            ob_clean();
        }
        $controller = new MatriculasController();
        $controller->procesarBajaAlumnos();
        exit;

    case 'createNotificacion':
        $controller = new NotificacionController();
        $controller->create();
        exit;

    case 'storeNotificacion':
        $controller = new NotificacionController();
        $controller->store();
        exit;

    case 'getUsuariosPorTipo':
        if (ob_get_length()) {
            ob_clean();
        }
        $controller = new NotificacionController();
        $controller->getUsuariosPorTipo();
        exit;

    case 'recibos':
        $controller = new RecibosController();
        $controller->create();
        exit;

    case 'updateRecibo':
        if (ob_get_length()) {
            ob_clean();
        }
        $controller = new RecibosController();
        $controller->updateRecibo();
        exit;
    case 'actualizarPagoRecibo':
        if (ob_get_length()) {
            ob_clean();
        }
        $controller = new RecibosController();
        $controller->actualizarPago();
        exit;

    case 'consultaCursos':
        require_once __DIR__ . '/views/users/consultaCursos.php';
        exit;
    case 'getModulosYUnidadesPorCurso':
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        $controller = new CursosController();
        $controller->getModulosYUnidadesPorCurso();
        exit;

    case 'updateCurso':
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['id_curso']) || !isset($data['modulos'])) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            exit;
        }

        require_once __DIR__ . '/controllers/CursosController.php';
        $controller = new CursosController();
        $resultado = $controller->updateCurso($data);

        echo json_encode($resultado);
        exit;

    // 📌 Rutas de gestión de ediciones de cursos
    case 'listarEdiciones':
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        require_once __DIR__ . '/controllers/EdicionCursosController.php';
        $controller = new EdicionCursosController();
        $controller->listarEdiciones();
        exit;

    case 'guardarEdicion':
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        require_once __DIR__ . '/controllers/EdicionCursosController.php';
        $controller = new EdicionCursosController();
        $controller->guardarEdicion();
        exit;

    case 'actualizarEdicion':
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        require_once __DIR__ . '/controllers/EdicionCursosController.php';
        $controller = new EdicionCursosController();
        $controller->actualizarEdicion();
        exit;

    case 'consultaCursos':
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');

        require_once __DIR__ . '/controllers/EdicionCursosController.php';
        $controller = new EdicionCursosController();
        $controller->consultaCursos();
        exit;


    case 'eliminarEdicion':
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        require_once __DIR__ . '/controllers/EdicionCursosController.php';
        $controller = new EdicionCursosController();
        $controller->eliminarEdicion();
        exit;

    case 'gestionarEdicionCursos':
        require_once __DIR__ . '/views/users/EdicionCursos.php';
        exit;

    case 'storeHorarioEmpleado':
        if (ob_get_length())
            ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        $controller = new RegistroHorarioController();
        $controller->store();
        exit;

    case 'createHorarioEmpleado':
        $controller = new RegistroHorarioController();
        $controller->create();
        exit;

    case 'listarPorUsuario':
        header('Content-Type: application/json; charset=utf-8');
        $controller = new RegistroHorarioController();
        $controller->listarPorUsuario();
        exit;

        case 'exportarHorarioPDF':
            (new RegistroHorarioController())->exportarHorarioPDF();
            break;
        case 'exportarHorarioExcel':
            (new RegistroHorarioController())->exportarHorarioExcel();
            break;




    default:
        require_once __DIR__ . '/mainpage.php';
        break;
}
