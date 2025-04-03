<?php

class Vacaciones {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerVacacionesPorId($id_vacaciones) {
        $sql = "SELECT * FROM Vacaciones WHERE id_vacaciones = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_vacaciones);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function obtenerVacacionesPorEmpleado($id_empleado) {
        $sql = "SELECT * FROM Vacaciones WHERE id_empleado = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_empleado);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function crearVacaciones($id_empleado, $fecha_inicio, $fecha_fin) {
        $sql = "INSERT INTO Vacaciones (id_empleado, fecha_inicio, fecha_fin) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $id_empleado, $fecha_inicio, $fecha_fin);
        return $stmt->execute();
    }

    public function actualizarEstadoVacaciones($id_vacaciones, $estado) {
        $sql = "UPDATE Vacaciones SET estado = ? WHERE id_vacaciones = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $estado, $id_vacaciones);
        return $stmt->execute();
    }

    public function eliminarVacaciones($id_vacaciones) {
        $sql = "DELETE FROM Vacaciones WHERE id_vacaciones = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_vacaciones);
        return $stmt->execute();
    }
}

?>