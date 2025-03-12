<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../../models/MatriculasModel.php';
require_once __DIR__ . '/../../config/conexion.php';

/**
 * Clase de prueba para MatriculasModel.
 */
class MatriculasModelTest extends TestCase
{
    /**
     * @var MatriculasModel
     */
    private $matriculasModel;

    /**
     * Configuración inicial antes de cada prueba.
     */
    protected function setUp(): void
    {
        // Instanciar el modelo de matrículas
        $this->matriculasModel = new MatriculasModel();
    }

    /**
     * Prueba para obtener la lista de cursos.
     */
    public function testGetCursos()
    {
        echo "\n🟡 Obteniendo todos los cursos...\n";
        $resultado = $this->matriculasModel->getCursos();

        $this->assertIsArray($resultado, "❌ El resultado debería ser un array.");
        $this->assertNotEmpty($resultado, "❌ No hay cursos en la base de datos.");
    }

    /**
     * Prueba para obtener la lista de alumnos.
     */
    public function testGetAlumnos()
    {
        echo "\n🟡 Obteniendo todos los alumnos...\n";
        $resultado = $this->matriculasModel->getAlumnos();

        $this->assertIsArray($resultado, "❌ El resultado debería ser un array.");
        $this->assertNotEmpty($resultado, "❌ No hay alumnos en la base de datos.");
    }

    /**
     * Prueba para insertar una matrícula.
     */
    public function testInsertMatricula()
    {
        $idAlumno = "22334455E"; // Asegúrate de que este ID existe en la BD
        $idCurso = "CUR001"; // ID del curso

        echo "\n🟡 Matriculando alumno $idAlumno en el curso $idCurso...\n";
        $resultado = $this->matriculasModel->insertMatricula($idAlumno, $idCurso);

        $this->assertTrue($resultado, "❌ No se pudo insertar la matrícula.");
    }

    /**
     * Prueba para verificar si una matrícula ya existe.
     */
    public function testExisteMatricula()
    {
        $idAlumno = "22334455E";
        $idCurso = "CUR001";

        echo "\n🟡 Verificando si el alumno $idAlumno ya está matriculado en el curso $idCurso...\n";
        $resultado = $this->matriculasModel->existeMatricula($idAlumno, $idCurso);

        $this->assertTrue($resultado, "❌ La matrícula debería existir.");
    }

    /**
     * Prueba para obtener los alumnos matriculados en un curso.
     */
    public function testGetAlumnosMatriculadosPorCurso()
    {
        $idCurso = "CUR001";

        echo "\n🟡 Obteniendo alumnos matriculados en el curso $idCurso...\n";
        $resultado = $this->matriculasModel->getAlumnosMatriculadosPorCurso($idCurso);

        $this->assertIsArray($resultado, "❌ El resultado debería ser un array.");
        $this->assertNotEmpty($resultado, "❌ No hay alumnos matriculados en el curso.");
    }

    /**
     * Prueba para dar de baja a alumnos matriculados en un curso.
     */
    public function testDarDeBajaAlumnos()
    {
        $idCurso = "CUR001";
        $alumnos = ["22334455E"]; // Asegúrate de que este ID existe y está matriculado

        echo "\n🟡 Dando de baja al alumno $alumnos[0] en el curso $idCurso...\n";
        $resultado = $this->matriculasModel->darDeBajaAlumnos($idCurso, $alumnos);

        $this->assertTrue($resultado, "❌ No se pudo dar de baja al alumno.");
    }
}
