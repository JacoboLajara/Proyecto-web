<?php

class Empleado {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerEmpleadoPorId($id_empleado) {
        $sql = "SELECT * FROM Empleados WHERE id_empleado = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_empleado);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function obtenerEmpleados() {
        $sql = "SELECT * FROM Empleados";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function crearEmpleado($nombre, $apellido, $email, $departamento, $contraseña) {
        $sql = "INSERT INTO Empleados (nombre, apellido, email, departamento, contraseña) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssss", $nombre, $apellido, $email, $departamento, $contraseña);
        return $stmt->execute();
    }

    public function actualizarEmpleado($id_empleado, $nombre, $apellido, $email, $departamento, $contraseña) {
        $sql = "UPDATE Empleados SET nombre = ?, apellido = ?, email = ?, departamento = ?, contraseña = ? WHERE id_empleado = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssi", $nombre, $apellido, $email, $departamento, $contraseña, $id_empleado);
        return $stmt->execute();
    }

    public function eliminarEmpleado($id_empleado) {
        $sql = "DELETE FROM Empleados WHERE id_empleado = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_empleado);
        return $stmt->execute();
    }
}

?>