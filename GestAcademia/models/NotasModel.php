<?php
/**
 * Modelo para la gestión de notas, alumnos y cursos.
 *
 * Este modelo se encarga de obtener, insertar y actualizar las notas de los alumnos
 * en relación a cursos, módulos y unidades formativas, así como de obtener la información
 * de los alumnos matriculados en un curso.
 *
 * @package YourPackageName
 */
class NotasModel
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
     */
    public function __construct()
    {
        $this->conn = include __DIR__ . '/../config/conexion.php';
    }

    /**
     * Obtiene los alumnos matriculados en un curso.
     *
     * Realiza una consulta para obtener los datos (ID, Nombre y Apellidos) de los alumnos
     * que se encuentran matriculados en el curso indicado.
     *
     * @param string $idCurso Identificador del curso.
     * @return array|bool Array de alumnos en formato asociativo o false en caso de error.
     */
    public function getAlumnosPorCurso($idCurso)
    {
        $sql = "SELECT a.ID_Alumno, a.Nombre, a.Apellido1, a.Apellido2 
            FROM Alumno_Curso ac 
            INNER JOIN Alumno a ON ac.ID_Alumno = a.ID_Alumno 
            WHERE ac.ID_Curso = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("❌ Error en la consulta SQL: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("s", $idCurso);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            error_log("❌ Error en la obtención de datos: " . $this->conn->error);
            return false;
        }

        $alumnos = $result->fetch_all(MYSQLI_ASSOC);

        // ✅ Depuración: Registrar en log
        error_log("✅ Alumnos obtenidos correctamente: " . json_encode($alumnos));

        return $alumnos ?: []; // Retorna un array vacío si no hay alumnos
    }

    /**
     * Obtiene las notas de un alumno en un curso.
     *
     * Realiza una consulta para obtener las notas asociadas a un alumno y curso específico.
     * Se utiliza COALESCE para mostrar el nombre del módulo o de la unidad formativa.
     *
     * @param string $idAlumno Identificador del alumno.
     * @param string $idCurso Identificador del curso.
     * @return array|bool Array de notas en formato asociativo o false en caso de error.
     */
    public function getNotasPorAlumno($idAlumno, $idCurso)
    {
        $sql = "SELECT 
            n.ID_Nota, 
            n.Tipo_Nota, 
            COALESCE(m.Nombre, uf.Nombre) AS Nombre, 
            n.Calificación,
            n.ID_Unidad_Formativa,  
            n.ID_Modulo            
        FROM Nota n
        LEFT JOIN Modulo m ON n.ID_Modulo = m.ID_Modulo
        LEFT JOIN Unidad_Formativa uf ON n.ID_Unidad_Formativa = uf.ID_Unidad_Formativa
        WHERE n.ID_Alumno = ? AND n.ID_Curso = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("❌ Error en la consulta SQL: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("ss", $idAlumno, $idCurso);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            error_log("❌ Error al obtener resultados: " . $this->conn->error);
            return false;
        }

        $notas = $result->fetch_all(MYSQLI_ASSOC);

        // ✅ Asegurar que no retorna NULL, sino un array vacío
        return $notas ?: [];
    }

    /**
     * Inserta una nota en la base de datos.
     *
     * Registra una nueva nota para un alumno en un curso, especificando a qué módulo o
     * unidad formativa pertenece la nota, el tipo de nota y la calificación obtenida.
     * La fecha de registro se establece como la fecha actual.
     *
     * @param string $id_alumno Identificador del alumno.
     * @param string $id_curso Identificador del curso.
     * @param string $id_modulo Identificador del módulo.
     * @param string $id_unidad_formativa Identificador de la unidad formativa.
     * @param string $tipo_nota Tipo de nota ('Unidad_Formativa', 'Modulo', 'Curso', etc.).
     * @param float $calificacion Calificación obtenida.
     * @return bool True en caso de éxito, false en caso de error.
     */
    public function insertNota($id_alumno, $id_curso, $id_modulo, $id_unidad_formativa, $tipo_nota, $calificacion)
    {
        $sql = "INSERT INTO Nota (ID_Alumno, ID_Curso, ID_Modulo, ID_Unidad_Formativa, Tipo_Nota, Calificación, Fecha_Registro) 
                VALUES (?, ?, ?, ?, ?, ?, CURDATE())";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("❌ Error en la consulta SQL: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("sssssd", $id_alumno, $id_curso, $id_modulo, $id_unidad_formativa, $tipo_nota, $calificacion);
        return $stmt->execute();
    }

    /**
     * Actualiza las notas de módulos y cursos según las unidades formativas registradas.
     *
     * Calcula y actualiza la calificación promedio de las notas de tipo 'Unidad_Formativa' para
     * actualizar la nota de cada módulo y, posteriormente, la nota promedio de los módulos para
     * actualizar la nota final del curso.
     *
     * @param string $id_alumno Identificador del alumno.
     * @param string $id_curso Identificador del curso.
     * @return void
     */
    public function actualizarNotas($id_alumno, $id_curso)
    {
        // Actualizar la nota de los módulos
        $sql_modulo = "UPDATE Nota n_mod
            JOIN (SELECT ID_Modulo, ID_Alumno, AVG(Calificación) AS Media
                  FROM Nota WHERE Tipo_Nota = 'Unidad_Formativa'
                  GROUP BY ID_Modulo, ID_Alumno) AS calculo
            ON n_mod.ID_Modulo = calculo.ID_Modulo 
            AND n_mod.ID_Alumno = calculo.ID_Alumno
            SET n_mod.Calificación = calculo.Media
            WHERE n_mod.Tipo_Nota = 'Modulo'";

        if (!$this->conn->query($sql_modulo)) {
            error_log("❌ Error al actualizar notas de módulo: " . $this->conn->error);
        }

        // Actualizar la nota del curso
        $sql_curso = "UPDATE Nota n_curso
            JOIN (SELECT ID_Curso, ID_Alumno, AVG(Calificación) AS Media
                  FROM Nota WHERE Tipo_Nota = 'Modulo'
                  GROUP BY ID_Curso, ID_Alumno) AS calculo
            ON n_curso.ID_Curso = calculo.ID_Curso 
            AND n_curso.ID_Alumno = calculo.ID_Alumno
            SET n_curso.Calificación = calculo.Media
            WHERE n_curso.Tipo_Nota = 'Curso'";

        if (!$this->conn->query($sql_curso)) {
            error_log("❌ Error al actualizar notas de curso: " . $this->conn->error);
        }
    }

    /**
     * Actualiza la calificación de una nota existente.
     *
     * Dependiendo del tipo de nota (Unidad_Formativa o Modulo), actualiza la calificación
     * utilizando el identificador correspondiente.
     *
     * @param string $id_alumno Identificador del alumno.
     * @param string $id_curso Identificador del curso.
     * @param string $tipo_nota Tipo de nota ('Unidad_Formativa' o 'Modulo').
     * @param float $calificacion Nueva calificación.
     * @param string|null $id_unidad_formativa Identificador de la unidad formativa (opcional).
     * @param string|null $id_modulo Identificador del módulo (opcional).
     * @return bool True en caso de éxito, false si los datos son insuficientes o en caso de error.
     */
    public function updateNota($id_alumno, $id_curso, $tipo_nota, $calificacion, $id_unidad_formativa = null, $id_modulo = null)
    {
        // Se asume que para unidades formativas se usa el ID_Unidad_Formativa, y para módulos el ID_Modulo
        if ($tipo_nota === 'Unidad_Formativa' && $id_unidad_formativa) {
            $sql = "UPDATE Nota SET Calificación = ? 
                WHERE ID_Alumno = ? AND ID_Curso = ? AND Tipo_Nota = 'Unidad_Formativa' AND ID_Unidad_Formativa = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("❌ Error en la consulta SQL: " . $this->conn->error);
                return false;
            }
            $stmt->bind_param("dsss", $calificacion, $id_alumno, $id_curso, $id_unidad_formativa);
        } elseif ($tipo_nota === 'Modulo' && $id_modulo) {
            $sql = "UPDATE Nota SET Calificación = ? 
                WHERE ID_Alumno = ? AND ID_Curso = ? AND Tipo_Nota = 'Modulo' AND ID_Modulo = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("❌ Error en la consulta SQL: " . $this->conn->error);
                return false;
            }
            $stmt->bind_param("dsss", $calificacion, $id_alumno, $id_curso, $id_modulo);
        } else {
            error_log("❌ Datos insuficientes para actualizar la nota.");
            return false;
        }

        return $stmt->execute();
    }

    /**
     * Obtiene los módulos y unidades formativas asociados a un curso.
     *
     * Realiza una consulta que une dos conjuntos de resultados mediante UNION ALL:
     * - Los módulos asociados al curso.
     * - Las unidades formativas asociadas a cada módulo del curso.
     *
     * @param string $idCurso Identificador del curso.
     * @return array Array de registros que incluyen ID, Nombre, Tipo ('Modulo' o 'Unidad_Formativa')
     *               y, en el caso de unidades formativas, el ID del módulo asociado.
     */
    public function getModulosUnidadesPorCurso($idCurso)
    {
        $sql = "
            SELECT m.ID_Modulo AS ID, m.Nombre, 'Modulo' AS Tipo, NULL AS ID_Modulo
            FROM Modulo m
            JOIN Curso_Modulo cm ON m.ID_Modulo = cm.ID_Modulo
            WHERE cm.ID_Curso = ?
    
            UNION ALL
    
            SELECT uf.ID_Unidad_Formativa AS ID, uf.Nombre, 'Unidad_Formativa' AS Tipo, uf.ID_Modulo
            FROM Unidad_Formativa uf
            JOIN Modulo m ON uf.ID_Modulo = m.ID_Modulo
            JOIN Curso_Modulo cm ON m.ID_Modulo = cm.ID_Modulo
            WHERE cm.ID_Curso = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $idCurso, $idCurso);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtiene todas las notas registradas de un alumno.
     *
     * Realiza una consulta para obtener todas las notas de un alumno, incluyendo información
     * sobre el módulo, unidad formativa y curso, ordenadas por fecha de registro descendente.
     *
     * @param string $idAlumno Identificador del alumno.
     * @return array|bool Array de notas en formato asociativo o false en caso de error.
     */
    public function obtenerTodasLasNotasPorAlumno($idAlumno)
    {
        $sql = "
            SELECT 
                n.ID_Nota,
                n.Tipo_Nota,
                COALESCE(m.Nombre, uf.Nombre) AS Nombre,
                n.Calificación,
                n.Fecha_Registro,
                c.Nombre AS Curso
            FROM Nota n
            LEFT JOIN Modulo m ON n.ID_Modulo = m.ID_Modulo
            LEFT JOIN Unidad_Formativa uf ON n.ID_Unidad_Formativa = uf.ID_Unidad_Formativa
            LEFT JOIN Curso c ON n.ID_Curso = c.ID_Curso
            WHERE n.ID_Alumno = ?
            ORDER BY n.Fecha_Registro DESC
        ";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("❌ Error en la consulta SQL: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("s", $idAlumno);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC) ?: [];
    }

    /**
     * Obtiene las notas de un alumno para un curso específico.
     *
     * Realiza una consulta que une varias tablas para obtener información detallada sobre las notas
     * de un alumno, incluyendo datos del alumno, curso, módulo y unidad formativa.
     *
     * @param string $idAlumno Identificador del alumno.
     * @param string $idCurso Identificador del curso.
     * @return array|bool Array de resultados en formato asociativo o false en caso de error.
     */
    public function obtenerNotasPorAlumnoYCurso($idAlumno, $idCurso)
    {
        $sql = "
           SELECT 
                a.ID_Alumno,
                a.Nombre AS AlumnoNombre,
                a.Apellido1,
                a.Apellido2,
                c.Nombre AS CursoNombre,
                n.ID_Modulo,
                m.Nombre AS ModuloNombre,
                n.ID_Unidad_Formativa,
                uf.Nombre AS UnidadFormativaNombre,
                n.Calificación AS Nota
            FROM Nota n
            INNER JOIN Alumno a ON n.ID_Alumno = a.ID_Alumno
            INNER JOIN Curso c ON n.ID_Curso = c.ID_Curso
            LEFT JOIN Modulo m ON n.ID_Modulo = m.ID_Modulo
            LEFT JOIN Unidad_Formativa uf ON n.ID_Unidad_Formativa = uf.ID_Unidad_Formativa
            WHERE a.ID_Alumno = ? AND c.ID_Curso = ?;
        ";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("❌ Error en la consulta SQL: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("ss", $idAlumno, $idCurso);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC) ?: [];
    }
}
