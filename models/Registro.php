<?php

class Registro {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerRegistroPorId($id_registro) {
        $sql = "SELECT * FROM Registros WHERE id_registro = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_registro);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function obtenerRegistrosPorEmpleado($id_empleado) {
        $sql = "SELECT * FROM Registros WHERE id_empleado = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_empleado);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function crearRegistro($id_empleado, $fecha, $entrada_manana, $salida_manana, $entrada_tarde, $salida_tarde, $horas_trabajadas) {
        $sql = "INSERT INTO Registros (id_empleado, fecha, entrada_manana, salida_manana, entrada_tarde, salida_tarde, horas_trabajadas) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isssssd", $id_empleado, $fecha, $entrada_manana, $salida_manana, $entrada_tarde, $salida_tarde, $horas_trabajadas);
        return $stmt->execute();
    }

    public function actualizarRegistro($id_registro, $entrada_manana, $salida_manana, $entrada_tarde, $salida_tarde, $horas_trabajadas) {
        $sql = "UPDATE Registros SET entrada_manana = ?, salida_manana = ?, entrada_tarde = ?, salida_tarde = ?, horas_trabajadas = ? WHERE id_registro = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssdi", $entrada_manana, $salida_manana, $entrada_tarde, $salida_tarde, $horas_trabajadas, $id_registro);
        return $stmt->execute();
    }

    public function eliminarRegistro($id_registro) {
        $sql = "DELETE FROM Registros WHERE id_registro = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_registro);
        return $stmt->execute();
    }
}

?>