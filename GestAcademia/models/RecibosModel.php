<?php
// models/RecibosModel.php

require_once __DIR__ . '/../config/conexion.php';

/**
 * Modelo para la gestión de recibos.
 *
 * Esta clase se encarga de interactuar con la base de datos para obtener, filtrar y actualizar
 * los recibos asociados a alumnos y cursos.
 *
 * @package YourPackageName
 */
class RecibosModel
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
     * Inicializa la conexión a la base de datos utilizando la variable global $conn definida
     * en el archivo de conexión. Si la conexión no se encuentra, termina la ejecución del script.
     */
    public function __construct()
    {
        global $conn; // Se asume que config/conexion.php define $conn
        $this->conn = $conn;
    }

    /**
     * Obtiene los recibos filtrados según los criterios.
     *
     * Permite filtrar recibos utilizando parámetros opcionales: búsqueda parcial por apellido,
     * año y mes de emisión, ID del curso y, opcionalmente, solo aquellos recibos en estado 'Pendiente'.
     *
     * @param string $apellido1 Búsqueda parcial por el primer apellido del alumno.
     * @param string $anio Año para filtrar la Fecha_Emision (se evalúa con YEAR()).
     * @param string $mes Mes para filtrar la Fecha_Emision (se evalúa con MONTH()).
     * @param string $curso Filtrado por el ID del curso.
     * @param bool $pendientes Si es true, se filtran solo los recibos en estado 'Pendiente'.
     * @return array Arreglo asociativo con los recibos que cumplen los criterios.
     */
    public function getRecibos($apellido1, $anio, $mes, $curso, $pendientes)
    {
        // Construcción de la consulta base
        $query = "SELECT r.ID_Recibo, a.Nombre, a.Apellido1, a.Apellido2, 
                         c.Nombre AS Curso, c.Precio_Curso AS Importe,
                         r.Fecha_Pago, r.Fecha_Emision, r.Periodo, r.Estado
                  FROM Recibo r 
                  JOIN Alumno a ON r.ID_Alumno = a.ID_Alumno 
                  JOIN Curso c ON r.ID_Curso = c.ID_Curso
                  WHERE 1=1";
        $params = [];
        $types = "";

        // Agregar condición para el apellido si se proporcionó
        if (!empty($apellido1)) {
            $query .= " AND a.Apellido1 LIKE ?";
            $params[] = "%$apellido1%";
            $types .= "s";
        }
        // Agregar condición para el año de emisión
        if (!empty($anio)) {
            $query .= " AND YEAR(r.Fecha_Emision) = ?";
            $params[] = intval($anio);
            $types .= "i";
        }
        // Agregar condición para el mes de emisión
        if (!empty($mes)) {
            $query .= " AND MONTH(r.Fecha_Emision) = ?";
            $params[] = intval($mes);
            $types .= "i";
        }
        // Agregar condición para el curso
        if (!empty($curso)) {
            $query .= " AND c.ID_Curso = ?";
            $params[] = $curso;
            $types .= "s";
        }
        // Filtrar recibos pendientes, si se solicita
        if ($pendientes) {
            $query .= " AND r.Estado = 'Pendiente'";
        }

        // Registrar la consulta y parámetros para depuración
        error_log("QUERY: " . $query);
        error_log("PARAMS: " . print_r($params, true));

        $stmt = $this->conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $recibos = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $recibos;
    }

    /**
     * Actualiza un recibo marcándolo como pagado.
     *
     * Establece la fecha de pago al día actual (CURDATE()) y actualiza el estado a 'Cobrado'.
     *
     * @param int $id ID del recibo a actualizar.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizarPago($id)
    {
        $query = "UPDATE Recibo SET Fecha_Pago = CURDATE(), Estado = 'Cobrado' WHERE ID_Recibo = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt)
            return false;
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    /**
     * Actualiza un recibo con los datos recibidos.
     *
     * Permite actualizar el estado del recibo y la fecha de pago. Si la fecha de pago es null,
     * se establece la columna Fecha_Pago a NULL.
     *
     * @param int $id ID del recibo.
     * @param string $estado Nuevo estado del recibo ('Pendiente' o 'Cobrado').
     * @param string|null $fecha_pago Nueva fecha de pago (formato 'YYYY-MM-DD') o null para eliminar la fecha.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function updateRecibo($id, $estado, $fecha_pago)
    {
        if ($fecha_pago === null) {
            $query = "UPDATE Recibo SET Estado = ?, Fecha_Pago = NULL WHERE ID_Recibo = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt)
                return false;
            $stmt->bind_param("si", $estado, $id);
        } else {
            $query = "UPDATE Recibo SET Estado = ?, Fecha_Pago = ? WHERE ID_Recibo = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt)
                return false;
            $stmt->bind_param("ssi", $estado, $fecha_pago, $id);
        }
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    /**
     * Obtiene todos los recibos.
     *
     * Esta función prepara una consulta para obtener todos los recibos. Se utiliza un parámetro
     * llamado $pendientes, el cual se vincula mediante bind_param, aunque la consulta no contiene
     * un marcador de posición para este parámetro.
     *
     * @param string $pendientes Parámetro utilizado para filtrar (no se emplea en la consulta SQL).
     * @return array Array asociativo con todos los recibos.
     */
    /* public function getTodosRecibos($pendientes)
    {
        $stmt = $this->conn->prepare("SELECT r.ID_Recibo, a.Nombre, a.Apellido1, a.Apellido2, 
                         c.Nombre AS Curso, c.Precio_Curso AS Importe,
                         r.Fecha_Pago, r.Fecha_Emision, r.Periodo, r.Estado
                  FROM Recibo r 
                  JOIN Alumno a ON r.ID_Alumno = a.ID_Alumno 
                  JOIN Curso c ON r.ID_Curso = c.ID_Curso
                  WHERE 1=1");
        // Nota: Se vincula el parámetro $pendientes, aunque no se utiliza en la cláusula WHERE.
        $stmt->bind_param("s", $pendientes);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    } */

    public function getTodosRecibos()
    {
        $stmt = $this->conn->prepare("SELECT r.ID_Recibo, a.Nombre, a.Apellido1, a.Apellido2, 
                         c.Nombre AS Curso, c.Precio_Curso AS Importe,
                         r.Fecha_Pago, r.Fecha_Emision, r.Periodo, r.Estado
                  FROM Recibo r 
                  JOIN Alumno a ON r.ID_Alumno = a.ID_Alumno 
                  JOIN Curso c ON r.ID_Curso = c.ID_Curso");

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    /**
     * Obtiene los cursos disponibles para los recibos.
     *
     * Ejecuta una consulta para obtener el ID y el nombre de todos los cursos.
     *
     * @return array Array asociativo con los cursos.
     */
    public function getCursosRecibo()
    {
        $sql = "SELECT ID_Curso, Nombre FROM Curso";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $cursos = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $cursos;
    }
}
