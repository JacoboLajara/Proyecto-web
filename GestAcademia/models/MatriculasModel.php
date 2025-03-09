<?php
/**
 * Modelo para la gestión de matrículas.
 *
 * Este modelo se encarga de realizar operaciones relacionadas con las matrículas de alumnos en cursos.
 * Permite obtener cursos, alumnos, insertar matrículas, verificar si ya existe una matrícula y dar de baja a alumnos.
 *
 * @package YourPackageName
 */
class MatriculasModel
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
        $sql = "SELECT ID_Curso, Nombre FROM Curso";
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
    public function getAlumnos()
    {
        $sql = "SELECT ID_Alumno, Nombre, Apellido1, Apellido2, ID_Alumno AS DNI FROM Alumno";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Inserta una matrícula en la base de datos.
     *
     * Registra la matrícula de un alumno en un curso, asignando la fecha actual y el estado 'Activo'.
     *
     * @param string $idAlumno Identificador del alumno.
     * @param string $idCurso Identificador del curso.
     * @return bool True en caso de éxito, false en caso de error.
     */
    public function insertMatricula($idAlumno, $idCurso)
    {
        $stmt = $this->conn->prepare("INSERT INTO Alumno_Curso (ID_Alumno, ID_Curso, Fecha_Matricula, Estado) VALUES (?, ?, CURDATE(), 'Activo')");
        $stmt->bind_param("ss", $idAlumno, $idCurso);
        return $stmt->execute();
    }

    /**
     * Verifica si una matrícula ya existe.
     *
     * Realiza una consulta para contar cuántas matrículas existen para el alumno y curso especificados.
     *
     * @param string $idAlumno Identificador del alumno.
     * @param string $idCurso Identificador del curso.
     * @return bool True si la matrícula ya existe, false en caso contrario.
     */
    public function existeMatricula($idAlumno, $idCurso)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Alumno_Curso WHERE ID_Alumno = ? AND ID_Curso = ?");
        $stmt->bind_param("ss", $idAlumno, $idCurso);

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
    public function getAlumnosMatriculadosPorCurso($idCurso)
    {
        $stmt = $this->conn->prepare("SELECT a.ID_Alumno, a.Nombre, a.Apellido1, a.Apellido2, ac.Fecha_Matricula 
                                  FROM Alumno a 
                                  JOIN Alumno_Curso ac ON a.ID_Alumno = ac.ID_Alumno 
                                  WHERE ac.ID_Curso = ? AND ac.Estado = 'Activo'");
        $stmt->bind_param("s", $idCurso);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Da de baja a los alumnos matriculados en un curso.
     *
     * Actualiza el estado de la matrícula a 'Baja' para cada alumno especificado en el curso dado.
     *
     * @param string $idCurso Identificador del curso.
     * @param array $alumnos Array de identificadores de alumnos a dar de baja.
     * @return bool True si la operación fue exitosa.
     */
    public function darDeBajaAlumnos($idCurso, $alumnos)
    {
        $stmt = $this->conn->prepare("UPDATE Alumno_Curso 
                                  SET Estado = 'Baja' 
                                  WHERE ID_Curso = ? AND ID_Alumno = ?");
        foreach ($alumnos as $idAlumno) {
            $stmt->bind_param("ss", $idCurso, $idAlumno);
            $stmt->execute();
        }
        return true;
    }
}
