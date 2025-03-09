<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../../models/AlumnosModel.php';
require_once __DIR__ . '/../../models/CursosModel.php';
require_once __DIR__ . '/../../models/MatriculasModel.php';
require_once __DIR__ . '/../../config/conexion.php';

/**
 * Prueba de integración para la inscripción de un alumno en un curso.
 */
class InscripcionTest extends TestCase
{
    private $conn;
    private $alumnosModel;
    private $cursosModel;
    private $matriculasModel;

    /**
     * Configuración antes de cada prueba: Inserta un alumno y un curso de prueba.
     */
    protected function setUp(): void
    {
        // Conectar a la base de datos
        $this->conn = require __DIR__ . '/../../config/conexion.php';
        $this->alumnosModel = new AlumnosModel();
        $this->cursosModel = new CursosModel();
        $this->matriculasModel = new MatriculasModel();

        // 🔹 ELIMINAR REGISTROS ANTES DE INSERTAR
        $this->conn->query("DELETE FROM alumno_curso WHERE ID_Alumno = '99999999X' AND ID_Curso = 'C001'");
        $this->conn->query("DELETE FROM alumno WHERE ID_Alumno = '99999999X'");
        $this->conn->query("DELETE FROM curso WHERE ID_Curso = 'C001'");

        // 🔹 Insertar alumno de prueba
        $sqlAlumno = "INSERT INTO alumno (ID_Alumno, ID_Usuario, Nombre, Apellido1, Email) 
                      VALUES ('99999999X', 1, 'Juan', 'Pérez', 'juan@example.com')";
        $this->conn->query($sqlAlumno);

        // 🔹 Insertar curso de prueba
        $sqlCurso = "INSERT INTO curso (ID_Curso, Nombre, Duracion_Horas, Tipo, Tipo_cuota, Precio_curso) 
                     VALUES ('C001', 'Curso de PHP', '40', 'Oficial', 'Mensual', '0')";
        $this->conn->query($sqlCurso);
    }

    /**
     * Prueba de integración: Inscribir al alumno en un curso y verificar que está inscrito correctamente.
     */
    public function testInscribirAlumnoEnCurso()
    {
        // 🔹 Act: Ejecutar la inscripción con insertMatricula()
        $resultado = $this->matriculasModel->insertMatricula('99999999X', 'C001');

        // 🔹 Assert: Verificar que la inscripción fue exitosa
        $this->assertTrue($resultado, "❌ La inscripción del alumno en el curso falló.");

        // 🔹 Verificar que la inscripción está en la base de datos
        $sqlVerificacion = "SELECT COUNT(*) as total FROM alumno_curso WHERE ID_Alumno = '99999999X' AND ID_Curso = 'C001'";
        $resultadoVerificacion = $this->conn->query($sqlVerificacion)->fetch_assoc();

        $this->assertEquals(1, $resultadoVerificacion['total'], "❌ La inscripción no se registró en la base de datos.");
    }

    /**
     * Limpieza después de cada prueba: Elimina los datos de prueba.
     */
    protected function tearDown(): void
    {
        $this->conn->query("DELETE FROM alumno_curso WHERE ID_Alumno = '99999999X' AND ID_Curso = 'C001'");
        $this->conn->query("DELETE FROM alumno WHERE ID_Alumno = '99999999X'");
        $this->conn->query("DELETE FROM curso WHERE ID_Curso = 'C001'");
    }
}
