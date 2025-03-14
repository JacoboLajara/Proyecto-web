-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS Centro_Formacion;
USE Centro_Formacion;

-- Crear el usuario y asignación de privilegios
CREATE USER IF NOT EXISTS 'centro_user'@'localhost' IDENTIFIED BY 'centro_password';
GRANT ALL PRIVILEGES ON Centro_Formacion.* TO 'centro_user'@'localhost';

-- Crear la tabla Rol
CREATE TABLE Rol (
    ID_Rol INT PRIMARY KEY AUTO_INCREMENT,
    Nombre VARCHAR(50) UNIQUE NOT NULL  -- Puede ser 'Administrador', 'Alumno', 'Profesor', etc.
);

-- Crear la tabla Usuario
CREATE TABLE Usuario (
    ID_Usuario INT PRIMARY KEY AUTO_INCREMENT,
    DNI_NIE VARCHAR(9) UNIQUE NOT NULL,
    password VARCHAR(150) NULL,  -- Solo se usa si es un Administrador
    Fecha_Creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ID_Rol INT NOT NULL,  -- Relacionamos con la tabla `Rol`
    FOREIGN KEY (ID_Rol) REFERENCES Rol(ID_Rol) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Crear la tabla Usuario_Rol
CREATE TABLE Usuario_Rol (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    DNI_NIE VARCHAR(9) NOT NULL,
    ID_Rol INT NOT NULL,
    FOREIGN KEY (DNI_NIE) REFERENCES Usuario(DNI_NIE) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ID_Rol) REFERENCES Rol(ID_Rol) ON DELETE CASCADE ON UPDATE CASCADE
);


-- Crear la tabla Alumno
CREATE TABLE Alumno (
    ID_Alumno VARCHAR(9) PRIMARY KEY ,
    ID_Usuario INT,
    Nombre VARCHAR(50),
    Apellido1 VARCHAR(50),
	Apellido2 VARCHAR(50),
    Direccion VARCHAR(100),
    Poblacion VARCHAR(50),
	Provincia VARCHAR(50),
    Codigo_Postal VARCHAR(10),
    Fecha_Nacimiento DATE,
    Nivel_Estudios VARCHAR(50),
    Fecha_Alta DATE,
    Fecha_Baja DATE,
    Telefono VARCHAR(15),
    Email VARCHAR(50),
    FOREIGN KEY (ID_Usuario) REFERENCES Rol(ID_Rol)
);

-- Crear la tabla Profesor
CREATE TABLE Profesor (
    ID_Profesor VARCHAR(9) PRIMARY KEY,
    ID_Usuario INT,
    Nombre VARCHAR(50),
    Apellido1 VARCHAR(50),
	Apellido2 VARCHAR(50),
    Direccion VARCHAR(100),
    Poblacion VARCHAR(50),
	Provincia VARCHAR(50),
    Codigo_Postal VARCHAR(10),
    Fecha_Nacimiento DATE,
    Nivel_Estudios VARCHAR(50),
    Fecha_Alta DATE,
    Fecha_Baja DATE,
    Telefono VARCHAR(15),
    Email VARCHAR(50),
    Especialidad VARCHAR(100),
    FOREIGN KEY (ID_Usuario) REFERENCES Rol(ID_Rol)
);

-- Crear la tabla Personal_No_Docente
CREATE TABLE Personal_No_Docente (
    ID_Personal VARCHAR(9) PRIMARY KEY,
    ID_Usuario INT,
    Nombre VARCHAR(50),
    Apellido1 VARCHAR(50),
	Apellido2 VARCHAR(50),
    Direccion VARCHAR(100),
    Poblacion VARCHAR(50),
	Provincia VARCHAR(50),
    Codigo_Postal VARCHAR(10),
    Fecha_Nacimiento DATE,
    Nivel_Estudios VARCHAR(50),
    Fecha_Alta DATE,
    Fecha_Baja DATE,
    Telefono VARCHAR(15),
    Email VARCHAR(50),
    FOREIGN KEY (ID_Usuario) REFERENCES Rol(ID_Rol)
);

