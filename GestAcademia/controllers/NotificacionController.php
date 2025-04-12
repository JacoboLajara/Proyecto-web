<?php
/**
 * Controlador para la gestión de notificaciones.
 *
 * Este controlador se encarga de manejar las solicitudes relacionadas con el envío de notificaciones
 * a usuarios, ya sea de forma individual o por grupos basados en el tipo de destinatario. Verifica
 * que el usuario esté autenticado antes de permitir el acceso a sus operaciones.
 *
 * @package YourPackageName
 */
class NotificacionController
{
    /**
     * Instancia del modelo de notificaciones.
     *
     * @var NotificacionModel
     */
    private $model;

    /**
     * Constructor del controlador.
     *
     * Incluye el archivo init.php para iniciar la sesión y verificar que el usuario esté autenticado.
     * Si el usuario no está autenticado, se redirige a la página de login. Luego, se incluye el modelo
     * de notificaciones y se instancia.
     */
    public function __construct()
    {
        require_once __DIR__ . '/../init.php';
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
            header('Location: /login.php');
            exit();
        }

        require_once __DIR__ . '/../models/NotificacionModel.php';
        $this->model = new NotificacionModel();
    }

    /**
     * Muestra la vista para la gestión de notificaciones.
     *
     * Carga la vista correspondiente para crear y enviar notificaciones. Además, registra
     * en el log que se está ejecutando este método.
     *
     * @return void
     */
    public function create()
    {
        require_once __DIR__ . '/../views/users/notificaciones.php';
        file_put_contents('debug.log', "Ejecutando NotificacionController::create()\n", FILE_APPEND);
    }

    /**
     * Procesa el envío de notificaciones.
     *
     * Este método se encarga de recibir los datos del formulario mediante una solicitud POST,
     * validar que se haya ingresado un mensaje y un tipo de destinatario, y enviar la notificación.
     * Si se seleccionan usuarios específicos, se envía la notificación a esos usuarios; de lo contrario,
     * se obtienen todos los usuarios activos del tipo especificado y se envía la notificación a cada uno.
     * Finalmente, se muestra un mensaje de alerta en el navegador y se redirige a la página principal.
     *
     * @return void
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mensaje = $_POST['notificacion'] ?? null;
            $tipoDestinatario = $_POST['opciones'] ?? null;
            $usuariosSeleccionados = $_POST['usuarios'] ?? [];

            file_put_contents('debug.log', "Entrando en store() - Mensaje: $mensaje, Tipo: $tipoDestinatario\n", FILE_APPEND);

            if (!$mensaje || !$tipoDestinatario) {
                echo "Error: El mensaje y el tipo de destinatario son obligatorios.";
                return;
            }

            $notificacionesEnviadas = 0;

            // Si se han seleccionado usuarios específicos, enviar la notificación a cada uno
            if (!empty($usuariosSeleccionados)) {
                foreach ($usuariosSeleccionados as $idUsuario) {
                    if ($this->model->insertarNotificacion($idUsuario, $mensaje, $tipoDestinatario)) {
                        $notificacionesEnviadas++;
                    }
                }
            } else {
                // Si no se seleccionaron usuarios, obtener todos los usuarios activos del tipo indicado
                $usuarios = $this->model->obtenerUsuariosActivosPorTipo($tipoDestinatario);
                if (empty($usuarios)) {
                    echo '<script>
                            alert("No se encontraron usuarios activos del tipo seleccionado.");
                            window.location.href = "mainpage.php?route=createNotificacion";
                          </script>';
                    exit;
                }
                foreach ($usuarios as $usuario) {
                    if ($this->model->insertarNotificacion($usuario['ID_Usuario'], $mensaje, $tipoDestinatario)) {
                        $notificacionesEnviadas++;
                    }
                }
            }

            echo '<script>
                    alert("Notificación enviada correctamente a ' . $notificacionesEnviadas . ' destinatarios.");
                    window.location.href = "mainpage.php?route=createNotificacion";
                  </script>';
            exit;
        } else {
            echo "Método no permitido.";
        }
    }

    /**
     * Obtiene los usuarios activos según el tipo de destinatario.
     *
     * Este método lee el parámetro GET 'type' para determinar el tipo de destinatario y
     * consulta el modelo para obtener los usuarios activos de ese tipo. Luego, devuelve la
     * respuesta en formato JSON.
     *
     * @return void
     */
    public function getUsuariosPorTipo()
    {
        $tipo = $_GET['type'] ?? null;
        if (!$tipo) {
            echo json_encode([]);
            return;
        }

        $usuarios = $this->model->obtenerUsuariosActivosPorTipo($tipo);
        file_put_contents('debug.log', "Usuarios obtenidos para el tipo $tipo: " . print_r($usuarios, true) . "\n", FILE_APPEND);

        echo json_encode($usuarios);
    }
}
