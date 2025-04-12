<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../../models/NotificacionModel.php';
require_once __DIR__ . '/../../config/conexion.php';

/**
 * Clase de prueba para NotificacionModel.
 */
class NotificacionModelTest extends TestCase
{
    /**
     * @var NotificacionModel
     */
    private $notificacionModel;

    /**
     * Configuración inicial antes de cada prueba.
     */
    protected function setUp(): void
    {
        // Instanciar el modelo de notificaciones
        $this->notificacionModel = new NotificacionModel();
    }

    /**
     * Prueba para obtener el ID de un rol por su nombre.
     */
    public function testObtenerIdRol()
    {
        $nombreRol = "Administrador";

        echo "\n🟡 Obteniendo ID del rol: $nombreRol...\n";
        $resultado = $this->notificacionModel->obtenerIdRol($nombreRol);

        $this->assertNotNull($resultado, "❌ No se encontró el ID del rol.");
    }

   
    /**
     * Prueba para obtener todos los usuarios.
     */
    public function testObtenerTodosLosUsuarios()
    {
        echo "\n🟡 Obteniendo todos los usuarios...\n";
        $resultado = $this->notificacionModel->obtenerTodosLosUsuarios();

        $this->assertIsArray($resultado, "❌ El resultado debería ser un array.");
        $this->assertNotEmpty($resultado, "❌ No hay usuarios en la base de datos.");
    }

    /**
     * Prueba para obtener usuarios activos por tipo.
     */
    public function testObtenerUsuariosActivosPorTipo()
    {
        $tipo = "Alumno";

        echo "\n🟡 Obteniendo usuarios activos del tipo $tipo...\n";
        $resultado = $this->notificacionModel->obtenerUsuariosActivosPorTipo($tipo);

        $this->assertIsArray($resultado, "❌ El resultado debería ser un array.");
    }

    /**
     * Prueba para insertar una notificación en la tabla correspondiente.
     */
    public function testInsertarNotificacion()
    {
        $idUsuario = "22334455E";
        $mensaje = "Prueba de notificación específica.";
        $tipo = "Alumno";

        echo "\n🟡 Insertando notificación para $tipo ID $idUsuario...\n";
        $resultado = $this->notificacionModel->insertarNotificacion($idUsuario, $mensaje, $tipo);

        $this->assertTrue($resultado, "❌ No se pudo insertar la notificación.");
    }

    /**
     * Prueba para obtener las notificaciones de un alumno.
     */
    public function testGetNotificacionesPorAlumno()
    {
        $idAlumno = "22334455E";

        echo "\n🟡 Obteniendo notificaciones del alumno ID $idAlumno...\n";
        $resultado = $this->notificacionModel->getNotificacionesPorAlumno($idAlumno);

        $this->assertIsArray($resultado, "❌ El resultado debería ser un array.");
    }
}