-- Crear la tabla Aula
CREATE TABLE Aula (
    ID_Aula INT PRIMARY KEY AUTO_INCREMENT,
    Capacidad INT,
    Nombre VARCHAR(50)
);

-- Crear la tabla Horario
CREATE TABLE Horario (
    ID_Horario INT PRIMARY KEY AUTO_INCREMENT,
    Día VARCHAR(15),
    Hora_Inicio TIME,
    Hora_Fin TIME
);

-- Crear la tabla Curso
CREATE TABLE Curso (
    ID_Curso VARCHAR(12) PRIMARY KEY,
    Nombre VARCHAR(100),
    Tipo ENUM('Oficial', 'Privado'),
	Tipo_cuota ENUM ('Hora', 'Mensual', 'Total','Gratuito'),
    Duracion_Horas INT,
    Precio_Curso DECIMAL(10, 2)
    
);

-- Crear la tabla Modulo (sin la referencia directa a Curso)
CREATE TABLE Modulo (
    ID_Modulo VARCHAR(10) PRIMARY KEY,
    Nombre VARCHAR(100),
    Duracion_Horas INT
);

-- Nueva tabla intermedia Curso_Modulo para la relación muchos a muchos
CREATE TABLE Curso_Modulo (
    ID_Curso VARCHAR(12),
    ID_Modulo VARCHAR(10),
    PRIMARY KEY (ID_Curso, ID_Modulo),
    FOREIGN KEY (ID_Curso) REFERENCES Curso(ID_Curso) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ID_Modulo) REFERENCES Modulo(ID_Modulo) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Alumno_Curso (
    ID_Alumno VARCHAR(9),
    ID_Curso VARCHAR(12),
    Fecha_Matricula DATE NOT NULL,
    Estado ENUM('Activo', 'Baja', 'Finalizado') NOT NULL DEFAULT 'Activo',
    PRIMARY KEY (ID_Alumno, ID_Curso),
    FOREIGN KEY (ID_Alumno) REFERENCES Alumno(ID_Alumno) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ID_Curso) REFERENCES Curso(ID_Curso) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE profesor_curso (
    ID_Profesor VARCHAR(9),
    ID_Curso VARCHAR(12),
    Fecha_Matricula DATE NOT NULL,
    Estado ENUM('Activo', 'Baja', 'Finalizado') NOT NULL DEFAULT 'Activo',
    PRIMARY KEY (ID_Profesor, ID_Curso),
    FOREIGN KEY (ID_Profesor) REFERENCES profesor(ID_Profesor) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ID_Curso) REFERENCES Curso(ID_Curso) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Crear la tabla Unidad_Formativa (para módulos de cursos oficiales)
CREATE TABLE Unidad_Formativa (
    ID_Unidad_Formativa VARCHAR(6) PRIMARY KEY ,
    Nombre VARCHAR(100),
    ID_Modulo VARCHAR(10),
	Duracion_Unidad INT,
    FOREIGN KEY (ID_Modulo) REFERENCES Modulo(ID_Modulo)
);

-- Crear la tabla Asignatura (para cursos privados)
CREATE TABLE Asignatura (
    ID_Asignatura VARCHAR(10) PRIMARY KEY ,
    Nombre VARCHAR(100),
    ID_Curso VARCHAR(12),
    FOREIGN KEY (ID_Curso) REFERENCES Curso(ID_Curso)
);

-- Crear la tabla Recibo
CREATE TABLE Recibo (
    ID_Recibo INT PRIMARY KEY AUTO_INCREMENT,
    ID_Alumno VARCHAR(9),
    ID_Curso VARCHAR(12),
    Importe DECIMAL(10,2),
    Fecha_Emision DATE,
    Fecha_Pago DATE,  -- Columna para registrar la fecha de pago
    Periodo VARCHAR(7) NOT NULL,  -- Por ejemplo, '2023-01'
    Estado ENUM('Pendiente', 'Cobrado') DEFAULT 'Pendiente',
    FOREIGN KEY (ID_Alumno) REFERENCES Alumno(ID_Alumno),
    FOREIGN KEY (ID_Curso) REFERENCES Curso(ID_Curso)
);

