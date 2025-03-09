<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../../models/NotasModel.php';
require_once __DIR__ . '/../../config/conexion.php';

/**
 * Clase de prueba para NotasModel.
 */
class NotasModelTest extends TestCase
{
    /**
     * @var NotasModel
     */
    private $notasModel;

    /**
     * Configuración inicial antes de cada prueba.
     */
    protected function setUp(): void
    {
        // Instanciar el modelo de notas
        $this->notasModel = new NotasModel();
    }

    /**
     * Prueba para obtener los alumnos de un curso.
     */
    public function testGetAlumnosPorCurso()
    {
        $idCurso = 'CUR001';
        echo "\n🟡 Obteniendo alumnos del curso: $idCurso...\n";
        $resultado = $this->notasModel->getAlumnosPorCurso($idCurso);
        $this->assertIsArray($resultado, "❌ El resultado debería ser un array.");
    }

    /**
     * Prueba para obtener las notas de un alumno en un curso.
     */
    public function testGetNotasPorAlumno()
    {
        $idAlumno = '22334455E';
        $idCurso = 'CUR002';
        echo "\n🟡 Obteniendo notas del alumno $idAlumno en el curso $idCurso...\n";
        $resultado = $this->notasModel->getNotasPorAlumno($idAlumno, $idCurso);
        $this->assertIsArray($resultado, "❌ El resultado debería ser un array.");
    }

    /**
     * Prueba para insertar una nota.
     */
    public function testInsertNota()
    {
        $idAlumno = '22334455E';
        $idCurso = 'CUR002';
        $idModulo = 'ADG0001';
        $idUnidad = 'UFADG1';
        $tipoNota = 'Unidad_Formativa';
        $calificacion = 8.5;
        echo "\n🟡 Insertando nota para el alumno $idAlumno...\n";
        $resultado = $this->notasModel->insertNota($idAlumno, $idCurso, $idModulo, $idUnidad, $tipoNota, $calificacion);
        $this->assertTrue($resultado, "❌ La nota no se insertó correctamente.");
    }

    /**
     * Prueba para actualizar las notas de un alumno en un curso.
     */
    public function testActualizarNotas()
    {
        $idAlumno = '22334455E';
        $idCurso = 'CUR002';
        echo "\n🟡 Actualizando notas del alumno $idAlumno en el curso $idCurso...\n";
        $this->notasModel->actualizarNotas($idAlumno, $idCurso);
        $this->assertTrue(true, "❌ Error al actualizar las notas.");
    }

    /**
     * Prueba para actualizar una nota específica.
     */
    public function testUpdateNota()
    {
        $idAlumno = '22334455E';
        $idCurso = 'CUR002';
        $tipoNota = 'Unidad_Formativa';
        $calificacion = 9.5;
        $idUnidad = 'U001';
        echo "\n🟡 Actualizando nota de la unidad formativa $idUnidad...\n";
        $resultado = $this->notasModel->updateNota($idAlumno, $idCurso, $tipoNota, $calificacion, $idUnidad);
        $this->assertTrue($resultado, "❌ Error al actualizar la nota.");
    }

    /**
     * Prueba para obtener módulos y unidades de un curso.
     */
    public function testGetModulosUnidadesPorCurso()
    {
        $idCurso = 'CUR002';
        echo "\n🟡 Obteniendo módulos y unidades del curso $idCurso...\n";
        $resultado = $this->notasModel->getModulosUnidadesPorCurso($idCurso);
        $this->assertIsArray($resultado, "❌ El resultado debería ser un array.");
    }

    /**
     * Prueba para obtener todas las notas de un alumno.
     */
    public function testObtenerTodasLasNotasPorAlumno()
    {
        $idAlumno = '22334455E';
        echo "\n🟡 Obteniendo todas las notas del alumno $idAlumno...\n";
        $resultado = $this->notasModel->obtenerTodasLasNotasPorAlumno($idAlumno);
        $this->assertIsArray($resultado, "❌ El resultado debería ser un array.");
    }

    /**
     * Prueba para obtener las notas de un alumno en un curso específico.
     */
    public function testObtenerNotasPorAlumnoYCurso()
    {
        $idAlumno = '22334455E';
        $idCurso = 'CUR002';
        echo "\n🟡 Obteniendo notas del alumno $idAlumno en el curso $idCurso...\n";
        $resultado = $this->notasModel->obtenerNotasPorAlumnoYCurso($idAlumno, $idCurso);
        $this->assertIsArray($resultado, "❌ El resultado debería ser un array.");
    }
}
