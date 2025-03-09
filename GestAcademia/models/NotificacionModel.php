<?php
/**
 * Modelo para la gestión de notificaciones.
 *
 * Este modelo se encarga de interactuar con la base de datos para la creación y consulta
 * de notificaciones dirigidas a roles, cursos, usuarios específicos y por tipo de destinatario.
 *
 * @package YourPackageName
 */
class NotificacionModel
{
    /**
     * Conexión a la base de datos.
     *
     * @var mysqli
     */
    private $conn;

    /**
     * Constructor de la clase.
     *
     * Inicializa la conexión a la base de datos mediante la inclusión del archivo de configuración.
     * Si la conexión falla, termina la ejecución del script.
     */
    public function __construct()
    {
        // Incluir la conexión a la base de datos
        $this->conn = include __DIR__ . '/../config/conexion.php';

        if (!$this->conn) {
            die("Error: La conexión no se pudo establecer correctamente.");
        }
    }

    /**
     * Obtiene el ID de un rol a partir de su nombre.
     *
     * Ejecuta una consulta para obtener el identificador del rol correspondiente al nombre proporcionado.
     *
     * @param string $nombreRol Nombre del rol.
     * @return mixed El ID del rol o null si no se encuentra.
     */
    public function obtenerIdRol($nombreRol)
    {
        // Obtener el ID del rol según su nombre
        $sql = "SELECT ID_Rol FROM Rol WHERE Nombre = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $this->conn->error);
        }

