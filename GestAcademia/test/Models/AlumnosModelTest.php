<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../../models/AlumnosModel.php';
require_once __DIR__ . '/../../config/conexion.php';

/**
 * Clase de prueba para AlumnosModel.
 */
class AlumnosModelTest extends TestCase
{
    /**
     * @var AlumnosModel
     */
    private $alumnosModel;
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

        // Instanciar el modelo de alumnos
        $this->alumnosModel = new AlumnosModel();
    }

    /**
     * Prueba para verificar si un alumno existe en la base de datos.
     */
    public function testExisteAlumno()
    {
        $idAlumno = "22334455E";

        echo "\n🟡 Comprobando existencia del alumno $idAlumno...\n";

        $resultado = $this->alumnosModel->existeAlumno($idAlumno);

        var_dump($resultado);

        $this->assertTrue($resultado, "❌ El alumno debería existir en la base de datos.");
    }

    /**
     * Prueba para la inserción de un alumno.
     */
    public function testInsertAlumno()
    {
        $idAlumno = "99999999X"; // ID nuevo para evitar conflicto
        $idUsuario = 4;
        $nombre = "TEST";
        $apellido1 = "PRUEBA";
        $apellido2 = "INSERT";
        $direccion = "TEST";
        $poblacion = "TEST";
        $cpostal = "12345";
        $fechanac = "2000-01-01";
        $estudios = "TEST";
        $fechalta = "2025-02-20";
        $fechabaja = null;
        $Phone = "666666666";
        $mail = "test@example.com";
        $provincia = "TEST";

        echo "\n🟡 Intentando insertar alumno $idAlumno...\n";

        $resultado = $this->alumnosModel->insertAlumno(
            $idAlumno, $idUsuario, $nombre, $apellido1, $apellido2, $direccion,
            $poblacion, $cpostal, $fechanac, $estudios, $fechalta, $fechabaja,
            $Phone, $mail, $provincia
        );

        var_dump($resultado);

        $this->assertTrue($resultado, "❌ El alumno no se insertó correctamente.");
    }

    /**
     * Prueba para la actualización de un alumno.
     */
    public function testUpdateAlumno()
    {
        $idAlumno = "22334455E"; // ID ya existente
        $nombre = "MARIA";
        $apellido1 = "PEREZ";
        $apellido2 = "SANCHEZ";
        $direccion = "ACTUALIZADO";
        $poblacion = "ADRA";
        $cpostal = "04770";
        $fechanac = "1996-04-26";
        $estudios = "Diplomatura Universitaria";
        $fechalta = "2025-02-20";
        $fechabaja = null;
        $Phone = "666666666";
        $mail = "maria_actualizado@gmail.com";
        $provincia = "MURCIA";

        echo "\n🟡 Intentando actualizar alumno $idAlumno...\n";

        $resultado = $this->alumnosModel->updateAlumno(
            $idAlumno, $nombre, $apellido1, $apellido2, $direccion,
            $poblacion, $cpostal, $fechanac, $estudios, $fechalta,
            $fechabaja, $Phone, $mail, $provincia
        );

        var_dump($resultado);

        $this->assertTrue($resultado, "❌ La actualización del alumno falló.");
    }

    /**
     * Prueba para obtener todos los alumnos.
     */
    public function testGetAlumnos()
    {
        echo "\n🟡 Obteniendo todos los alumnos...\n";

        $alumnos = $this->alumnosModel->getAlumnos();

        var_dump($alumnos);

        $this->assertIsArray($alumnos, "❌ El resultado debería ser un array.");
        $this->assertNotEmpty($alumnos, "❌ No hay alumnos en la base de datos.");
    }

    /**
     * Prueba para obtener los detalles de un alumno con sus cursos.
     */
    public function testGetAlumnoDetalleConCursos()
    {
        $idAlumno = "22334455E"; // ID que tiene cursos asignados

        echo "\n🟡 Obteniendo detalle del alumno $idAlumno...\n";

        $datosAlumno = $this->alumnosModel->getAlumnoDetalleConCursos($idAlumno);

        var_dump($datosAlumno);

        $this->assertIsArray($datosAlumno, "❌ Los datos del alumno deberían devolverse en un array.");
        $this->assertArrayHasKey('alumno', $datosAlumno, "❌ El array debe contener los datos del alumno.");
        $this->assertArrayHasKey('historial', $datosAlumno, "❌ El array debe contener el historial de cursos.");
    }
}

