<?php
class RegistroHorarioModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = include __DIR__ . '/../config/conexion.php';
    }
    private function generarHashIntegridad($idUsuario, $fecha, $entradaM, $salidaM, $entradaT, $salidaT)
    {
        $entradaM = $entradaM ?: '--:--';
        $salidaM = $salidaM ?: '--:--';
        $entradaT = $entradaT ?: '--:--';
        $salidaT = $salidaT ?: '--:--';

        $cadena = $idUsuario . $fecha . $entradaM . $salidaM . $entradaT . $salidaT;
        return hash('sha256', $cadena);
    }
    public function insertarRegistro($datos)
    {
        // Asegurar todos los campos con valores por defecto
        $datos = array_merge([
            'Hora_Entrada_Manana' => null,
            'Hora_Salida_Manana' => null,
            'Hora_Entrada_Tarde' => null,
            'Hora_Salida_Tarde' => null,
            'Tipo_Jornada' => 'Completa',
            'Tipo_Dia' => 'Ordinario',
            'Observaciones' => '',
            'Justificante_URL' => null
        ], $datos);

        // Comprobar si ya existe registro para este usuario y fecha
        $sqlCheck = "SELECT * FROM Registro_Horario WHERE ID_Usuario = ? AND Fecha = ?";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->bind_param("ss", $datos['ID_Usuario'], $datos['Fecha']);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();

        if ($result->num_rows > 0) {
            $registro = $result->fetch_assoc();
            $idRegistro = $registro['ID_Registro'];

            $camposHorarios = [
                'Hora_Entrada_Manana',
                'Hora_Salida_Manana',
                'Hora_Entrada_Tarde',
                'Hora_Salida_Tarde'
            ];

            foreach ($camposHorarios as $campo) {
                if (!empty($datos[$campo])) {
                    $campoActualizar = $campo;
                    $valorActualizar = $datos[$campo];
                    $valorAnterior = array_key_exists($campo, $registro) ? ($registro[$campo] ?? 'NULL') : 'NULL';



                    if ((string) $valorAnterior !== (string) $valorActualizar) {
                        $exitoHistorial = $this->registrarHistorial(
                            $idRegistro,
                            $campoActualizar,
                            $valorAnterior,
                            $valorActualizar,
                            $_SESSION['usuario']
                        );

                        if (!$exitoHistorial) {
                            file_put_contents('debug.log', "⚠️ Fallo al insertar en Historial_Horario\n", FILE_APPEND);
                            // Pero no detenemos la ejecución
                        }
                    }

                    break;
                }
            }

            if (!isset($campoActualizar)) {
                file_put_contents('debug.log', "⚠️ Ningún campo de horario válido recibido para actualizar\n", FILE_APPEND);
                return false;
            }

            $sqlUpdate = "UPDATE Registro_Horario SET $campoActualizar = ? WHERE ID_Registro = ?";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("si", $valorActualizar, $idRegistro);

            $resultado = $stmtUpdate->execute();
            if (!$resultado) {
                file_put_contents('debug.log', "❌ Error actualizando $campoActualizar: " . $stmtUpdate->error . "\n", FILE_APPEND);
            } else {
                file_put_contents('debug.log', "✅ $campoActualizar actualizado a $valorActualizar para ID_Registro $idRegistro\n", FILE_APPEND);
            }

            return $resultado;
        }

        // No existe: hacer INSERT
        $sqlInsert = "INSERT INTO Registro_Horario (
            ID_Usuario, Fecha,
            Hora_Entrada_Manana, Hora_Salida_Manana,
            Hora_Entrada_Tarde, Hora_Salida_Tarde,
            Tipo_Jornada, Tipo_Dia,
            Observaciones, Justificante_URL
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmtInsert = $this->conn->prepare($sqlInsert);
        if (!$stmtInsert) {
            file_put_contents('debug.log', "❌ Error prepare INSERT: " . $this->conn->error . "\n", FILE_APPEND);
            return false;
        }

        $stmtInsert->bind_param(
            "ssssssssss",
            $datos['ID_Usuario'],
            $datos['Fecha'],
            $datos['Hora_Entrada_Manana'],
            $datos['Hora_Salida_Manana'],
            $datos['Hora_Entrada_Tarde'],
            $datos['Hora_Salida_Tarde'],
            $datos['Tipo_Jornada'],
            $datos['Tipo_Dia'],
            $datos['Observaciones'],
            $datos['Justificante_URL']
        );

        $resultado = $stmtInsert->execute();

        if (!$resultado) {
            file_put_contents('debug.log', "❌ Error en INSERT: " . $stmtInsert->error . "\n", FILE_APPEND);
        } else {
            file_put_contents('debug.log', "✅ Registro insertado para {$datos['ID_Usuario']} el {$datos['Fecha']}\n", FILE_APPEND);
        }

        return $resultado;
    }


    private function registrarHistorial($idRegistro, $campo, $valorAnterior, $valorNuevo, $usuarioModificador)
    {
        file_put_contents('debug.log', "🛠 registrarHistorial() con: ID=$idRegistro, Campo=$campo, Anterior=$valorAnterior, Nuevo=$valorNuevo, Usuario=$usuarioModificador\n", FILE_APPEND);

        $sql = "INSERT INTO Historial_Horario (ID_Registro, Campo_Modificado, Valor_Anterior, Valor_Nuevo, Modificado_Por)
            VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            file_put_contents('debug.log', "❌ Error prepare(): " . $this->conn->error . "\n", FILE_APPEND);
            return false;
        }

        $stmt->bind_param(
            "issss",
            $idRegistro,
            $campo,
            $valorAnterior,
            $valorNuevo,
            $usuarioModificador
        );

        $result = $stmt->execute();

        if (!$result) {
            file_put_contents('debug.log', "❌ ERROR ejecutando registrarHistorial(): " . $stmt->error . "\n", FILE_APPEND);
        } else {
            file_put_contents('debug.log', "✅ Historial insertado correctamente\n", FILE_APPEND);
        }

        return $result;
    }




    public function obtenerPorUsuarioYFecha($idUsuario, $fecha)
    {
        $sql = "SELECT * FROM Registro_Horario WHERE ID_Usuario = ? AND Fecha = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $idUsuario, $fecha);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerPorUsuarioYRango($idUsuario, $inicio, $fin)
    {
        $sql = "SELECT * FROM Registro_Horario WHERE ID_Usuario = ? AND Fecha BETWEEN ? AND ? ORDER BY Fecha";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $idUsuario, $inicio, $fin);
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