CREATE TABLE Nota (
    ID_Nota INT PRIMARY KEY AUTO_INCREMENT,
    ID_Alumno VARCHAR(9) NOT NULL,
    ID_Curso VARCHAR(12) NOT NULL,
    ID_Modulo VARCHAR(10) NULL,
    ID_Unidad_Formativa VARCHAR(6) NULL,
    Tipo_Nota ENUM('Modulo', 'Unidad_Formativa') NOT NULL,
    Calificación DECIMAL(4, 2) NOT NULL,
    Fecha_Registro DATE DEFAULT (CURDATE()),
    FOREIGN KEY (ID_Alumno) REFERENCES Alumno(ID_Alumno) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ID_Curso) REFERENCES Curso(ID_Curso) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ID_Modulo) REFERENCES Modulo(ID_Modulo) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ID_Unidad_Formativa) REFERENCES Unidad_Formativa(ID_Unidad_Formativa) ON DELETE CASCADE ON UPDATE CASCADE
);



-- Crear la tabla Diploma_Certificado
CREATE TABLE Diploma_Certificado (
    ID_Diploma INT PRIMARY KEY AUTO_INCREMENT,
    Tipo ENUM('Diploma', 'Certificado'),
    ID_Alumno VARCHAR(9),
    ID_Curso VARCHAR(12),
    Fecha_Emisión DATE,
    FOREIGN KEY (ID_Alumno) REFERENCES Alumno(ID_Alumno),
    FOREIGN KEY (ID_Curso) REFERENCES Curso(ID_Curso)
);


-- Crear tabla Notificacion para Alumnos
CREATE TABLE Notificacion_Alumno (
    ID_Notificacion INT PRIMARY KEY AUTO_INCREMENT,
    ID_Alumno VARCHAR(9) NOT NULL,
    Mensaje TEXT NOT NULL,
    Fecha_Envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Alumno) REFERENCES Alumno(ID_Alumno) ON DELETE CASCADE
);

-- Crear tabla Notificacion para Profesores
CREATE TABLE Notificacion_Profesor (
    ID_Notificacion INT PRIMARY KEY AUTO_INCREMENT,
    ID_Profesor VARCHAR(9) NOT NULL,
    Mensaje TEXT NOT NULL,
    Fecha_Envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Profesor) REFERENCES Profesor(ID_Profesor) ON DELETE CASCADE
);

-- Crear tabla Notificacion para Personal No Docente
CREATE TABLE Notificacion_Personal (
    ID_Notificacion INT PRIMARY KEY AUTO_INCREMENT,
    ID_Personal VARCHAR(9) NOT NULL,
    Mensaje TEXT NOT NULL,
    Fecha_Envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Personal) REFERENCES Personal_No_Docente(ID_Personal) ON DELETE CASCADE
);

-- Opcional: Crear tabla Notificacion_Curso si deseas enviar notificaciones específicas por curso
CREATE TABLE Notificacion_Curso (
    ID_Notificacion INT PRIMARY KEY AUTO_INCREMENT,
    ID_Curso VARCHAR(12) NOT NULL,
    Mensaje TEXT NOT NULL,
    Fecha_Envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Curso) REFERENCES Curso(ID_Curso) ON DELETE CASCADE
);



-- Crear la tabla Asignacion_Horario para asignar horarios a aulas y cursos
CREATE TABLE Asignacion_Horario (
    ID_Asignacion INT PRIMARY KEY AUTO_INCREMENT,
    ID_Aula INT,
    ID_Curso VARCHAR(12),
    Dia VARCHAR(15),
    Hora_Inicio TIME,
    Hora_Fin TIME,
    Tarde_Inicio TIME,
    Tarde_Fin TIME,
    FOREIGN KEY (ID_Aula) REFERENCES Aula(ID_Aula),
    FOREIGN KEY (ID_Curso) REFERENCES Curso(ID_Curso)
);



