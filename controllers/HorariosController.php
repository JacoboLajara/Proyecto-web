<?php
require_once __DIR__ . '/../models/HorariosModel.php';

/**
 * Controlador para la gestión de horarios.
 *
 * Este controlador se encarga de manejar la interacción entre la vista y el modelo para la asignación
 * y consulta de horarios en aulas para cursos. Permite cargar la vista de horarios, obtener listas de aulas
 * y cursos, asignar horarios y consultar los horarios ocupados.
 *
 * @package YourPackageName
 */
class HorariosController
{
    /**
     * Instancia del modelo de horarios.
     *
     * @var HorariosModel
     */
    private $model;

    /**
     * Constructor del controlador.
     *
     * Inicializa la instancia del modelo de horarios.
     */
    public function __construct()
    {
        $this->model = new HorariosModel();
    }

    /**
     * Muestra la vista para la gestión de horarios.
     *
     * Obtiene las aulas y cursos del modelo y carga la vista correspondiente para la asignación de horarios.
     *
     * @return void
     */
    public function create()
    {
        $model = new HorariosModel();
        $aulas = $model->getAulas();  // Obtener aulas desde el modelo
        $cursos = $model->getCursos(); // Obtener cursos desde el modelo
        require_once __DIR__ . '/../views/users/horarios.php';  // Cargar la vista de horarios
    }

    /**
     * Obtiene todas las aulas.
     *
     * Llama al modelo para obtener la lista de aulas.
     *
     * @return array Lista de aulas en formato asociativo.
     */
    public function getAulas()
    {
        return $this->model->getAulas();
    }

    /**
     * Obtiene todos los cursos.
     *
     * Llama al modelo para obtener la lista de cursos.
     *
     * @return array Lista de cursos en formato asociativo.
     */
    public function getCursos()
    {
        return $this->model->getCursos();
    }

    /**
     * Asigna horarios a un aula para un curso.
     *
     * Lee los datos enviados en formato JSON desde la solicitud HTTP, los cuales deben incluir
     * el identificador del aula, el curso y un array de horarios. Para cada horario, llama al método
     * del modelo para asignar el horario. Devuelve una respuesta en formato JSON indicando el éxito
     * o fallo de la operación.
     *
     * @return void
     */

    /*public function storeHorario()
    {
        header("Content-Type: application/json");

        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            echo json_encode(["success" => false, "message" => "❌ Datos no recibidos correctamente."]);
            return;
        }

        $aula = $data["aula"];
        $curso = $data["curso"];
        $horarios = $data["horarios"];
        $conn = $this->model->getConnection();

        // 🔹 **Eliminar horarios si se enviaron en `horariosEliminar`**
        if (!empty($data["horariosEliminar"])) {
            foreach ($data["horariosEliminar"] as $horarioEliminar) {
                $dia = $horarioEliminar["dia"];
                $tipo = $horarioEliminar["tipo"];

                if ($tipo === "mañana") {
                    $queryDelete = "UPDATE Asignacion_Horario SET Hora_Inicio = NULL, Hora_Fin = NULL WHERE ID_Aula = ? AND ID_Curso = ? AND Dia = ?";
                } else {
                    $queryDelete = "UPDATE Asignacion_Horario SET Tarde_Inicio = NULL, Tarde_Fin = NULL WHERE ID_Aula = ? AND ID_Curso = ? AND Dia = ?";
                }

                $stmtDelete = $conn->prepare($queryDelete);
                $stmtDelete->bind_param("sss", $aula, $curso, $dia);
                $stmtDelete->execute();
            }
        }

        // 🔹 **Procesar horarios nuevos o actualizados**
        foreach ($horarios as $horario) {
            $dia = $horario["dia"];
            $hora_inicio = isset($horario["hora_inicio"]) ? $horario["hora_inicio"] : null;
            $hora_fin = isset($horario["hora_fin"]) ? $horario["hora_fin"] : null;
            $tipo = $horario["tipo"];

            // 📌 **Comprobar si el horario ya existe**
            $queryCheck = "SELECT COUNT(*) FROM Asignacion_Horario WHERE ID_Aula = ? AND ID_Curso = ? AND Dia = ?";
            $stmtCheck = $conn->prepare($queryCheck);
            $stmtCheck->bind_param("sss", $aula, $curso, $dia);
            $stmtCheck->execute();
            $stmtCheck->bind_result($count);
            $stmtCheck->fetch();
            $stmtCheck->close();

            if ($count > 0) {
                // 🔄 **Actualizar horario existente**
                if ($tipo === "mañana") {
                    $queryUpdate = "UPDATE Asignacion_Horario SET Hora_Inicio = ?, Hora_Fin = ? WHERE ID_Aula = ? AND ID_Curso = ? AND Dia = ?";
                } else {
                    $queryUpdate = "UPDATE Asignacion_Horario SET Tarde_Inicio = ?, Tarde_Fin = ? WHERE ID_Aula = ? AND ID_Curso = ? AND Dia = ?";
                }

                $stmtUpdate = $conn->prepare($queryUpdate);
                $stmtUpdate->bind_param("sssss", $hora_inicio, $hora_fin, $aula, $curso, $dia);
                $stmtUpdate->execute();
            } else {
                // 🔹 **Insertar nuevo horario**
                if ($tipo === "mañana") {
                    $queryInsert = "INSERT INTO Asignacion_Horario (ID_Aula, ID_Curso, Dia, Hora_Inicio, Hora_Fin) VALUES (?, ?, ?, ?, ?)";
                } else {
                    $queryInsert = "INSERT INTO Asignacion_Horario (ID_Aula, ID_Curso, Dia, Tarde_Inicio, Tarde_Fin) VALUES (?, ?, ?, ?, ?)";
                }

                $stmtInsert = $conn->prepare($queryInsert);
                $stmtInsert->bind_param("sssss", $aula, $curso, $dia, $hora_inicio, $hora_fin);
                $stmtInsert->execute();
            }
        }

        echo json_encode(["success" => true, "message" => "✅ Horarios actualizados correctamente."]);
    }*/


