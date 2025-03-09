<?php
/**
 * Modelo para la gestión de usuarios.
 *
 * Esta clase se encarga de interactuar con la base de datos para realizar operaciones relacionadas con los usuarios,
 * tales como obtener usuarios, buscar un usuario por su DNI/NIE, actualizar o eliminar contraseñas y obtener la lista de usuarios
 * junto con sus roles.
 *
 * @package YourPackageName
 */
class UserModel
{
    /**
     * Conexión a la base de datos.
     *
     * @var mysqli
     */
    private $conn;

    /**
     * Constructor del modelo.
     *
     * Incluye el archivo de conexión y establece la conexión a la base de datos.
     * Si la conexión falla, termina la ejecución del script.
     *
     * @return void
     */
    public function __construct()
    {
        // Incluir la conexión a la base de datos
        $this->conn = include __DIR__ . '/../config/conexion.php';
        if (!$this->conn) {
            die("Error: La conexión no se pudo establecer correctamente.");
        }
    }

    /**
     * Obtiene todos los usuarios.
     *
     * Ejecuta una consulta para obtener el DNI/NIE de todos los usuarios registrados en la tabla "usuario".
     *
     * @return array Un arreglo asociativo con los usuarios.
     */
    public function getAllUsers()
    {
        $sql = "SELECT DNI_NIE FROM usuario";
        $result = $this->conn->query($sql);

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        return $users;
    }

    /**
     * Busca un usuario por DNI/NIE.
     *
     * Ejecuta una consulta preparada para obtener el DNI_NIE y la contraseña del usuario
     * cuyo DNI coincide con el parámetro proporcionado.
     *
     * @param string $dni El DNI/NIE del usuario a buscar.
     * @return array|null Un arreglo asociativo con los datos del usuario o null si no se encuentra.
     */
    public function getUserByDNI($dni)
    {
        $sql = "SELECT DNI_NIE, password FROM Usuario WHERE DNI_NIE = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->conn->error);
        }

        $stmt->bind_param("s", $dni);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Actualiza la contraseña de un usuario.
     *
     * Realiza el hash de la nueva contraseña utilizando PASSWORD_BCRYPT y actualiza la contraseña
     * del usuario identificado por el DNI/NIE.
     *
     * @param string $dni El DNI/NIE del usuario.
     * @param string $newPassword La nueva contraseña a actualizar.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function updatePassword($dni, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE Usuario SET password = ? WHERE DNI_NIE = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->conn->error);
        }

        $stmt->bind_param("ss", $hashedPassword, $dni);
        return $stmt->execute();
    }

    /**
     * Elimina la contraseña de un usuario.
     *
     * Actualiza la contraseña del usuario identificado por su DNI_NIE, estableciéndola a NULL.
     *
     * @param string $dni El DNI/NIE del usuario.
     * @return bool True si la operación fue exitosa, false en caso contrario.
     */
    public function deletePassword($dni)
    {
        $sql = "UPDATE Usuario SET password = NULL WHERE DNI_NIE = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->conn->error);
        }

        $stmt->bind_param("s", $dni);
        return $stmt->execute();
    }

    /**
     * Obtiene todos los usuarios junto con sus roles.
     *
     * Realiza una consulta que une la tabla "Usuario" con las tablas "Alumno", "Profesor" y "Personal_No_Docente"
     * (usando LEFT JOIN para cada una) para obtener el nombre, apellidos y rol de cada usuario.
     * Se utiliza COALESCE para obtener el nombre y apellidos de la tabla que contenga datos.
     *
     * @return array Un arreglo asociativo con los datos de los usuarios y sus roles.
     */
    public function getAllUsersWithRoles()
    {
        $sql = "
            SELECT u.DNI_NIE, 
               COALESCE(a.Nombre, p.Nombre, pn.Nombre) AS Nombre, 
               COALESCE(a.Apellido1, p.Apellido1, pn.Apellido1) AS Apellido1, 
               COALESCE(a.Apellido2, p.Apellido2, pn.Apellido2) AS Apellido2, 
               r.Nombre AS Rol
            FROM Usuario u
            LEFT JOIN Alumno a ON u.DNI_NIE = a.ID_Alumno
            LEFT JOIN Profesor p ON u.DNI_NIE = p.ID_Profesor
            LEFT JOIN Personal_No_Docente pn ON u.DNI_NIE = pn.ID_Personal
            JOIN Rol r ON u.ID_Rol = r.ID_Rol
        ";

        $result = $this->conn->query($sql);

        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        return $usuarios;
    }
}
