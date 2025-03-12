<?php

/**
 * Clase AlumnosModel
 * 
 * Maneja las operaciones relacionadas con los alumnos en la base de datos.
 */
class AlumnosModel
{
    /**
     * @var mysqli|null Conexión a la base de datos.
     */
    private $conn;

    /**
     * Constructor de la clase.
     * 
     * Establece la conexión a la base de datos.
     */
    public function __construct()
    {
        // Incluir conexión a la base de datos
        $this->conn = include __DIR__ . '/../config/conexion.php';

        // Verificar si la conexión fue establecida correctamente
        if (!$this->conn) {
            die("Error: La conexión no se pudo establecer correctamente.");
        }
    }

    /**
     * Verifica si un alumno existe en la base de datos.
     * 
     * @param string $id_alumno ID del alumno a verificar.
     * @return bool Devuelve true si el alumno existe, false en caso contrario.
     */
    public function existeAlumno($id_alumno)
    {
        $sql = "SELECT COUNT(*) as total FROM alumno WHERE ID_Alumno = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $id_alumno);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] > 0;
    }

    /**
     * Inserta un nuevo alumno en la base de datos.
     * 
     * @param string $id_alumno ID del alumno.
     * @param int $id_usuario ID del usuario asociado.
     * @param string $nombre Nombre del alumno.
     * @param string $apellido1 Primer apellido del alumno.
     * @param string $apellido2 Segundo apellido del alumno.
     * @param string $direccion Dirección del alumno.
     * @param string $poblacion Población del alumno.
     * @param string $cpostal Código postal.
     * @param string $fechanac Fecha de nacimiento en formato YYYY-MM-DD.
     * @param string $estudios Nivel de estudios.
     * @param string $fechalta Fecha de alta en el sistema.
     * @param string|null $fechabaja Fecha de baja (puede ser NULL).
     * @param string $Phone Teléfono de contacto.
     * @param string $mail Correo electrónico.
     * @param string $provincia Provincia del alumno.
     * @return bool Devuelve true si la inserción es exitosa, de lo contrario termina la ejecución.
     */
    public function insertAlumno(
        $id_alumno,
        $id_usuario,
        $nombre,
        $apellido1,
        $apellido2,
        $direccion,
        $poblacion,
        $cpostal,
        $fechanac,
        $estudios,
        $fechalta,
        $fechabaja,
        $Phone,
        $mail,
        $provincia
    ) {
        $sql = "INSERT INTO alumno (ID_Alumno,ID_Usuario,Nombre,Apellido1,Apellido2,Direccion,Poblacion,Codigo_Postal,Fecha_Nacimiento,Nivel_Estudios,
         Fecha_Alta,Fecha_Baja,Telefono,Email,Provincia) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        if (!$this->conn) {
            die("Conexión a la base de datos no establecida correctamente.");
        }

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $this->conn->error);
        }

        $stmt->bind_param(
            "sisssssssssssss",
            $id_alumno,
            $id_usuario,
            $nombre,
            $apellido1,
            $apellido2,
            $direccion,
            $poblacion,
            $cpostal,
            $fechanac,
            $estudios,
            $fechalta,
            $fechabaja,
            $Phone,
            $mail,
            $provincia
        );

        if ($stmt->execute()) {
            return true;
        } else {
            die("Error en la ejecución: " . $stmt->error);
        }
    }

    /**
     * Actualiza la información de un alumno en la base de datos.
     * 
     * @param string $id_alumno ID del alumno.
     * @param string $nombre Nombre del alumno.
     * @param string $apellido1 Primer apellido.
     * @param string $apellido2 Segundo apellido.
     * @param string $direccion Dirección.
     * @param string $poblacion Población.
     * @param string $cpostal Código postal.
     * @param string $fechanac Fecha de nacimiento.
     * @param string $estudios Nivel de estudios.
     * @param string $fechalta Fecha de alta.
     * @param string|null $fechabaja Fecha de baja.
     * @param string $Phone Teléfono.
     * @param string $mail Correo electrónico.
     * @param string $provincia Provincia.
     * @return bool Devuelve true si la actualización es exitosa.
     */
    public function updateAlumno(
        $id_alumno,
        $nombre,
        $apellido1,
        $apellido2,
        $direccion,
        $poblacion,
        $cpostal,
        $fechanac,
        $estudios,
        $fechalta,
        $fechabaja,
        $Phone,
        $mail,
        $provincia
    ) {
        $sql = "UPDATE alumno 
            SET Nombre = ?, Apellido1 = ?, Apellido2 = ?, Direccion = ?, Poblacion = ?, Codigo_Postal = ?, 
                Fecha_Nacimiento = ?, Nivel_Estudios = ?, Fecha_Alta = ?, Fecha_Baja = ?, Telefono = ?, Email = ?, Provincia = ?
            WHERE ID_Alumno = ?";

        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            die("Error al preparar la consulta: " . $this->conn->error);
        }

        $stmt->bind_param(
            "ssssssssssssss",
            $nombre,
            $apellido1,
            $apellido2,
            $direccion,
            $poblacion,
            $cpostal,
            $fechanac,
            $estudios,
            $fechalta,
            $fechabaja,
            $Phone,
            $mail,
            $provincia,
            $id_alumno
        );

        return $stmt->execute();
    }

    /**
     * Obtiene la lista de todos los alumnos registrados.
     * 
     * @return array Retorna un array con todos los alumnos.
     */
    public function getAlumnos()
    {
        $query = $this->conn->prepare("SELECT * FROM Alumno");
        $query->execute();
        $result = $query->get_result();

        $alumnos = [];
        while ($row = $result->fetch_assoc()) {
            $alumnos[] = $row;
        }
        $query->close();
        return $alumnos;
    }

    /**
     * Obtiene los detalles de un alumno junto con su historial de cursos.
     * 
     * @param string $idAlumno ID del alumno.
     * @return array|null Retorna un array con la información del alumno y sus cursos, o null si no existe.
     */
    public function getAlumnoDetalleConCursos($idAlumno)
    {
        if (!$this->conn) {
            die("Error: Conexión a la base de datos no establecida.");
        }

        $sqlAlumno = "SELECT ID_Alumno, Nombre, Apellido1, Apellido2, Direccion, Poblacion, Provincia, 
                             Codigo_Postal, Fecha_Nacimiento, Nivel_Estudios, Fecha_Alta, Fecha_Baja, Telefono, Email 
                      FROM Alumno WHERE ID_Alumno = ?";
        $stmtAlumno = $this->conn->prepare($sqlAlumno);
        if (!$stmtAlumno) {
            die("Error al preparar consulta de alumno: " . $this->conn->error);
        }

        $stmtAlumno->bind_param("s", $idAlumno);
        $stmtAlumno->execute();
        $resultAlumno = $stmtAlumno->get_result();
        $datosAlumno = $resultAlumno->fetch_assoc();
        $stmtAlumno->close();

        if (!$datosAlumno) {
            return null;
        }

        $sqlCursos = "SELECT c.ID_Curso, c.Nombre 
                      FROM Alumno_Curso ac
                      JOIN Curso c ON ac.ID_Curso = c.ID_Curso
                      WHERE ac.ID_Alumno = ?";
        $stmtCursos = $this->conn->prepare($sqlCursos);
        $stmtCursos->bind_param("s", $idAlumno);
        $stmtCursos->execute();
        $resultCursos = $stmtCursos->get_result();

        $cursos = [];
        while ($curso = $resultCursos->fetch_assoc()) {
            $cursos[] = $curso;
        }
        $stmtCursos->close();

        return ['alumno' => $datosAlumno, 'historial' => $cursos];
    }
}
