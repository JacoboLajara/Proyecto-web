<?php
class RegistroHorarioModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = include __DIR__ . '/../config/conexion.php';
    }

    public function insertarRegistro($datos)
    {
        $sql = "INSERT INTO Registro_Horario (
                    ID_Usuario, Fecha,
                    Hora_Entrada_Mañana, Hora_Salida_Mañana,
                    Hora_Entrada_Tarde, Hora_Salida_Tarde,
                    Tipo_Jornada, Tipo_Dia,
                    Observaciones, Justificante_URL
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("❌ Error al preparar la consulta: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param(
            "isssssssss",
            $datos['ID_Usuario'],
            $datos['Fecha'],
            $datos['Hora_Entrada_Mañana'],
            $datos['Hora_Salida_Mañana'],
            $datos['Hora_Entrada_Tarde'],
            $datos['Hora_Salida_Tarde'],
            $datos['Tipo_Jornada'],
            $datos['Tipo_Dia'],
            $datos['Observaciones'],
            $datos['Justificante_URL']
        );

        return $stmt->execute();
    }

    public function obtenerPorUsuarioYFecha($idUsuario, $fecha)
    {
        $sql = "SELECT * FROM Registro_Horario WHERE ID_Usuario = ? AND Fecha = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $idUsuario, $fecha);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerPorUsuarioYRango($idUsuario, $inicio, $fin)
    {
        $sql = "SELECT * FROM Registro_Horario WHERE ID_Usuario = ? AND Fecha BETWEEN ? AND ? ORDER BY Fecha";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $idUsuario, $inicio, $fin);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerTodosPorRango($inicio, $fin)
    {
        $sql = "SELECT * FROM Registro_Horario WHERE Fecha BETWEEN ? AND ? ORDER BY Fecha, ID_Usuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $inicio, $fin);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