    public function storeHorario()
    {
        header("Content-Type: application/json");

        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            echo json_encode(["success" => false, "message" => "❌ Datos no recibidos correctamente."]);
            return;
        }

        $aula = $data["aula"];
        $curso = $data["curso"];
        $horarios = $data["horarios"];
        $conn = $this->model->getConnection();

        try {
            // 🔹 **Eliminar horarios si se enviaron en `horariosEliminar`**
            if (!empty($data["horariosEliminar"])) {
                foreach ($data["horariosEliminar"] as $horarioEliminar) {
                    $dia = $horarioEliminar["dia"];
                    $tipo = $horarioEliminar["tipo"];

                    if ($tipo === "mañana") {
                        $queryDelete = "UPDATE Asignacion_Horario SET Hora_Inicio = NULL, Hora_Fin = NULL WHERE ID_Aula = ? AND ID_Curso = ? AND Dia = ?";
                    } else {
                        $queryDelete = "UPDATE Asignacion_Horario SET Tarde_Inicio = NULL, Tarde_Fin = NULL WHERE ID_Aula = ? AND ID_Curso = ? AND Dia = ?";
                    }

                    $stmtDelete = $conn->prepare($queryDelete);
                    $stmtDelete->bind_param("sss", $aula, $curso, $dia);
                    $stmtDelete->execute();
                }
            }

            // 🔹 **Procesar horarios nuevos o actualizados**
            foreach ($horarios as $horario) {
                $dia = $horario["dia"];
                $hora_inicio = isset($horario["hora_inicio"]) ? $horario["hora_inicio"] : null;
                $hora_fin = isset($horario["hora_fin"]) ? $horario["hora_fin"] : null;
                $tipo = $horario["tipo"];

                // 📌 **Comprobar si el horario ya existe**
                $queryCheck = "SELECT COUNT(*) FROM Asignacion_Horario WHERE ID_Aula = ? AND ID_Curso = ? AND Dia = ?";
                $stmtCheck = $conn->prepare($queryCheck);
                $stmtCheck->bind_param("sss", $aula, $curso, $dia);
                $stmtCheck->execute();
                $stmtCheck->bind_result($count);
                $stmtCheck->fetch();
                $stmtCheck->close();

                if ($count > 0) {
                    // 🔄 **Actualizar horario existente**
                    if ($tipo === "mañana") {
                        $queryUpdate = "UPDATE Asignacion_Horario SET Hora_Inicio = ?, Hora_Fin = ? WHERE ID_Aula = ? AND ID_Curso = ? AND Dia = ?";
                    } else {
                        $queryUpdate = "UPDATE Asignacion_Horario SET Tarde_Inicio = ?, Tarde_Fin = ? WHERE ID_Aula = ? AND ID_Curso = ? AND Dia = ?";
                    }

                    $stmtUpdate = $conn->prepare($queryUpdate);
                    $stmtUpdate->bind_param("sssss", $hora_inicio, $hora_fin, $aula, $curso, $dia);
                    $stmtUpdate->execute();
                } else {
                    // 🔹 **Insertar nuevo horario**
                    if ($tipo === "mañana") {
                        $queryInsert = "INSERT INTO Asignacion_Horario (ID_Aula, ID_Curso, Dia, Hora_Inicio, Hora_Fin) VALUES (?, ?, ?, ?, ?)";
                    } else {
                        $queryInsert = "INSERT INTO Asignacion_Horario (ID_Aula, ID_Curso, Dia, Tarde_Inicio, Tarde_Fin) VALUES (?, ?, ?, ?, ?)";
                    }

                    $stmtInsert = $conn->prepare($queryInsert);
                    $stmtInsert->bind_param("sssss", $aula, $curso, $dia, $hora_inicio, $hora_fin);
                    $stmtInsert->execute();
                }
            }

            echo json_encode(["success" => true, "message" => "✅ Horarios actualizados correctamente."]);

        } catch (mysqli_sql_exception $e) {
            // 📌 **Capturar error del trigger de solapamiento**
            if (strpos($e->getMessage(), 'Error: Solapamiento de horarios') !== false) {
                echo json_encode(["success" => false, "message" => "❌ El aula ya está ocupada en este horario. Consulte disponibilidad."]);
            } else {
                echo json_encode(["success" => false, "message" => "❌ Error en la base de datos: " . $e->getMessage()]);
            }
        }
    }








    /**
     * Obtiene los horarios ocupados de un aula.
     *
     * Verifica que se haya especificado un aula mediante la variable GET y llama al modelo para obtener
     * los horarios ocupados de dicha aula. Devuelve la información en formato JSON.
     *
     * @return void
     */
    public function getHorariosOcupados()
    {
        header("Content-Type: application/json");

        if (!isset($_GET['aula']) || empty($_GET['aula'])) {
            echo json_encode(["success" => false, "message" => "❌ Aula no especificada."]);
            return;
        }

        $aulaId = $_GET['aula'];
        $conn = $this->model->getConnection();

        $query = "SELECT Dia, 
                     Hora_Inicio, Hora_Fin, 
                     Tarde_Inicio, Tarde_Fin, 
                     c.Nombre AS Curso
              FROM Asignacion_Horario ah
              INNER JOIN Curso c ON ah.ID_Curso = c.ID_Curso
              WHERE ah.ID_Aula = ?
              ORDER BY FIELD(Dia, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'), Hora_Inicio";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $aulaId);
        $stmt->execute();
        $result = $stmt->get_result();

        $horarios = $result->fetch_all(MYSQLI_ASSOC);

        // 🟢 DEBUG: Verificar que la consulta devuelve los horarios correctamente
        error_log("📌 getHorariosOcupados - Resultado: " . print_r($horarios, true));


        echo json_encode(["success" => true, "horarios" => $horarios]);
    }


    /**
     * Obtiene los horarios asignados a un curso específico.
     *
     * @return void
     */
    /**
     * Obtiene los horarios asignados a un curso específico en una aula específica.
     *
     * @return void
     */
    public function getHorariosPorCurso()
    {
        header("Content-Type: application/json");

        if (!isset($_GET['aula']) || !isset($_GET['curso'])) {
            echo json_encode(["success" => false, "message" => "❌ Aula o curso no especificado."]);
            return;
        }

        $aulaId = $_GET['aula'];
        $cursoId = $_GET['curso'];

        // 🟢 Obtener la conexión desde el modelo
        $conn = $this->model->getConnection();

        $query = "SELECT Dia, 
                     Hora_Inicio, Hora_Fin, 
                     Tarde_Inicio, Tarde_Fin 
              FROM Asignacion_Horario 
              WHERE ID_Aula = ? AND ID_Curso = ? 
              ORDER BY FIELD(Dia, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo')";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $aulaId, $cursoId);
        $stmt->execute();
        $result = $stmt->get_result();

        $horarios = $result->fetch_all(MYSQLI_ASSOC);

        // 🟢 DEBUG: Ver qué devuelve la consulta SQL
        error_log("📌 getHorariosPorCurso - Resultado: " . print_r($horarios, true));

        echo json_encode(["success" => true, "horarios" => $horarios]);
    }






}
