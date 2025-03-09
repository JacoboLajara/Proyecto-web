<?php
/**
 * Modelo para la gestión de cursos, módulos y unidades formativas.
 *
 * Este modelo se encarga de realizar operaciones CRUD (Crear, Leer, Actualizar, Eliminar)
 * sobre las tablas curso, modulo, unidad_formativa y curso_modulo en la base de datos.
 *
 * @package YourPackageName
 */
class CursosModel
{
    /**
     * Conexión a la base de datos.
     *
     * @var mysqli
     */
    private $conn;

    /**
     * Constructor de la clase.
     *
     * Inicializa la conexión a la base de datos incluyendo el archivo de configuración.
     * Si la conexión falla, se termina la ejecución del script.
     */
    public function __construct()
    {
        $this->conn = include __DIR__ . '/../config/conexion.php';

        if (!$this->conn) {
            die("Error: La conexión no se pudo establecer correctamente.");
        }
    }

    /**
     * Obtiene la conexión a la base de datos.
     *
     * @return mysqli Conexión a la base de datos.
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Inserta un curso en la base de datos.
     *
     * Inserta un nuevo registro en la tabla curso con los datos proporcionados.
     * Confirma la transacción en caso de éxito y registra un mensaje en el log.
     *
     * @param string $nombre Nombre del curso.
     * @param string $codigo Código único del curso.
     * @param string $duracion Duración del curso en horas.
     * @param string $tipo_curso Tipo de curso.
     * @param string $cuota Tipo de cuota.
     * @param string $precio Precio del curso.
     * @return mixed Devuelve el código del curso en caso de éxito, o false en caso de error.
     */
    public function insertCurso($nombre, $codigo, $duracion, $tipo_curso, $cuota, $precio)
    {
        $sql = "INSERT INTO curso (Nombre, ID_Curso, Duracion_Horas, Tipo, Tipo_cuota, Precio_curso) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            error_log("Error al preparar la consulta: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("ssssss", $nombre, $codigo, $duracion, $tipo_curso, $cuota, $precio);

        if ($stmt->execute()) {
            $this->conn->commit();  // Confirmar la transacción después de la inserción
            error_log("Curso insertado correctamente con Código: " . $codigo);
            return $codigo;  // Devuelve el código único como identificador
        } else {
            $this->conn->rollback();  // Si falla, revertir la transacción
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            return false;
        }
    }

