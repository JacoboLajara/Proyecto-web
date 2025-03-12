<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../../models/RecibosModel.php';
require_once __DIR__ . '/../../config/conexion.php';

/**
 * Clase de prueba para RecibosModel.
 */
class RecibosModelTest extends TestCase
{
    /**
     * @var RecibosModel
     */
    private $recibosModel;
    private $conn;

    /**
     * Configuración inicial antes de cada prueba.
     */
    protected function setUp(): void
    {
        // Establecer conexión a la base de datos
        $this->conn = require __DIR__ . '/../../config/conexion.php';

        if (!$this->conn) {
            die("❌ Error: No se pudo establecer la conexión a la base de datos.");
        } else {
            echo "\n🔹 Conexión a la base de datos establecida correctamente.\n";
        }

        // Forzar que la conexión global se pase a RecibosModel
        global $conn;
        $conn = $this->conn; // Asignamos la conexión al contexto global
        $this->recibosModel = new RecibosModel();
    }

    /**
     * Prueba para obtener los cursos disponibles en recibos.
     */
    public function testGetCursosRecibo()
    {
        echo "\n🟡 Obteniendo todos los cursos disponibles en los recibos...\n";

        $cursos = $this->recibosModel->getCursosRecibo();

        var_dump($cursos);

        $this->assertIsArray($cursos, "❌ El resultado debería ser un array.");
        $this->assertNotEmpty($cursos, "❌ No hay cursos disponibles en los recibos.");
    }

    /**
     * Prueba para obtener los recibos filtrados.
     */
    public function testGetRecibosPendientes()
    {
        echo "\n🟡 Obteniendo recibos filtrados (Pendientes)...\n";

        $apellido1 = "Pérez";
        $anio = "2024";
        $mes = "2";
        $curso = "CUR001";
        $estado = "Pendiente"; // Se filtra por estado

        $recibos = $this->recibosModel->getRecibos($apellido1, $anio, $mes, $curso, $estado);

        var_dump($recibos);

        $this->assertIsArray($recibos, "❌ El resultado debería ser un array.");
        $this->assertNotEmpty($recibos, "❌ No hay recibos pendientes.");
    }

    /**
     * Prueba para obtener todos los recibos.
     */
    public function testGetTodosRecibos()
{
    echo "\n🟡 Obteniendo todos los recibos...\n";

    $recibos = $this->recibosModel->getTodosRecibos();

    var_dump($recibos);

    $this->assertIsArray($recibos, "❌ El resultado debería ser un array.");
    $this->assertNotEmpty($recibos, "❌ No hay recibos en la base de datos.");
}


    /**
     * Prueba para marcar un recibo como pagado.
     */
    public function testActualizarPago()
    {
        $idRecibo = 3;

        echo "\n🟡 Marcando el recibo ID $idRecibo como pagado...\n";

        $resultado = $this->recibosModel->actualizarPago($idRecibo);

        var_dump($resultado);

        $this->assertTrue($resultado, "❌ No se pudo actualizar el pago del recibo.");
    }

    /**
     * Prueba para actualizar un recibo estableciendo su estado como 'Pendiente' (debe dejar la fecha de pago en NULL).
     */
    public function testUpdateReciboPendiente()
    {
        $idRecibo = 2;
        $estado = "Pendiente";
        $fechaPago = null;

        echo "\n🟡 Actualizando recibo ID $idRecibo a estado '$estado' (Fecha_Pago debe quedar NULL)...\n";

        $resultado = $this->recibosModel->updateRecibo($idRecibo, $estado, $fechaPago);

        var_dump($resultado);

        $this->assertTrue($resultado, "❌ No se pudo actualizar el recibo a 'Pendiente'.");
    }

    /**
     * Prueba para actualizar un recibo estableciendo su estado como 'Cobrado' con una fecha de pago.
     */
    public function testUpdateReciboCobrado()
    {
        $idRecibo = 5;
        $estado = "Cobrado";
        $fechaPago = "2025-02-21";

        echo "\n🟡 Actualizando recibo ID $idRecibo a estado '$estado' con fecha de pago $fechaPago...\n";

        $resultado = $this->recibosModel->updateRecibo($idRecibo, $estado, $fechaPago);

        var_dump($resultado);

        $this->assertTrue($resultado, "❌ No se pudo actualizar el recibo a 'Cobrado'.");
    }
}
