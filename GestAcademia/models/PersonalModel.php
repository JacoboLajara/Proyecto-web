<?php

/**
 * Clase PersonalModel para gestionar las operaciones relacionadas con el personal no docente.
 */
class PersonalModel
{
    /**
     * @var mysqli Conexión a la base de datos.
     */
    private $conn;

    /**
     * Constructor de la clase PersonalModel.
     * Establece la conexión a la base de datos.
     */
    public function __construct()
    {
        // Incluir conexión a la base de datos
        $this->conn = include __DIR__ . '/../config/conexion.php';

        if (!$this->conn) {
            die("Error: La conexión no se pudo establecer correctamente.");
        }
    }

    /**
     * Comprueba si un empleado ya existe en la base de datos.
     *
     * @param string $id_personal Identificador único del personal.
     * @return bool Devuelve true si el personal existe, false en caso contrario.
     */
    public function existePersonal($id_personal)
    {
        $sql = "SELECT COUNT(*) as total FROM personal_no_docente WHERE ID_Personal = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $id_personal);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] > 0;
    }

    /**
     * Inserta un nuevo registro de personal en la base de datos.
     *
     * @param string $id_personal ID del personal.
     * @param int $id_usuario ID del usuario asociado.
     * @param string $nombre Nombre del personal.
     * @param string $apellido1 Primer apellido.
     * @param string $apellido2 Segundo apellido.
     * @param string $direccion Dirección del personal.
     * @param string $poblacion Ciudad de residencia.
     * @param string $cpostal Código postal.
     * @param string $fechanac Fecha de nacimiento (YYYY-MM-DD).
     * @param string $estudios Nivel de estudios.
     * @param string $fechalta Fecha de alta en la empresa (YYYY-MM-DD).
     * @param string|null $fechabaja Fecha de baja en la empresa (puede ser NULL).
     * @param string $Phone Número de teléfono.
     * @param string $mail Correo electrónico.
     * @param string $provincia Provincia de residencia.
     * @return bool Devuelve true si la inserción fue exitosa, false en caso de error.
     */
    public function insertPersonal(
        $id_personal,
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
        $sql = "INSERT INTO personal_no_docente (ID_Personal, ID_Usuario, Nombre, Apellido1, Apellido2, Direccion, 
            Poblacion, Codigo_Postal, Fecha_Nacimiento, Nivel_Estudios, Fecha_Alta, Fecha_Baja, Telefono, Email, Provincia) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if (!$this->conn) {
            die("Conexión a la base de datos no establecida correctamente.");
        }

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $this->conn->error);
        }

        $fechabaja = empty($fechabaja) ? null : $fechabaja;

        $stmt->bind_param(
            "sssssssssssssss",
            $id_personal,
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

        return $stmt->execute();
    }

    /**
     * Actualiza la información de un personal existente en la base de datos.
     *
     * @param string $id_personal_new Nuevo ID del personal (si aplica cambio de ID).
     * @param string $nombre Nuevo nombre.
     * @param string $apellido1 Nuevo primer apellido.
     * @param string $apellido2 Nuevo segundo apellido.
     * @param string $direccion Nueva dirección.
     * @param string $poblacion Nueva ciudad de residencia.
     * @param string $cpostal Nuevo código postal.
     * @param string $fechanac Nueva fecha de nacimiento.
     * @param string $estudios Nuevo nivel de estudios.
     * @param string $fechalta Nueva fecha de alta.
     * @param string|null $fechabaja Nueva fecha de baja (puede ser NULL).
     * @param string $Phone Nuevo número de teléfono.
     * @param string $mail Nuevo correo electrónico.
     * @param string $provincia Nueva provincia.
     * @param string $id_personal_old ID actual del personal a actualizar.
     * @return bool Devuelve true si la actualización fue exitosa, false en caso de error.
     */
    public function updatePersonal(
        $id_personal_new,
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
        $id_personal_old
    ) {
        $sql = "UPDATE personal_no_docente SET 
            ID_Personal = ?, Nombre = ?, Apellido1 = ?, Apellido2 = ?, Direccion = ?, Poblacion = ?, Codigo_Postal = ?, 
            Fecha_Nacimiento = ?, Nivel_Estudios = ?, Fecha_Alta = ?, Fecha_Baja = ?, Telefono = ?, Email = ?, Provincia = ?
            WHERE ID_Personal = ?";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $this->conn->error);
        }

        $stmt->bind_param(
            "sssssssssssssss",
            $id_personal_new,
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
            $id_personal_old
        );

        return $stmt->execute();
    }

    /**
     * Obtiene la lista de todos los empleados del personal no docente.
     *
     * @param string $status Estado opcional para filtrar los resultados.
     * @return array Devuelve un array con los registros encontrados.
     */
    public function getPersonal($status = 'all')
    {
        $query = $this->conn->prepare("SELECT * FROM personal_no_docente");
        $query->execute();
        $result = $query->get_result();

        $personal = [];
        while ($row = $result->fetch_assoc()) {
            $personal[] = $row;
        }
        $query->close();
        return $personal;
    }

    /**
     * Obtiene los detalles de un empleado en particular.
     *
     * @param string $idPersonal ID del empleado.
     * @return array|null Devuelve un array con los datos del empleado o null si no se encuentra.
     */
    public function getPersonalDetalle($idPersonal)
    {
        $sql = "SELECT * FROM personal_no_docente WHERE ID_Personal = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $idPersonal);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
