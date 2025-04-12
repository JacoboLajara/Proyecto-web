<?php

class DiasLibres {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerDiaLibrePorId($id_dia_libre) {
        $sql = "SELECT * FROM Dias_libres WHERE id_dia_libre = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_dia_libre);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function obtenerDiasLibresPorEmpleado($id_empleado) {
        $sql = "SELECT * FROM Dias_libres WHERE id_empleado = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_empleado);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function crearDiaLibre($id_empleado, $fecha, $motivo) {
        $sql = "INSERT INTO Dias_libres (id_empleado, fecha, motivo) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $id_empleado, $fecha, $motivo);
        return $stmt->execute();
    }

    public function actualizarEstadoDiaLibre($id_dia_libre, $estado) {
        $sql = "UPDATE Dias_libres SET estado = ? WHERE id_dia_libre = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $estado, $id_dia_libre);
        return $stmt->execute();
    }

    public function eliminarDiaLibre($id_dia_libre) {
        $sql = "DELETE FROM Dias_libres WHERE id_dia_libre = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_dia_libre);
        return $stmt->execute();
    }
}

?>