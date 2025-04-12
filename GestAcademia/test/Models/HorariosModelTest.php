<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../../models/HorariosModel.php';
require_once __DIR__ . '/../../config/conexion.php';

/**
 * Clase de prueba para HorariosModel.
 */
class HorariosModelTest extends TestCase
{
    /**
     * @var HorariosModel
     */
    private $horariosModel;

    /**
     * Configuración inicial antes de cada prueba.
     */
    protected function setUp(): void
    {
        // Instanciar el modelo de horarios
        $this->horariosModel = new HorariosModel();
    }

    /**
     * Prueba para obtener todas las aulas.
     */
    public function testGetAulas()
    {
        echo "\n🟡 Obteniendo todas las aulas...\n";
        $resultado = $this->horariosModel->getAulas();

        $this->assertIsArray($resultado, "❌ El resultado debería ser un array.");
        $this->assertNotEmpty($resultado, "❌ No hay aulas en la base de datos.");
    }

    /**
     * Prueba para obtener todos los cursos.
     */
    public function testGetCursos()
    {
        echo "\n🟡 Obteniendo todos los cursos...\n";
        $resultado = $this->horariosModel->getCursos();

        $this->assertIsArray($resultado, "❌ El resultado debería ser un array.");
        $this->assertNotEmpty($resultado, "❌ No hay cursos en la base de datos.");
    }

    /**
     * Prueba para asignar un horario a un aula y curso.
     */
    public function testAsignarHorario()
    {
        $aula = 99; // Asegúrate de que este ID de aula existe en la base de datos
        $curso = "CUR001"; // ID del curso
        $dia = "Lunes";
        $hora_inicio = "10:00:00";
        $hora_fin = "12:00:00";

        echo "\n🟡 Asignando horario para Aula $aula, Curso $curso...\n";
        $resultado = $this->horariosModel->asignarHorario($aula, $curso, $dia, $hora_inicio, $hora_fin);

        $this->assertIsArray($resultado, "❌ El resultado debería ser un array.");
        $this->assertArrayHasKey("success", $resultado, "❌ Falta la clave 'success' en la respuesta.");
        $this->assertTrue($resultado["success"], "❌ El horario no se asignó correctamente: " . $resultado["message"]);
    }

    /**
     * Prueba para evitar la asignación de horarios solapados.
     */
    public function testAsignarHorarioSolapado()
    {
        $aula = 2; // Asegúrate de que este ID de aula existe
        $curso = "CUR001"; // Otro curso
        $dia = "Lunes";
        $hora_inicio = "11:00:00"; // Solapado con el anterior (10:00 - 12:00)
        $hora_fin = "13:00:00";

        echo "\n🟡 Intentando asignar un horario solapado...\n";
        $resultado = $this->horariosModel->asignarHorario($aula, $curso, $dia, $hora_inicio, $hora_fin);

        $this->assertIsArray($resultado, "❌ El resultado debería ser un array.");
        $this->assertArrayHasKey("success", $resultado, "❌ Falta la clave 'success' en la respuesta.");
        $this->assertFalse($resultado["success"], "❌ Se permitió un horario solapado.");
    }

    /**
     * Prueba para obtener los horarios ocupados de un aula.
     */
    public function testGetHorariosOcupados()
    {
        $aula = 2;

        echo "\n🟡 Obteniendo horarios ocupados del aula $aula...\n";
        $resultado = $this->horariosModel->getHorariosOcupados($aula);

        $this->assertIsArray($resultado, "❌ El resultado debería ser un array.");
        $this->assertNotEmpty($resultado, "❌ No hay horarios ocupados en el aula.");
    }
}