        $stmt->bind_param("s", $nombreRol);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['ID_Rol'] ?? null;
    }

    /**
     * Inserta una notificación para un rol específico.
     *
     * Registra en la tabla Notificacion un mensaje asociado a un rol mediante su ID.
     *
     * @param int    $idRol   Identificador del rol.
     * @param string $mensaje Mensaje de la notificación.
     * @return bool True si la inserción fue exitosa, false en caso contrario.
     */
    public function insertarNotificacionPorRol($idRol, $mensaje)
    {
        // Insertar notificación para un rol específico
        $sql = "INSERT INTO Notificacion (ID_Rol, Mensaje) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $this->conn->error);
        }

        $stmt->bind_param("is", $idRol, $mensaje);
        return $stmt->execute();
    }

    /**
     * Inserta una notificación para un curso específico.
     *
     * Registra en la tabla Notificacion un mensaje asociado a un curso mediante su ID.
     *
     * @param string $idCurso Identificador del curso.
     * @param string $mensaje Mensaje de la notificación.
     * @return bool True si la inserción fue exitosa, false en caso contrario.
     */
    public function insertarNotificacionPorCurso($idCurso, $mensaje)
    {
        // Insertar notificación para un curso específico
        $sql = "INSERT INTO Notificacion (ID_Curso, Mensaje) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $this->conn->error);
        }

        $stmt->bind_param("ss", $idCurso, $mensaje);
        return $stmt->execute();
    }

    /**
     * Inserta una notificación para un usuario específico.
     *
     * Registra en la tabla Notificacion un mensaje dirigido a un usuario, especificando el tipo de destinatario.
     *
     * @param string $idUsuario       Identificador del usuario.
     * @param string $mensaje         Mensaje de la notificación.
     * @param string $tipoDestinatario Tipo de destinatario.
     * @return bool True si la inserción fue exitosa, false en caso de error.
     */
    public function insertarNotificacionPorUsuario($idUsuario, $mensaje, $tipoDestinatario)
    {
        $sql = "INSERT INTO Notificacion (ID_Usuario, Mensaje, Tipo_Destinatario) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $this->conn->error);
        }

        $stmt->bind_param("sss", $idUsuario, $mensaje, $tipoDestinatario);
        return $stmt->execute();
    }

    /**
     * Obtiene todos los usuarios.
     *
     * Ejecuta una consulta para recuperar los identificadores de todos los usuarios registrados.
     *
     * @return array Array asociativo con los usuarios.
     */
    public function obtenerTodosLosUsuarios()
    {
        // Obtener todos los usuarios de la tabla Usuario
        $sql = "SELECT ID_Usuario FROM Usuario";
        $result = $this->conn->query($sql);
        if ($result === false) {
            die("Error en la consulta: " . $this->conn->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtiene los usuarios activos según el tipo especificado.
     *
     * Según el tipo (Alumno, Profesor, Personal o Curso), ejecuta una consulta para obtener
     * los usuarios activos o con matrícula activa.
     *
     * @param string $tipo Tipo de usuario ('Alumno', 'Profesor', 'Personal', 'Curso').
     * @return array Array asociativo con los usuarios activos o un array vacío si el tipo no coincide.
     */
    public function obtenerUsuariosActivosPorTipo($tipo)
    {
        $sql = '';

        switch ($tipo) {
            case 'Alumno':
                $sql = "SELECT ID_Alumno AS ID_Usuario, Nombre FROM Alumno WHERE Fecha_Baja='2000-01-01'";
                break;
            case 'Profesor':
                $sql = "SELECT ID_Profesor AS ID_Usuario, Nombre FROM Profesor WHERE Fecha_Baja IS NULL";
                break;
            case 'Personal':
                $sql = "SELECT ID_Personal AS ID_Usuario, Nombre FROM Personal_No_Docente WHERE Fecha_Baja IS NULL";
                break;
            case 'Curso':
                $sql = "SELECT DISTINCT ac.ID_Curso AS ID_Usuario 
                            FROM Alumno_Curso ac 
                            WHERE ac.Estado = 'Activo'";
                break;

            default:
                return [];
        }

        $result = $this->conn->query($sql);
        if ($result === false) {
            file_put_contents('debug.log', "Error en la consulta SQL: " . $this->conn->error . "\n", FILE_APPEND);
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Inserta una notificación en una tabla específica según el tipo de destinatario.
     *
     * Dependiendo del tipo (Alumno, Profesor, Personal o Curso), se selecciona la tabla y columna
     * correspondiente para registrar la notificación.
     *
     * @param string $idUsuario Identificador del usuario, profesor, personal o curso.
     * @param string $mensaje   Mensaje de la notificación.
     * @param string $tipo      Tipo de destinatario ('Alumno', 'Profesor', 'Personal', 'Curso').
     * @return bool True si la inserción fue exitosa, false en caso contrario.
     */
    public function insertarNotificacion($idUsuario, $mensaje, $tipo)
    {
        $tabla = '';
        $columna = '';

        switch ($tipo) {
            case 'Alumno':
                $tabla = 'Notificacion_Alumno';
                $columna = 'ID_Alumno';
                break;
            case 'Profesor':
                $tabla = 'Notificacion_Profesor';
                $columna = 'ID_Profesor';
                break;
            case 'Personal':
                $tabla = 'Notificacion_Personal';
                $columna = 'ID_Personal';
                break;
            case 'Curso':
                $tabla = 'Notificacion_Curso';
                $columna = 'ID_Curso';
                break;
        }

        if ($tabla) {
            $sql = "INSERT INTO {$tabla} ({$columna}, Mensaje) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                file_put_contents('debug.log', "Error en la preparación de la consulta: " . $this->conn->error . "\n", FILE_APPEND);
                return false;
            }

            $stmt->bind_param("ss", $idUsuario, $mensaje);

            if (!$stmt->execute()) {
                file_put_contents('debug.log', "Error en la inserción: " . $stmt->error . "\n", FILE_APPEND);
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Obtiene las notificaciones enviadas a un alumno.
     *
     * Ejecuta una consulta en la tabla Notificacion_Alumno para obtener los mensajes y
     * la fecha de envío de las notificaciones dirigidas a un alumno, ordenados de forma descendente.
     *
     * @param string $idAlumno Identificador del alumno.
     * @return array Array asociativo con las notificaciones o un array vacío en caso de error.
     */
    public function getNotificacionesPorAlumno($idAlumno)
    {
        $sql = "SELECT n.Mensaje, n.Fecha_Envio AS Fecha
                FROM Notificacion_Alumno n
                WHERE n.ID_Alumno = ?
                ORDER BY n.Fecha_Envio DESC";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            file_put_contents('debug.log', "Error al preparar la consulta: " . $this->conn->error . "\n", FILE_APPEND);
            return [];
        }

        $stmt->bind_param("s", $idAlumno);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            file_put_contents('debug.log', "Error al obtener las notificaciones: " . $this->conn->error . "\n", FILE_APPEND);
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
