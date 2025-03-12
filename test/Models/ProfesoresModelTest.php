<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../../models/ProfesoresModel.php';
require_once __DIR__ . '/../../config/conexion.php';

/**
 * Clase de prueba para ProfesoresModel.
 */
class ProfesoresModelTest extends TestCase
{
    /**
     * @var ProfesoresModel
     */
    private $profesoresModel;

    /**
     * Configuración inicial antes de cada prueba.
     */
    protected function setUp(): void
    {
        $this->profesoresModel = new ProfesoresModel();
    }

    /**
     * Prueba para verificar si un profesor existe en la base de datos.
     */
    public function testExisteProfesor()
    {
        $idProfesor = "11223344C";

        // Verificamos que el profesor está en la base de datos
        $resultado = $this->profesoresModel->existeProfesor($idProfesor);

        $this->assertTrue($resultado, "❌ El profesor debería existir en la base de datos.");
    }

    /**
     * Prueba para la inserción de un profesor.
     */
    public function testInsertProfesor()
    {
        $idProfesor = "99999999X"; // ID modificado para evitar conflicto
        $idUsuario = 3;
        $nombre = "TEST";
        $apellido1 = "PRUEBA";
        $apellido2 = "INSERT";
        $direccion = "TEST";
        $poblacion = "TEST";
        $cpostal = "12345";
        $fechanac = "1990-01-01";
        $estudios = "TEST";
        $fechalta = "2025-02-20";
        $fechabaja = null;
        $Phone = "666666666";
        $mail = "test@example.com";
        $provincia = "TEST";
        $especialidad = "TEST";

        $resultado = $this->profesoresModel->insertProfesor(
            $idProfesor, $idUsuario, $nombre, $apellido1, $apellido2, $direccion,
            $poblacion, $cpostal, $fechanac, $estudios, $fechalta, $fechabaja,
            $Phone, $mail, $provincia, $especialidad
        );

        $this->assertTrue($resultado, "❌ El profesor no se insertó correctamente.");
    }

    /**
     * Prueba para la actualización de un profesor.
     */
    public function testUpdateProfesor()
    {
        $idProfesor = "11223344C"; // ID ya existente
        $nombre = "ANA ACTUALIZADO";
        $apellido1 = "ANTUNEZ";
        $apellido2 = "GARCIA";
        $direccion = "NUEVA DIRECCIÓN";
        $poblacion = "MURCIA";
        $cpostal = "30010";
        $fechanac = "1956-10-10";
        $estudios = "Doctorado";
        $fechalta = "2025-02-20";
        $fechabaja = null;
        $Phone = "777777777";
        $mail = "antonio_actualizado@example.com";
        $provincia = "MURCIA";
        $especialidad = "Programación Web";

        $resultado = $this->profesoresModel->updateProfesor(
            $idProfesor, $nombre, $apellido1, $apellido2, $direccion,
            $poblacion, $cpostal, $fechanac, $estudios, $fechalta,
            $fechabaja, $Phone, $mail, $provincia, $especialidad
        );

        $this->assertTrue($resultado, "❌ La actualización del profesor falló.");
    }

    /**
     * Prueba para obtener todos los profesores.
     */
    public function testGetProfesores()
    {
        $profesores = $this->profesoresModel->getProfesores();

        $this->assertIsArray($profesores, "❌ El resultado debería ser un array.");
        $this->assertNotEmpty($profesores, "❌ No hay profesores en la base de datos.");
    }

    /**
     * Prueba para obtener los detalles de un profesor por su ID.
     */
    public function testGetProfesorDetalle()
    {
        $idProfesor = "11223344C"; // ID que debe existir

        $datosProfesor = $this->profesoresModel->getProfesorDetalle($idProfesor);

        $this->assertIsArray($datosProfesor, "❌ Los datos del profesor deberían devolverse en un array.");
        $this->assertArrayHasKey('profesor', $datosProfesor, "❌ El array debe contener los datos del profesor.");
    }
}
