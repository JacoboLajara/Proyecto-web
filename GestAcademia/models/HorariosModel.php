<?php
/**
 * Modelo para la gestión de horarios.
 *
 * Este modelo permite obtener aulas, cursos y asignar horarios a un aula y curso específicos.
 * Además, valida que no existan solapamientos en las franjas horarias y permite obtener los horarios ocupados.
 *
 * @package YourPackageName
 */
class HorariosModel
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
     * Inicializa la conexión a la base de datos incluyendo el archivo de configuración.
     * Verifica que la conexión se establezca correctamente y configura el reporte de errores de MySQLi.
     */
    public function __construct()
    {
        $this->conn = include __DIR__ . '/../config/conexion.php';

        if (!$this->conn) {
            die("Error: La conexión no se pudo establecer correctamente.");
        }
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }

    /**
     * Obtiene la conexión a la base de datos.
     *
     * @return mysqli Conexión a la base de datos.
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Obtiene todas las aulas.
     *
     * Ejecuta una consulta para obtener el identificador y nombre de todas las aulas.
     *
     * @return array Array asociativo de aulas.
     */
    public function getAulas()
    {
        $sql = "SELECT ID_Aula, Nombre FROM Aula";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtiene todos los cursos.
     *
     * Ejecuta una consulta para obtener el identificador y nombre de todos los cursos.
     *
     * @return array Array asociativo de cursos.
     */
    public function getCursos()
    {
        $sql = "SELECT ID_Curso, Nombre FROM Curso";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Asigna un horario a un aula y curso específicos.
     *
     * Primero verifica si existe un solapamiento en la franja horaria indicada para el aula y día especificados.
     * Si se detecta solapamiento, devuelve un mensaje de error con la franja horaria ocupada y el curso asignado.
     * Si no hay solapamiento, inserta el nuevo horario en la base de datos.
     *
     * @param int    $aula         Identificador del aula.
     * @param string $curso        Identificador del curso.
     * @param string $dia          Día en el que se asigna el horario.
     * @param string $hora_inicio  Hora de inicio en formato HH:MM:SS.
     * @param string $hora_fin     Hora de fin en formato HH:MM:SS.
     * @return array Array con claves "success" (bool) y "message" (string) indicando el resultado de la operación.
     */
    public function asignarHorario($aula, $curso, $dia, $hora_inicio, $hora_fin)
    {
        // Verificar si hay solapamiento y obtener el horario ya asignado
        $query = "SELECT ah.Hora_Inicio, ah.Hora_Fin, c.Nombre AS Curso
                  FROM asignacion_horario ah
                  INNER JOIN curso c ON ah.ID_Curso = c.ID_Curso
                  WHERE ah.ID_Aula = ? AND ah.Dia = ? 
                  AND ((? BETWEEN ah.Hora_Inicio AND ah.Hora_Fin)
                  OR (? BETWEEN ah.Hora_Inicio AND ah.Hora_Fin)
                  OR (ah.Hora_Inicio BETWEEN ? AND ?)
                  OR (ah.Hora_Fin BETWEEN ? AND ?))";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isssssss", $aula, $dia, $hora_inicio, $hora_fin, $hora_inicio, $hora_fin, $hora_inicio, $hora_fin);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // Si hay solapamiento, devolver la franja horaria ya ocupada junto al curso asignado
        if ($row) {
            return [
                "success" => false,
                "message" => "❌ Error: La franja horaria de " . $row['Hora_Inicio'] . " a " . $row['Hora_Fin'] .
                    " ya está ocupada por el curso '" . $row['Curso'] . "'."
            ];
        }

        // Intentar insertar el horario si no hay solapamientos
        try {
            $query_insert = "INSERT INTO Asignacion_Horario (ID_Aula, ID_Curso, Dia, Hora_Inicio, Hora_Fin) 
                             VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $this->conn->prepare($query_insert);
            $stmt_insert->bind_param("issss", $aula, $curso, $dia, $hora_inicio, $hora_fin);
            $stmt_insert->execute();

            return ["success" => true, "message" => "✅ Horario asignado correctamente."];
        } catch (mysqli_sql_exception $e) {
            return ["success" => false, "message" => "❌ Error inesperado: " . $e->getMessage()];
        }
    }

    /**
     * Obtiene los horarios ocupados de un aula.
     *
     * Ejecuta una consulta para obtener los horarios asignados a un aula en particular,
     * ordenándolos por día y hora de inicio.
     *
     * @param int $aulaId Identificador del aula.
     * @return array Array asociativo de horarios ocupados.
     */
    public function getHorariosOcupados($aulaId)
    {
        $conn = $this->getConnection();

        $query = "SELECT Dia, 
                     Hora_Inicio, Hora_Fin, 
                     Tarde_Inicio, Tarde_Fin, 
                     c.Nombre AS Curso
              FROM asignacion_horario ah
              INNER JOIN curso c ON ah.ID_Curso = c.ID_Curso
              WHERE ah.ID_Aula = ?
              ORDER BY FIELD(Dia, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'), Hora_Inicio";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $aulaId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }


    /**
     * Obtiene los horarios de los cursos en los que el alumno está matriculado.
     * @param string $idAlumno ID del alumno.
     * @return array Lista de cursos con horarios.
     */
    public function obtenerHorariosPorAlumno($idAlumno)
    {
        $sql = "SELECT c.Nombre AS Curso, ah.Dia, ah.Hora_Inicio, ah.Hora_Fin, ah.Tarde_Inicio, ah.Tarde_Fin, a.Nombre AS Aula
            FROM alumno_curso ac
            JOIN curso c ON ac.ID_Curso = c.ID_Curso
            JOIN asignacion_horario ah ON ah.ID_Curso = c.ID_Curso
            JOIN aula a ON ah.ID_Aula = a.ID_Aula
            WHERE ac.ID_Alumno = ?
            ORDER BY FIELD(ah.Dia, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'), ah.Hora_Inicio";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $idAlumno);
        $stmt->execute();
        $result = $stmt->get_result();

        // Debug: Imprimir los resultados antes de devolverlos
        $horarios = $result->fetch_all(MYSQLI_ASSOC);


        return $horarios;
    }

    /**
     * Obtiene los horarios de un curso específico.
     *
     * @param int $cursoId ID del curso.
     * @return array Lista de horarios del curso.
     */
    /**
     * Obtiene los horarios de un curso específico en una aula específica.
     *
     * @param int $cursoId ID del curso.
     * @param int|null $aulaId ID del aula (opcional).
     * @return array Lista de horarios filtrados.
     */
    public function getHorariosPorCurso($cursoId, $aulaId = null)
    {
        $query = "SELECT ah.Dia, ah.Hora_Inicio, ah.Hora_Fin,ah.Tarde_Inicio, ah.Tarde_Fin, a.Nombre AS Aula, ah.ID_Aula
              FROM asignacion_horario ah
              JOIN aula a ON ah.ID_Aula = a.ID_Aula
              WHERE ah.ID_Curso = ?";

        // Si el aula se especifica, también filtramos por ella
        if ($aulaId) {
            $query .= " AND ah.ID_Aula = ?";
        }

        $query .= " ORDER BY FIELD(ah.Dia, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'), ah.Hora_Inicio";

        $stmt = $this->conn->prepare($query);

        if ($aulaId) {
            $stmt->bind_param("ii", $cursoId, $aulaId);
        } else {
            $stmt->bind_param("i", $cursoId);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
/**
     * Obtiene los horarios de los cursos en los que el alumno está matriculado.
     * @param string $idAlumno ID del alumno.
     * @return array Lista de cursos con horarios.
     */
    public function obtenerHorariosPorProfesor($idProfesor)
    {
        $sql = "SELECT c.Nombre AS Curso, ah.Dia, ah.Hora_Inicio, ah.Hora_Fin, ah.Tarde_Inicio, ah.Tarde_Fin, a.Nombre AS Aula
            FROM profesor_curso pc
            JOIN curso c ON pc.ID_Curso = c.ID_Curso
            JOIN asignacion_horario ah ON ah.ID_Curso = c.ID_Curso
            JOIN aula a ON ah.ID_Aula = a.ID_Aula
            WHERE pc.ID_Profesor = ?
            ORDER BY FIELD(ah.Dia, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'), ah.Hora_Inicio";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $idProfesor);
        $stmt->execute();
        $result = $stmt->get_result();

        // Debug: Imprimir los resultados antes de devolverlos
        $horarios = $result->fetch_all(MYSQLI_ASSOC);


        return $horarios;
    }


}