    /**
     * Inserta un módulo en la base de datos.
     *
     * Inserta un nuevo registro en la tabla modulo con los datos proporcionados.
     *
     * @param string $codigo Código único del módulo.
     * @param string $nombre Nombre del módulo.
     * @param mixed $id_curso Identificador del curso al que pertenece el módulo (no se utiliza en la consulta).
     * @param int $duracion Duración del módulo en horas.
     * @return bool True en caso de éxito, false en caso de error.
     */
    public function insertModulo($codigo, $nombre, $id_curso, $duracion)
    {
        /*error_log("Valores recibidos en insertModulo: ID_Curso=$id_curso, Nombre=$nombre, ID_Modulo=$codigo, Duracion=$duracion");
        file_put_contents('debug.log', "Insertando módulo EN EL INSERTMODULO: ID_Curso=$id_curso, Código=$codigo, Nombre=$nombre, Duración=$duracion\n", FILE_APPEND);*/
        $sql = "INSERT INTO modulo (ID_Modulo, Nombre, Duracion_Horas) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            error_log("Error al preparar la consulta: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("ssi", $codigo, $nombre, $duracion);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            return false;
        }
    }

    /**
     * Inserta una unidad formativa en la base de datos.
     *
     * Inserta un nuevo registro en la tabla unidad_formativa con los datos proporcionados.
     *
     * @param string $id_modulo Identificador del módulo al que pertenece la unidad.
     * @param string $denominacion Nombre o denominación de la unidad formativa.
     * @param string $codigo Código único de la unidad formativa.
     * @param int $duracion Duración de la unidad formativa.
     * @return bool True en caso de éxito, false en caso de error.
     */
    public function insertUnidadFormativa($id_modulo, $denominacion, $codigo, $duracion)
    {
        $sql = "INSERT INTO unidad_formativa (ID_Modulo, Nombre, ID_Unidad_Formativa, Duracion_Unidad) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            error_log("Error al preparar la consulta: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("sssi", $id_modulo, $denominacion, $codigo, $duracion);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            return false;
        }
    }

    /**
     * Inserta la relación entre un curso y un módulo en la tabla curso_modulo.
     *
     * @param string $id_modulo Código único del módulo.
     * @param string $codigo Código único del curso.
     * @return bool True en caso de éxito, false en caso de error.
     */
    public function insertCursoWithModulo($id_modulo, $codigo)
    {
        $sql = "INSERT INTO curso_modulo (ID_Modulo, ID_Curso) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            error_log("Error al preparar la consulta: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("ss", $id_modulo, $codigo);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            return false;
        }
    }

    /**
     * Obtiene el último ID insertado en la conexión.
     *
     * @return mixed El último ID insertado si es mayor que 0, o null en caso de error.
     */
    public function getLastInsertedId()
    {
        $lastId = $this->conn->insert_id;
        if ($lastId > 0) {
            return $lastId;
        } else {
            error_log("Error al obtener el último ID insertado: No se ha realizado ninguna inserción.");
            return null;
        }
    }

    /**
     * Inserta un curso junto con sus módulos y unidades formativas.
     *
     * Ejecuta una transacción que inserta un curso, luego los módulos asociados
     * y finalmente las unidades formativas de cada módulo.
     *
     * @param array $curso Datos del curso. Debe incluir 'nombre', 'codigo', 'duracion', 'tipo_curso' y 'precio'.
     * @param array $modulos Array de módulos. Cada elemento debe incluir 'nombre', 'codigo' y 'duracion'.
     * @param array $unidades Array asociativo de unidades formativas. La clave debe ser el código del módulo y el valor, un array de unidades.
     * @return bool True si la transacción fue exitosa, false en caso de error.
     */
    public function insertCursoWithModulesAndUnits($curso, $modulos, $unidades)
    {
        $this->conn->begin_transaction();

        try {
            // Insertar curso
            $this->insertCurso($curso['nombre'], $curso['codigo'], $curso['duracion'], $curso['tipo_curso'],$curso['tipo_cuota'], $curso['precio']);
            $id_curso = $this->getLastInsertedId();

            // Insertar módulos
            foreach ($modulos as $modulo) {
                $this->insertModulo($id_curso, $modulo['nombre'], $modulo['codigo'], $modulo['duracion']);
                $id_modulo = $this->getLastInsertedId();

                // Insertar unidades formativas para cada módulo
                foreach ($unidades[$modulo['codigo']] as $unidad) {
                    $this->insertUnidadFormativa($id_modulo, $unidad['nombre'], $unidad['codigo'], $unidad['duracion']);
                }
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error durante la transacción: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si un módulo existe en la base de datos.
     *
     * Realiza una consulta que cuenta las ocurrencias del módulo con el ID proporcionado.
     *
     * @param string $id_modulo Identificador del módulo.
     * @return bool True si el módulo existe, false en caso contrario.
     */
    public function existeModulo($id_modulo)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Modulo WHERE ID_Modulo = ?");
        $stmt->bind_param("s", $id_modulo);
        $stmt->execute();
        $count = 0;
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        file_put_contents('debug.log', "Existe módulo $id_modulo: " . ($count > 0 ? 'Sí' : 'No') . "\n", FILE_APPEND);
        return $count > 0;
    }

    /**
     * Verifica si una unidad formativa existe en la base de datos.
     *
     * Realiza una consulta que cuenta las ocurrencias de la unidad formativa con el ID proporcionado.
     *
     * @param string $id_unidad Identificador de la unidad formativa.
     * @return bool True si la unidad existe, false en caso contrario.
     */
    public function existeUnidad($id_unidad)
    {
        $count = 0;
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Unidad_Formativa WHERE ID_Unidad_Formativa = ?");
        $stmt->bind_param("s", $id_unidad);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        file_put_contents('debug.log', "Existe unidad $id_unidad: " . ($count > 0 ? 'Sí' : 'No') . "\n", FILE_APPEND);
        return $count > 0;
    }

    /**
     * Actualiza los datos de un módulo en la base de datos.
     *
     * Modifica el nombre y la duración del módulo identificado por su ID.
     *
     * @param string $id_modulo Identificador del módulo.
     * @param string $nombre Nuevo nombre del módulo.
     * @param int $duracion Nueva duración del módulo en horas.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function updateModulo($id_modulo, $nombre, $duracion)
    {
        $stmt = $this->conn->prepare("UPDATE Modulo SET Nombre = ?, Duracion_Horas = ? WHERE ID_Modulo = ?");
        $stmt->bind_param("sis", $nombre, $duracion, $id_modulo);
        return $stmt->execute();
    }

    /**
     * Actualiza los datos de una unidad formativa en la base de datos.
     *
     * Modifica el nombre y la duración de la unidad formativa identificada por su ID.
     *
     * @param string $id_unidad Identificador de la unidad formativa.
     * @param string $nombre Nuevo nombre de la unidad formativa.
     * @param int $duracion Nueva duración de la unidad formativa.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function updateUnidadFormativa($id_unidad, $nombre, $duracion)
    {
        $stmt = $this->conn->prepare("UPDATE Unidad_Formativa SET Nombre = ?, Duracion_Unidad = ? WHERE ID_Unidad_Formativa = ?");
        $stmt->bind_param("sis", $nombre, $duracion, $id_unidad);
        return $stmt->execute();
    }

    /**
     * Obtiene los módulos asociados a un curso.
     *
     * Realiza una consulta a la tabla curso_modulo para obtener los ID de los módulos asociados al curso.
     *
     * @param string $id_curso Identificador del curso.
     * @return array Array de módulos (cada elemento es un array asociativo con el campo 'ID_Modulo').
     */
    public function getModulosPorCurso($id_curso)
    {
        $stmt = $this->conn->prepare("SELECT ID_Modulo FROM Curso_Modulo WHERE ID_Curso = ?");
        $stmt->bind_param("s", $id_curso);
        $stmt->execute();
        $result = $stmt->get_result();
        $modulos = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $modulos;
    }

    /**
     * Obtiene las unidades formativas asociadas a un módulo.
     *
     * Realiza una consulta a la tabla unidad_formativa para obtener los ID de las unidades formativas asociadas al módulo.
     *
     * @param string $id_modulo Identificador del módulo.
     * @return array Array de unidades formativas (cada elemento es un array asociativo con el campo 'ID_Unidad_Formativa').
     */
    public function getUnidadesPorModulo($id_modulo)
    {
        $stmt = $this->conn->prepare("SELECT ID_Unidad_Formativa FROM Unidad_Formativa WHERE ID_Modulo = ?");
        $stmt->bind_param("s", $id_modulo);
        $stmt->execute();
        $result = $stmt->get_result();
        $unidades = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $unidades;
    }

    /**
     * Elimina un módulo y sus relaciones asociadas de la base de datos.
     *
     * Primero elimina las unidades formativas asociadas al módulo,
     * luego elimina la relación en la tabla curso_modulo,
     * y finalmente elimina el módulo de la tabla modulo.
     *
     * @param string $id_modulo Identificador del módulo a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function deleteModulo($id_modulo)
    {
        // 🔥 Eliminar unidades formativas antes de borrar el módulo
        $stmt1 = $this->conn->prepare("DELETE FROM Unidad_Formativa WHERE ID_Modulo = ?");
        $stmt1->bind_param("s", $id_modulo);
        $stmt1->execute();
        $stmt1->close();

        // 🔥 Eliminar relación en Curso_Modulo
        $stmt2 = $this->conn->prepare("DELETE FROM Curso_Modulo WHERE ID_Modulo = ?");
        $stmt2->bind_param("s", $id_modulo);
        $stmt2->execute();
        $stmt2->close();

        // 🔥 Eliminar el módulo
        $stmt3 = $this->conn->prepare("DELETE FROM Modulo WHERE ID_Modulo = ?");
        $stmt3->bind_param("s", $id_modulo);
        return $stmt3->execute();
    }

    /**
     * Elimina una unidad formativa de la base de datos.
     *
     * @param string $id_unidad Identificador de la unidad formativa a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function deleteUnidadFormativa($id_unidad)
    {
        $stmt = $this->conn->prepare("DELETE FROM Unidad_Formativa WHERE ID_Unidad_Formativa = ?");
        $stmt->bind_param("s", $id_unidad);
        return $stmt->execute();
    }

    /**
     * Obtiene todos los cursos con datos básicos.
     *
     * Realiza una consulta a la tabla curso para obtener una lista de todos los cursos
     * con campos básicos como ID_Curso, Nombre, Duracion_Horas, Tipo, Tipo_cuota y Precio_curso.
     *
     * @return array Array de cursos con datos básicos.
     */
    public function getTodosLosCursos()
    {
        $sql = "SELECT ID_Curso, Nombre, Duracion_Horas, Tipo, Tipo_cuota, Precio_curso FROM Curso";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            error_log("Error al preparar la consulta: " . $this->conn->error);
            return [];
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $cursos = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $cursos;
    }

    /**
     * Obtiene todos los cursos junto con sus módulos y unidades formativas.
     *
     * Realiza una consulta a la tabla curso para obtener los cursos, luego obtiene
     * los módulos asociados a cada curso y, finalmente, las unidades formativas correspondientes a cada módulo.
     *
     * @return array Array de cursos, cada uno con sus módulos y unidades formativas.
     */
    public function getCursosConModulosYUnidades()
    {
        $conn = $this->getConnection();
        $cursosData = [];

        // Obtener todos los cursos
        $sqlCursos = "SELECT ID_Curso, Nombre, Duracion_Horas, Tipo, Tipo_cuota, Precio_curso FROM Curso";
        $resultCursos = $conn->query($sqlCursos);

        while ($curso = $resultCursos->fetch_assoc()) {
            $curso['modulos'] = [];

            // Obtener módulos del curso
            $sqlModulos = "SELECT m.ID_Modulo, m.Nombre, m.Duracion_Horas 
                           FROM Modulo m 
                           JOIN Curso_Modulo cm ON m.ID_Modulo = cm.ID_Modulo 
                           WHERE cm.ID_Curso = ?";
            $stmtModulos = $conn->prepare($sqlModulos);
            $stmtModulos->bind_param("s", $curso['ID_Curso']);
            $stmtModulos->execute();
            $resultModulos = $stmtModulos->get_result();

            while ($modulo = $resultModulos->fetch_assoc()) {
                $modulo['unidades'] = [];

                // Obtener unidades formativas del módulo
                $sqlUnidades = "SELECT ID_Unidad_Formativa, Nombre, Duracion_Unidad 
                                FROM Unidad_Formativa 
                                WHERE ID_Modulo = ?";
                $stmtUnidades = $conn->prepare($sqlUnidades);
                $stmtUnidades->bind_param("s", $modulo['ID_Modulo']);
                $stmtUnidades->execute();
                $resultUnidades = $stmtUnidades->get_result();

                while ($unidad = $resultUnidades->fetch_assoc()) {
                    $unidad['Nombre'] = mb_convert_encoding(trim($unidad['Nombre']), 'UTF-8', 'ISO-8859-1');
                    $modulo['unidades'][] = $unidad;
                }
                $stmtUnidades->close();

                $curso['modulos'][] = $modulo;
            }
            $stmtModulos->close();

            $cursosData[] = $curso;
        }

        return $cursosData;
    }
}
