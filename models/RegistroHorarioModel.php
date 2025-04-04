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
        // Asegurar que todos los campos existen (si no, null)
        $datos = array_merge([
            'Hora_Entrada_Mañana' => null,
            'Hora_Salida_Mañana' => null,
            'Hora_Entrada_Tarde' => null,
            'Hora_Salida_Tarde' => null,
            'Tipo_Jornada' => null,
            'Tipo_Dia' => null,
            'Observaciones' => null,
            'Justificante_URL' => null
        ], $datos);
    
        $sql = "INSERT INTO Registro_Horario (
                    ID_Usuario, Fecha,
                    Hora_Entrada_Mañana, Hora_Salida_Mañana,
                    Hora_Entrada_Tarde, Hora_Salida_Tarde,
                    Tipo_Jornada, Tipo_Dia,
                    Observaciones, Justificante_URL
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            file_put_contents('debug.log', "❌ Error prepare(): " . $this->conn->error . "\n", FILE_APPEND);
            return false;
        }
    
        // Asegúrate de que todos los campos son cadenas de texto (ID incluido si es DNI)
        $stmt->bind_param(
            "ssssssssss",
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
    
        $resultado = $stmt->execute();
    
        if (!$resultado) {
            file_put_contents('debug.log', "❌ Error execute(): " . $stmt->error . "\n", FILE_APPEND);
        } else {
            file_put_contents('debug.log', "✅ Registro insertado correctamente\n", FILE_APPEND);
        }
    
        return $resultado;
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
