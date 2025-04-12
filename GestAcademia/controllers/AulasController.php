<?php
/**
 * Controlador para la gestión de aulas.
 *
 * Este controlador se encarga de manejar las solicitudes relacionadas con las aulas,
 * tales como la creación y actualización de aulas. Verifica que el usuario esté autenticado
 * antes de permitir el acceso a sus operaciones.
 *
 * @package YourPackageName
 */
class AulasController
{
    /**
     * Instancia del modelo de aulas.
     *
     * @var AulasModel
     */
    private $model;

    /**
     * Constructor del controlador.
     *
     * Incluye el archivo init.php para iniciar la sesión y verificar la autenticación.
     * Si el usuario no está autenticado, se redirige al login.
     * Luego, incluye el modelo de aulas y crea su instancia.
     */
    public function __construct()
    {
        // Incluir init.php para iniciar la sesión y verificar si el usuario está autenticado
        require_once __DIR__ . '/../init.php';  // Ruta ajustada según la estructura del proyecto

        // Depuración: Verificar valores de $_SESSION (comentado para producción)
        /* echo "<pre>";
           var_dump($_SESSION);
           echo "</pre>"; */

        // Verificar si el usuario no está autenticado
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
            header('Location: /login.php');
            exit();
        }

        // Incluir el modelo de aulas y crear su instancia
        require_once __DIR__ . '/../models/AulasModel.php';
        $this->model = new AulasModel();
    }

    /**
     * Muestra la vista de gestión de aulas.
     *
     * Incluye la vista correspondiente para la gestión de aulas.
     */
    public function create()
    {
        require_once __DIR__ . '/../views/users/aulas.php';
    }

    /**
     * Procesa el formulario para insertar o actualizar aulas.
     *
     * Determina la acción a realizar (insertar o actualizar) en función del valor del
     * campo 'accion' enviado por el formulario y llama al método correspondiente.
     */
    public function storeAulas()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger el valor del campo 'accion' para diferenciar entre inserción o actualización
            $accion = $_POST['accion'] ?? 'insert';

            // Llamar a los métodos correspondientes según el valor de 'accion'
            if ($accion === 'insert') {
                $this->insertAulas();
            } elseif ($accion === 'update') {
                $this->updateAulas();
            } else {
                echo "Acción no válida.";
            }
        } else {
            echo "Método no permitido.";
        }
    }

    /**
     * Inserta una nueva aula.
     *
     * Recoge y valida los datos enviados por el formulario. Si existen errores en la validación,
     * se muestra un mensaje de error. Además, verifica si el aula ya existe para evitar duplicados.
     * Finalmente, llama al modelo para insertar el aula y redirige según el resultado.
     */
    private function insertAulas()
    {
        // Validar y recoger los datos del formulario
        $id_aula   = $_POST['id_aula']   ?? null;
        $nombre    = $_POST['nombre']    ?? null;
        $capacidad = $_POST['capacidad'] ?? null;

        // Validar campos obligatorios
        $errores = [];
        if (!$id_aula)
            $errores[] = "El campo 'ID aula' es obligatorio.";
        if (!$nombre)
            $errores[] = "El campo 'Nombre' es obligatorio.";
        if (!$capacidad)
            $errores[] = "El campo 'capacidad' es obligatorio.";

        if (!empty($errores)) {
            echo "Error: " . implode('<br>', $errores);
            return;
        }

        // Verificar si el aula ya existe antes de insertar
        if ($this->model->existeAula($id_aula)) {
            // Redirigir con un mensaje de error si el aula ya existe
            header("Location: /views/users/aulas.php?error=" . urlencode("El aula con ID {$id_aula} ya existe."));
            exit;
        }

        // Llamar al modelo para insertar el aula
        $success = $this->model->insertAulas(
            $id_aula,
            $nombre,
            $capacidad
        );

        if ($success) {
            // Redirigir con mensaje de éxito
            header("Location: /views/users/aulas.php?success=" . urlencode("Aula creada correctamente."));
            exit;
        } else {
            // Redirigir con mensaje de error genérico en caso de fallo
            header("Location: /views/users/aulas.php?error=" . urlencode("Error al crear el aula."));
            exit;
        }
    }

    /**
     * Actualiza los datos de un aula existente.
     *
     * Recoge y valida los datos enviados por el formulario. Si existen errores en la validación,
     * muestra un mensaje de error. Luego, llama al modelo para actualizar la información del aula
     * y redirige según el resultado de la operación.
     */
    private function updateAulas()
    {
        // Validar y recoger los datos del formulario
        $id_aula   = $_POST['id_aula']   ?? null;
        $nombre    = $_POST['nombre']    ?? null;
        $capacidad = $_POST['capacidad'] ?? null;

        // Validar campos obligatorios
        $errores = [];
        if (!$id_aula)
            $errores[] = "El campo 'ID aula' es obligatorio.";
        if (!$nombre)
            $errores[] = "El campo 'Nombre' es obligatorio.";
        if (!$capacidad)
            $errores[] = "El campo 'capacidad' es obligatorio.";

        if (!empty($errores)) {
            echo "Error: " . implode('<br>', $errores);
            return;
        }

        // Llamar al modelo para actualizar el aula
        $success = $this->model->updateAulas(
            $id_aula,
            $nombre,
            $capacidad
        );

        if ($success) {
            // Redirigir con mensaje de éxito
            header("Location: /views/users/aulas.php?success=" . urlencode("Aula actualizada correctamente."));
            exit;
        } else {
            // Redirigir con mensaje de error genérico en caso de fallo
            header("Location: /views/users/aulas.php?error=" . urlencode("Error al actualizar el aula."));
            exit;
        }
    }
}
