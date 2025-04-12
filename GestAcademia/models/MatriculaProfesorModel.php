<?php
/**
 * Modelo para la gestión de matrículas.
 *
 * Este modelo se encarga de realizar operaciones relacionadas con las matrículas de alumnos en cursos.
 * Permite obtener cursos, alumnos, insertar matrículas, verificar si ya existe una matrícula y dar de baja a alumnos.
 *
 * @package YourPackageName
 */
class MatriculaProfesorModel
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
     * Si la conexión falla, se termina la ejecución del script.
     */
    public function __construct()
    {
        // Incluir conexión a la base de datos
        $this->conn = include __DIR__ . '/../config/conexion.php';
        //var_dump($this->conn);

        if (!$this->conn) {
            die("Error: La conexión no se pudo establecer correctamente.");
        }
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
     * Obtiene la lista de cursos.
     *
     * Ejecuta una consulta para obtener el identificador y el nombre de todos los cursos.
     *
     * @return array Array asociativo con los cursos.
     */
    public function getCursos()
    {
        $sql = "SELECT ID_Curso, Nombre FROM curso";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtiene la lista de alumnos.
     *
     * Ejecuta una consulta para obtener el identificador, nombre y apellidos de los alumnos.
     * Además, se incluye el ID_Alumno como DNI.
     *
     * @return array Array asociativo con los alumnos.
     */
    public function getProfesor()
    {
        $sql = "SELECT ID_Profesor, Nombre, Apellido1, Apellido2, ID_Profesor AS DNI FROM profesor";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Inserta una asgignación profesor/curso en la base de datos.
     *
     * Registra la asignación de un profesor en un curso, asignando la fecha actual y el estado 'Activo'.
     *
     * @param string $idProfesor Identificador del profesor.
     * @param string $idCurso Identificador del curso.
     * @return bool True en caso de éxito, false en caso de error.
     */
    public function insertAsignacion($idProfesor, $idCurso)
    {
        $stmt = $this->conn->prepare("INSERT INTO profesor_curso (ID_Profesor, ID_Curso, Fecha_Matricula, Estado) VALUES (?, ?, CURDATE(), 'Activo')");
        $stmt->bind_param("ss", $idProfesor, $idCurso);
        return $stmt->execute();
    }

    /**
     * Verifica si una matrícula ya existe.
     *
     * Realiza una consulta para contar cuántas matrículas existen para el alumno y curso especificados.
     *
     * @param string $idProfesor Identificador del profesor.
     * @param string $idCurso Identificador del curso.
     * @return bool True si la matrícula ya existe, false en caso contrario.
     */
    public function existeAsignacion($idProfesor, $idCurso)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM profesor_curso WHERE ID_Profesor = ? AND ID_Curso = ?");
        $stmt->bind_param("ss", $idProfesor, $idCurso);

        $count = 0; // Inicializamos la variable
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();

        return $count > 0;
    }

    /**
     * Obtiene los alumnos matriculados en un curso activo.
     *
     * Realiza una consulta que une las tablas Alumno y Alumno_Curso para obtener los datos de los alumnos
     * que están matriculados en el curso especificado y que tienen el estado 'Activo'.
     *
     * @param string $idCurso Identificador del curso.
     * @return array Array asociativo con los alumnos matriculados y la fecha de matrícula.
     */
    public function getProfesoresMatriculadosPorCurso($idCurso)
    {
        $stmt = $this->conn->prepare("SELECT p.ID_Profesor, p.Nombre, p.Apellido1, p.Apellido2, pc.Fecha_Matricula, c.Nombre as nombre_curso
                                  FROM profesor p 
                                  JOIN profesor_curso pc ON p.ID_Profesor = pc.ID_Profesor 
                                  JOIN curso c On c.ID_Curso=pc.ID_Curso
                                  WHERE pc.ID_Curso = ? AND pc.Estado = 'Activo'");
        $stmt->bind_param("s", $idCurso);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getNombreCurso($idCurso)
    {
        $stmt = $this->conn->prepare("SELECT Nombre FROM curso WHERE ID_Curso = ?");
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $this->conn->error);
        }

        $stmt->bind_param("s", $idCurso);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row['Nombre']; // Devuelve solo el valor de la columna
        } else {
            return null; // Retorna null si no se encuentra el curso
        }
    }



    /**
     * Da de baja a los alumnos matriculados en un curso.
     *
     * Actualiza el estado de la matrícula a 'Baja' para cada alumno especificado en el curso dado.
     *
     * @param string $idCurso Identificador del curso.
     * @param array $profesores Array de identificadores de profesores a dar de baja.
     * @return bool True si la operación fue exitosa.
     */
    public function darDeBajaAsignacion($idCurso, $profesores)
    {
        $stmt = $this->conn->prepare("UPDATE profesor_curso 
                                  SET Estado = 'Baja' 
                                  WHERE ID_Curso = ? AND ID_Profesor = ?");
        foreach ($profesores as $idProfesor) {
            $stmt->bind_param("ss", $idCurso, $idProfesor);
            $stmt->execute();
        }
        return true;
    }
}
