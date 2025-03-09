<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../../models/AulasModel.php';
require_once __DIR__ . '/../../config/conexion.php';

/**
 * Clase de prueba para AulasModel.
 */
class AulasModelTest extends TestCase
{
    /**
     * @var AulasModel
     */
    private $aulasModel;

    /**
     * Configuración inicial antes de cada prueba.
     */
    protected function setUp(): void
    {
        $this->aulasModel = new AulasModel();
    }

    /**
     * Prueba para verificar si un aula existe en la base de datos.
     */
    public function testExisteAula()
    {
        $idAula = "1";

        $resultado = $this->aulasModel->existeAula($idAula);

        $this->assertTrue($resultado, "❌ El aula debería existir en la base de datos.");
    }

    /**
     * Prueba para la inserción de un aula.
     */
    public function testInsertAula()
    {
        $idAula = "99"; // ID nuevo para evitar conflicto
        $nombre = "AULA DE PRUEBAS";
        $capacidad = "20";

        $resultado = $this->aulasModel->insertAulas($idAula, $nombre, $capacidad);

        $this->assertTrue($resultado, "❌ El aula no se insertó correctamente.");
    }

    /**
     * Prueba para la actualización de un aula.
     */
    public function testUpdateAula()
    {
        $idAula = "1"; // ID ya existente
        $nombre = "AULA INFORMÁTICA ACTUALIZADA";
        $capacidad = "25";

        $resultado = $this->aulasModel->updateAulas($idAula, $nombre, $capacidad);

        $this->assertTrue($resultado, "❌ La actualización del aula falló.");
    }

    /**
     * Prueba para obtener todas las aulas.
     */
    public function testGetAulas()
    {
        $aulas = $this->aulasModel->getAulas();

        $this->assertIsArray($aulas, "❌ El resultado debería ser un array.");
        $this->assertNotEmpty($aulas, "❌ No hay aulas en la base de datos.");
    }
}