-- Aplicación de los privilegios
FLUSH PRIVILEGES;

DELIMITER //
CREATE TRIGGER evitar_solapamiento_horarios_insert
BEFORE INSERT ON Asignacion_Horario
FOR EACH ROW
BEGIN
    DECLARE solapado INT;

    -- Verifica si el nuevo horario se solapa con alguno existente en el mismo aula y día
    SET solapado = (
        SELECT COUNT(*)
        FROM Asignacion_Horario
        WHERE ID_Aula = NEW.ID_Aula
          AND Dia = NEW.Dia
          AND (
              (NEW.Hora_Inicio >= Hora_Inicio AND NEW.Hora_Inicio < Hora_Fin)
              OR (NEW.Hora_Fin > Hora_Inicio AND NEW.Hora_Fin <= Hora_Fin)
              OR (Hora_Inicio >= NEW.Hora_Inicio AND Hora_Inicio < NEW.Hora_Fin)
              OR (Hora_Fin > NEW.Hora_Inicio AND Hora_Fin <= NEW.Hora_Fin)
          )
    );

    -- Si encuentra un solapamiento, lanza un error
    IF solapado > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = '❌ Error: Solapamiento de horarios en el aula (INSERT).';
    END IF;
END;
//
DELIMITER ;

DELIMITER //
CREATE TRIGGER evitar_solapamiento_horarios_update
BEFORE UPDATE ON Asignacion_Horario
FOR EACH ROW
BEGIN
    DECLARE solapado INT;

    -- Verifica si el horario actualizado se solapa con otro existente en la misma aula y día, excluyendo la fila actual
    SET solapado = (
        SELECT COUNT(*)
        FROM Asignacion_Horario
        WHERE ID_Aula = NEW.ID_Aula
          AND Dia = NEW.Dia
          AND ID_Asignacion <> OLD.ID_Asignacion  -- Excluir la fila que se está modificando
          AND (
              (NEW.Hora_Inicio >= Hora_Inicio AND NEW.Hora_Inicio < Hora_Fin)
              OR (NEW.Hora_Fin > Hora_Inicio AND NEW.Hora_Fin <= Hora_Fin)
              OR (Hora_Inicio >= NEW.Hora_Inicio AND Hora_Inicio < NEW.Hora_Fin)
              OR (Hora_Fin > NEW.Hora_Inicio AND Hora_Fin <= NEW.Hora_Fin)
          )
    );

    -- Si hay solapamiento, se lanza un error
    IF solapado > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = '❌ Error: Solapamiento de horarios en el aula (UPDATE).';
    END IF;
END;
//
DELIMITER ;




DELIMITER //
CREATE TRIGGER after_insert_alumno
AFTER INSERT ON Alumno
FOR EACH ROW
BEGIN
    DECLARE user_id INT;
    DECLARE rol_id INT;

    -- Obtener el ID del rol 'Alumno' desde la tabla Rol
    SELECT ID_Rol INTO rol_id FROM Rol WHERE Nombre = 'Alumno';

    -- Si el usuario no existe en Usuario, lo creamos con password NULL
    IF NOT EXISTS (SELECT 1 FROM Usuario WHERE DNI_NIE = NEW.ID_Alumno) THEN
        INSERT INTO Usuario (DNI_NIE, password, Fecha_Creacion, ID_Rol)
        VALUES (NEW.ID_Alumno, NULL, NOW(), rol_id);
    END IF;

    -- Obtener el ID_Usuario generado o existente
    SELECT ID_Usuario INTO user_id FROM Usuario WHERE DNI_NIE = NEW.ID_Alumno;

    -- Asignar el rol al usuario si aún no lo tiene
    INSERT IGNORE INTO Usuario_Rol (DNI_NIE, ID_Rol)
    VALUES (NEW.ID_Alumno, rol_id);
