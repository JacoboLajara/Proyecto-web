<?php
require_once __DIR__ . '/../config/conexion.php';

class EdicionCursosModel {
    private $conn;

    public function __construct() {
        $this->conn = include __DIR__ . '/../config/conexion.php';
        if (!$this->conn) {
            die("Error: No se pudo conectar a la base de datos.");
        }
    }

    public function getEdicionesPorCurso($idCurso) {
        $query = "SELECT * FROM Edicion_Curso WHERE ID_Curso = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$idCurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearEdicion($idCurso, $fechaInicio, $fechaFin, $estado) {
        $query = "INSERT INTO Edicion_Curso (ID_Curso, Fecha_Inicio, Fecha_Fin, Estado) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$idCurso, $fechaInicio, $fechaFin, $estado]);
    }

    public function actualizarEdicion($idEdicion, $fechaInicio, $fechaFin, $estado) {
        $query = "UPDATE Edicion_Curso SET Fecha_Inicio = ?, Fecha_Fin = ?, Estado = ? WHERE ID_Edicion = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$fechaInicio, $fechaFin, $estado, $idEdicion]);
    }

    public function eliminarEdicion($idEdicion) {
        $query = "DELETE FROM Edicion_Curso WHERE ID_Edicion = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$idEdicion]);
    }
}
?>
