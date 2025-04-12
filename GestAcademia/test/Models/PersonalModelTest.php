<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../../models/PersonalModel.php';
require_once __DIR__ . '/../../config/conexion.php';

/**
 * Clase de prueba para PersonalModel.
 */
class PersonalModelTest extends TestCase
{
    /**
     * @var PersonalModel
     */
    private $personalModel;

    /**
     * Configuración inicial antes de cada prueba.
     */
    protected function setUp(): void
    {
        $this->personalModel = new PersonalModel();
    }

    /**
     * Prueba para verificar si un empleado del personal no docente existe en la base de datos.
     */
    public function testExistePersonal()
    {
        $idPersonal = "55667788D";

        $resultado = $this->personalModel->existePersonal($idPersonal);

        $this->assertTrue($resultado, "❌ El personal debería existir en la base de datos.");
    }

    /**
     * Prueba para la inserción de un empleado del personal no docente.
     */
    public function testInsertPersonal()
    {
        $idPersonal = "99999999X"; // ID nuevo para evitar conflicto
        $idUsuario = 2;
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
        $Phone = "999999999";
        $mail = "test@example.com";
        $provincia = "TEST";

        $resultado = $this->personalModel->insertPersonal(
            $idPersonal, $idUsuario, $nombre, $apellido1, $apellido2, $direccion,
            $poblacion, $cpostal, $fechanac, $estudios, $fechalta, $fechabaja,
            $Phone, $mail, $provincia
        );

        $this->assertTrue($resultado, "❌ El personal no se insertó correctamente.");
    }

    /**
     * Prueba para la actualización de un empleado del personal no docente.
     */
    public function testUpdatePersonal()
    {
        $idPersonalOld = "55667788D"; // ID ya existente
        $idPersonalNew = "29045847T"; // No cambia el ID en este caso
        $nombre = "PEDRO MODIFICADO";
        $apellido1 = "MARTINEZ";
        $apellido2 = "MARTINEZ";
        $direccion = "MODIFICADO";
        $poblacion = "JUMILLA";
        $cpostal = "30520";
        $fechanac = "1985-04-05";
        $estudios = "Bachiller";
        $fechalta = "2025-02-20";
        $fechabaja = null;
        $Phone = "222222222";
        $mail = "pedro_modificado@gmail.com";
        $provincia = "MURCIA";

        $resultado = $this->personalModel->updatePersonal(
            $idPersonalNew, $nombre, $apellido1, $apellido2, $direccion,
            $poblacion, $cpostal, $fechanac, $estudios, $fechalta,
            $fechabaja, $Phone, $mail, $provincia, $idPersonalOld
        );

        $this->assertTrue($resultado, "❌ La actualización del personal falló.");
    }

    /**
     * Prueba para obtener todos los empleados del personal no docente.
     */
    public function testGetPersonal()
    {
        $personal = $this->personalModel->getPersonal();

        $this->assertIsArray($personal, "❌ El resultado debería ser un array.");
        $this->assertNotEmpty($personal, "❌ No hay empleados del personal en la base de datos.");
    }

    /**
     * Prueba para obtener los detalles de un empleado del personal no docente.
     */
    public function testGetPersonalDetalle()
    {
        $idPersonal = "29045847T"; // ID que tiene datos

        $datosPersonal = $this->personalModel->getPersonalDetalle($idPersonal);

        $this->assertIsArray($datosPersonal, "❌ Los datos del personal deberían devolverse en un array.");
        $this->assertArrayHasKey('ID_Personal', $datosPersonal, "❌ El array debe contener los datos del personal.");
    }
}