END;
//
DELIMITER ;


DELIMITER //
CREATE TRIGGER after_insert_profesor
AFTER INSERT ON Profesor
FOR EACH ROW
BEGIN
    DECLARE user_id INT;
    DECLARE rol_id INT;

    -- Obtener el ID del rol 'Profesor'
    SELECT ID_Rol INTO rol_id FROM Rol WHERE Nombre = 'Profesor';

    -- Si el usuario no existe, se inserta en Usuario con password NULL
    IF NOT EXISTS (SELECT 1 FROM Usuario WHERE DNI_NIE = NEW.ID_Profesor) THEN
        INSERT INTO Usuario (DNI_NIE, password, Fecha_Creacion, ID_Rol)
        VALUES (NEW.ID_Profesor, NULL, NOW(), rol_id);
    END IF;

    -- Obtener el ID_Usuario
    SELECT ID_Usuario INTO user_id FROM Usuario WHERE DNI_NIE = NEW.ID_Profesor;

    -- Insertar en Usuario_Rol si aún no tiene el rol
    INSERT IGNORE INTO Usuario_Rol (DNI_NIE, ID_Rol)
    VALUES (NEW.ID_Profesor, rol_id);
END;
//
DELIMITER ;

DELIMITER //
CREATE TRIGGER after_insert_personal
AFTER INSERT ON Personal_No_Docente
FOR EACH ROW
BEGIN
    DECLARE user_id INT;
    DECLARE rol_id INT;

    -- Obtener el ID del rol 'Personal_No_Docente'
    SELECT ID_Rol INTO rol_id FROM Rol WHERE Nombre = 'Personal_No_Docente';

    -- Si el usuario no existe en Usuario, lo insertamos con password NULL
    IF NOT EXISTS (SELECT 1 FROM Usuario WHERE DNI_NIE = NEW.ID_Personal) THEN
        INSERT INTO Usuario (DNI_NIE, password, Fecha_Creacion, ID_Rol)
        VALUES (NEW.ID_Personal, NULL, NOW(), rol_id);
    END IF;

    -- Obtener el ID_Usuario
    SELECT ID_Usuario INTO user_id FROM Usuario WHERE DNI_NIE = NEW.ID_Personal;

    -- Asignar el rol al usuario si aún no lo tiene
    INSERT IGNORE INTO Usuario_Rol (DNI_NIE, ID_Rol)
    VALUES (NEW.ID_Personal, rol_id);
END;
//
DELIMITER ;

DELIMITER //

CREATE EVENT IF NOT EXISTS generar_recibos_mensuales
ON SCHEDULE EVERY 1 MONTH
STARTS '2023-01-01 00:00:00'
DO
BEGIN
    -- Insertar recibos para cada alumno activo
    INSERT INTO Recibo (ID_Alumno, ID_Curso, Importe, Fecha_Emision, Periodo, Estado)
    SELECT a.ID_Alumno, ac.ID_Curso, c.Precio_Curso, CURDATE(), DATE_FORMAT(CURDATE(), '%Y-%m'), 'Pendiente'
    FROM Alumno a
    JOIN Alumno_Curso ac ON a.ID_Alumno = ac.ID_Alumno
    JOIN Curso c ON ac.ID_Curso = c.ID_Curso
    WHERE a.Fecha_Alta IS NOT NULL
      AND (a.Fecha_Baja IS NULL OR a.Fecha_Baja = '')
      -- Evitar duplicados: solo insertar si para el alumno y el mes no existe ya un recibo
      AND NOT EXISTS (
          SELECT 1 FROM Recibo r
          WHERE r.ID_Alumno = a.ID_Alumno
            AND r.Periodo = DATE_FORMAT(CURDATE(), '%Y-%m')
      );
END;
//
DELIMITER ;


