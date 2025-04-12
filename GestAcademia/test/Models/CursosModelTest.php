<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../../models/CursosModel.php';
require_once __DIR__ . '/../../config/conexion.php';

/**
 * Clase de prueba para CursosModel.
 */
class CursosModelTest extends TestCase
{
    /**
     * @var CursosModel
     */
    private $cursosModel;

    /**
     * Configuración inicial antes de cada prueba.
     */
    protected function setUp(): void
    {
        // Instanciar el modelo de cursos
        $this->cursosModel = new CursosModel();
    }

    /**
     * Prueba para insertar un curso.
     */
    public function testInsertCurso()
    {
        $nombre = "GESTION CONTABLE";
        $codigo = "ADGG200";
        $duracion = "510";
        $tipo_curso = "Oficial";
        $cuota = "Gratuito";
        $precio = "0.00";

        echo "\n🟡 Insertando curso: $nombre...\n";

        $resultado = $this->cursosModel->insertCurso($nombre, $codigo, $duracion, $tipo_curso, $cuota, $precio);

        $this->assertNotEmpty($resultado, "❌ El curso no se insertó correctamente.");

    }

    /**
     * Prueba para insertar un módulo.
     */
    public function testInsertModulo()
    {
        $codigo = "ADG0001";
        $nombre = "CONTABILIDAD CLIENTE";
        $duracion = "190";

        echo "\n🟡 Insertando módulo: $nombre...\n";

        $resultado = $this->cursosModel->insertModulo($codigo, $nombre, "DAW", $duracion);

        $this->assertTrue($resultado, "❌ El módulo no se insertó correctamente.");
    }

    /**
     * Prueba para insertar una unidad formativa.
     */
    public function testInsertUnidadFormativa()
    {
        $id_modulo = "ADG0001";
        $denominacion = "CONTAPLUS";
        $codigo = "UFADG1";
        $duracion = "100";

        echo "\n🟡 Insertando unidad formativa: $denominacion...\n";

        $resultado = $this->cursosModel->insertUnidadFormativa($id_modulo, $denominacion, $codigo, $duracion);

        $this->assertTrue($resultado, "❌ La unidad formativa no se insertó correctamente.");
    }

    /**
     * Prueba para verificar si un módulo existe en la base de datos.
     */
    public function testExisteModulo()
    {
        $id_modulo = "ADG0001";

        echo "\n🟡 Verificando existencia del módulo: $id_modulo...\n";

        $resultado = $this->cursosModel->existeModulo($id_modulo);

        $this->assertTrue($resultado, "❌ El módulo no existe en la base de datos.");
    }

    /**
     * Prueba para verificar si una unidad formativa existe en la base de datos.
     */
    public function testExisteUnidad()
    {
        $id_unidad = "UFADG1";

        echo "\n🟡 Verificando existencia de la unidad formativa: $id_unidad...\n";

        $resultado = $this->cursosModel->existeUnidad($id_unidad);

        $this->assertTrue($resultado, "❌ La unidad formativa no existe en la base de datos.");
    }

    /**
     * Prueba para obtener todos los cursos.
     */
    public function testGetTodosLosCursos()
    {
        echo "\n🟡 Obteniendo todos los cursos...\n";

        $cursos = $this->cursosModel->getTodosLosCursos();

        $this->assertIsArray($cursos, "❌ El resultado debería ser un array.");
        $this->assertNotEmpty($cursos, "❌ No hay cursos en la base de datos.");
    }

    /**
     * Prueba para obtener un curso con sus módulos y unidades formativas.
     */
    public function testGetCursosConModulosYUnidades()
    {
        echo "\n🟡 Obteniendo curso con módulos y unidades formativas...\n";

        $datosCurso = $this->cursosModel->getCursosConModulosYUnidades();

        $this->assertIsArray($datosCurso, "❌ Los datos del curso deberían devolverse en un array.");
        $this->assertNotEmpty($datosCurso, "❌ No hay cursos con módulos y unidades.");
    }
}
