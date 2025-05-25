<?php
class EncuestasModel {
    private $conn;

    public function __construct() {
        $this->conn = include __DIR__ . '/../config/conexion.php';
        if (!$this->conn) {
            die("Error: conexión no establecida.");
        }
    }

    public function guardarEncuestaCentro($data) {
        $sql = "INSERT INTO EncuestaCentro (
                    ID_Alumno, ID_Curso,
                    q1_global, q2_1_expectativas, q2_2_material, q2_3_conocimientos,
                    q2_4_equipos, q2_5_medios,
                    q3_1_info_doc, q3_2_personal_serv, q3_3_instalaciones,
                    q4_competidores,
                    comentario_q5, comentario_q6
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssiiiiiiiiiss",
            $data['id_alumno'], $data['id_curso'],
            $data['q1'], $data['q2_1'], $data['q2_2'], $data['q2_3'],
            $data['q2_4'], $data['q2_5'],
            $data['q3_1'], $data['q3_2'], $data['q3_3'],
            $data['q4'],
            $data['q5_coment'], $data['q6_coment']
        );
        return $stmt->execute();
    }

    public function guardarEncuestaProfesor($data) {
        $sql = "INSERT INTO EncuestaProfesor (
                    ID_Alumno, ID_Profesor,
                    pq1_1_claridad, pq1_2_atienen_programa, pq1_3_entusiasmo,
                    pq1_4_participativas, pq1_5_ritmo_clase, pq1_6_preparacion_clase,
                    pq1_7_comportamiento_prof,
                    comentario_pq2
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssiiiiiii s",
            $data['id_alumno'], $data['id_profesor'],
            $data['pq1_1'], $data['pq1_2'], $data['pq1_3'],
            $data['pq1_4'], $data['pq1_5'], $data['pq1_6'],
            $data['pq1_7'], $data['pq2_coment']
        );
        return $stmt->execute();
    }

    /**
     * Obtiene el curso y profesor activos de un alumno,
     * para precargar nombres en el formulario.
     */
    public function getDatosFormulario($idAlumno) {
        $sql = "SELECT c.ID_Curso, c.Nombre AS curso,
                       p.ID_Profesor, CONCAT(p.Nombre,' ',p.Apellido1) AS profesor
                FROM Alumno_Curso ac
                JOIN Curso c ON ac.ID_Curso = c.ID_Curso
                JOIN profesor_curso pc ON c.ID_Curso = pc.ID_Curso
                JOIN Profesor p ON pc.ID_Profesor = p.ID_Profesor
                WHERE ac.ID_Alumno = ? AND ac.Estado='Activo'
                  AND pc.Estado='Activo' LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $idAlumno);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
