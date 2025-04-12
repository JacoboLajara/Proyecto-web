<?php
// verificar_integridad.php
require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../config/conexion.php';

$conn = include __DIR__ . '/../config/conexion.php';

function calcularHashRegistro($registro) {
    $datosConcatenados = implode('|', [
        $registro['ID_Usuario'],
        $registro['Fecha'],
        $registro['Hora_Entrada_Manana'],
        $registro['Hora_Salida_Manana'],
        $registro['Hora_Entrada_Tarde'],
        $registro['Hora_Salida_Tarde'],
        $registro['Tipo_Jornada'],
        $registro['Tipo_Dia'],
        $registro['Observaciones']
    ]);
    return hash('sha256', $datosConcatenados);
}

// Obtener registros
$sql = "SELECT * FROM Registro_Horario ORDER BY Fecha";
$result = $conn->query($sql);
$fallos = [];

while ($registro = $result->fetch_assoc()) {
    $hashCalculado = calcularHashRegistro($registro);
    if ($registro['Hash_Integridad'] !== $hashCalculado) {
        $fallos[] = [
            'id' => $registro['ID_Registro'],
            'usuario' => $registro['ID_Usuario'],
            'fecha' => $registro['Fecha'],
            'hashGuardado' => $registro['Hash_Integridad'],
            'hashCalculado' => $hashCalculado
        ];
    }
}

$fechaHoy = date('Y-m-d');
$verificacionExitosa = empty($fallos);

// Guardar resultado de verificación
$stmt = $conn->prepare("INSERT INTO Verificacion_Integridad (Fecha_Verificacion, Estado, Detalles) VALUES (?, ?, ?)");
$estado = $verificacionExitosa ? 'Correcto' : 'Fallos';
$detalles = $verificacionExitosa ? 'OK' : json_encode($fallos);
$stmt->bind_param("sss", $fechaHoy, $estado, $detalles);
$stmt->execute();

// Enviar alerta si hay errores (simplificado, aquí solo logging)
if (!$verificacionExitosa) {
    file_put_contents(__DIR__ . '/../logs/verificacion_integridad.log', "[{$fechaHoy}] FALLA DE INTEGRIDAD:\n" . print_r($fallos, true) . "\n", FILE_APPEND);
}

echo $verificacionExitosa ? "\n✔️ Integridad verificada correctamente\n" : "\n❌ Falla de integridad detectada.\n";
