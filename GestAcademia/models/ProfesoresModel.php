<?php

/**
 * Clase ProfesoresModel
 * 
 * Modelo para gestionar las operaciones relacionadas con los profesores en la base de datos.
 */
class ProfesoresModel
{
    /**
     * @var mysqli $conn Conexión a la base de datos
     */
    private $conn;

    /**
     * Constructor de la clase.
     * Inicializa la conexión a la base de datos.
     */
    public function __construct()
    {
        $this->conn = include __DIR__ . '/../config/conexion.php';

        if (!$this->conn) {
            die("Error: La conexión no se pudo establecer correctamente.");
        }
    }

    /**
     * Verifica si un profesor existe en la base de datos.
     *
     * @param string $id_profesor ID del profesor a verificar.
     * @return bool True si el profesor existe, False si no.
     */
    public function existeProfesor($id_profesor)
    {
        $sql = "SELECT COUNT(*) as total FROM profesor WHERE ID_Profesor = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $id_profesor);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] > 0;
    }

    /**
     * Inserta un nuevo profesor en la base de datos.
     *
     * @param string $id_profesor ID del profesor.
     * @param int $id_usuario ID del usuario asociado.
     * @param string $nombre Nombre del profesor.
     * @param string $apellido1 Primer apellido.
     * @param string $apellido2 Segundo apellido.
     * @param string $direccion Dirección del profesor.
     * @param string $poblacion Población del profesor.
     * @param string $cpostal Código postal.
     * @param string $fechanac Fecha de nacimiento (YYYY-MM-DD).
     * @param string $estudios Nivel de estudios.
     * @param string $fechalta Fecha de alta en el sistema.
     * @param string|null $fechabaja Fecha de baja (puede ser null).
     * @param string $Phone Número de teléfono.
     * @param string $mail Correo electrónico.
     * @param string $provincia Provincia del profesor.
     * @param string $especialidad Especialidad del profesor.
     * @return bool True si la inserción fue exitosa, False en caso de error.
     */
    public function insertProfesor(
        $id_profesor,
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
        $provincia,
        $especialidad
    ) {
        $sql = "INSERT INTO profesor (ID_Profesor, ID_Usuario, Nombre, Apellido1, Apellido2, Direccion, 
                Poblacion, Codigo_Postal, Fecha_Nacimiento, Nivel_Estudios, Fecha_Alta, Fecha_Baja, 
                Telefono, Email, Provincia, Especialidad) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        if (!$this->conn) {
            die("Conexión a la base de datos no establecida correctamente.");
        }

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $this->conn->error);
        }

        $fechabaja = empty($fechabaja) ? null : $fechabaja;

        $stmt->bind_param(
            "sissssssssssssss",
            $id_profesor,
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
            $provincia,
            $especialidad
        );

        return $stmt->execute();
    }

    /**
     * Actualiza los datos de un profesor en la base de datos.
     *
     * @param string $id_profesor ID del profesor.
     * @param string $nombre Nombre del profesor.
     * @param string $apellido1 Primer apellido.
     * @param string $apellido2 Segundo apellido.
     * @param string $direccion Dirección del profesor.
     * @param string $poblacion Población.
     * @param string $cpostal Código postal.
     * @param string $fechanac Fecha de nacimiento.
     * @param string $estudios Nivel de estudios.
     * @param string $fechalta Fecha de alta.
     * @param string|null $fechabaja Fecha de baja (puede ser null).
     * @param string $Phone Número de teléfono.
     * @param string $mail Correo electrónico.
     * @param string $provincia Provincia.
     * @param string $especialidad Especialidad del profesor.
     * @return bool True si la actualización fue exitosa, False en caso de error.
     */
    public function updateProfesor(
        $id_profesor,
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
        $especialidad
    ) {
        $sql = "UPDATE profesor 
            SET Nombre = ?, Apellido1 = ?, Apellido2 = ?, Direccion = ?, Poblacion = ?, 
                Codigo_Postal = ?, Fecha_Nacimiento = ?, Nivel_Estudios = ?, Fecha_Alta = ?, 
                Fecha_Baja = ?, Telefono = ?, Email = ?, Provincia = ?, Especialidad = ?
            WHERE ID_Profesor = ?";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $this->conn->error);
        }

        $stmt->bind_param(
            "sssssssssssssss",
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
            $especialidad,
            $id_profesor
        );

        return $stmt->execute();
    }

    /**
     * Obtiene la lista de todos los profesores.
     *
     * @return array Lista de profesores como un array asociativo.
     */
    public function getProfesores()
    {
        $query = $this->conn->prepare("SELECT * FROM profesor");
        $query->execute();
        $result = $query->get_result();

        $profesores = [];
        while ($row = $result->fetch_assoc()) {
            $profesores[] = $row;
        }

        return $profesores;
    }

    /**
     * Obtiene los detalles de un profesor específico por su ID.
     *
     * @param string $idProfesor ID del profesor.
     * @return array|null Datos del profesor o null si no se encuentra.
     */
    public function getProfesorDetalle($idProfesor)
    {
        $sql = "SELECT * FROM Profesor WHERE ID_Profesor = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $idProfesor);
        $stmt->execute();
        $result = $stmt->get_result();
        $datosProfesor = $result->fetch_assoc();

        return $datosProfesor ? ["profesor" => $datosProfesor] : null;
    }
}
