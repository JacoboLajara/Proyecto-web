<?php

/**
 * Clase AulasModel
 *
 * Maneja las operaciones CRUD relacionadas con la gestión de aulas en la base de datos.
 */
class AulasModel
{
    /**
     * @var mysqli|null $conn Conexión a la base de datos
     */
    private $conn;

    /**
     * Constructor de la clase.
     * Establece la conexión con la base de datos.
     */
    public function __construct()
    {
        // Incluir conexión a la base de datos
        $this->conn = include __DIR__ . '/../config/conexion.php';

        // Verificar la conexión
        if (!$this->conn) {
            die("Error: La conexión no se pudo establecer correctamente.");
        }
    }

    /**
     * Verifica si un aula existe en la base de datos.
     *
     * @param string $id_aula Identificador del aula.
     * @return bool Retorna `true` si el aula existe, `false` en caso contrario.
     */
    public function existeAula($id_aula)
    {
        $sql = "SELECT COUNT(*) as total FROM aula WHERE ID_Aula = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $id_aula);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] > 0;
    }

    /**
     * Inserta un nuevo aula en la base de datos.
     *
     * @param string $id_aula Identificador del aula.
     * @param string $nombre Nombre del aula.
     * @param int $capacidad Capacidad máxima del aula.
     * @return bool Retorna `true` si la inserción fue exitosa, `false` en caso contrario.
     */
    public function insertAulas($id_aula, $nombre, $capacidad)
    {
        $sql = "INSERT INTO aula (ID_Aula, Nombre, Capacidad) VALUES (?, ?, ?)";
        
        if (!$this->conn) {
            die("Conexión a la base de datos no establecida correctamente.");
        }

        // Preparar la consulta
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $this->conn->error);
        }

        // Vincular parámetros
        $stmt->bind_param("sss", $id_aula, $nombre, $capacidad);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            return true; // Inserción exitosa
        } else {
            die("Error en la ejecución: " . $stmt->error);
        }
    }

    /**
     * Actualiza los datos de un aula en la base de datos.
     *
     * @param string $id_aula Identificador del aula.
     * @param string $nombre Nombre del aula.
     * @param int $capacidad Capacidad máxima del aula.
     * @return bool Retorna `true` si la actualización fue exitosa, `false` en caso contrario.
     */
    public function updateAulas($id_aula, $nombre, $capacidad)
    {
        $sql = "UPDATE aula SET ID_Aula = ?, Nombre = ?, Capacidad = ? WHERE ID_Aula = ?";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $this->conn->error);
        }

        // Vincular parámetros
        $stmt->bind_param("ssss", $id_aula, $nombre, $capacidad, $id_aula);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            return true;
        } else {
            die("Error en la ejecución: " . $stmt->error);
        }
    }

    /**
     * Obtiene todas las aulas almacenadas en la base de datos.
     *
     * @return array Retorna un array con la lista de aulas.
     */
    public function getAulas()
    {
        $query = $this->conn->prepare("SELECT * FROM aula");
        $query->execute();
        $result = $query->get_result();

        $aulas = [];
        while ($row = $result->fetch_assoc()) {
            $aulas[] = $row;
        }
        $query->close();
        return $aulas;
    }
}
