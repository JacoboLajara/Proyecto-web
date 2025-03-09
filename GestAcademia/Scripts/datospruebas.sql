USE Centro_Formacion;

-- Insertar roles
INSERT INTO Rol (Nombre) VALUES ('Administrador'),('Personal_No_Docente'), ('Profesor'), ('Alumno');

-- Insertar usuarios
INSERT INTO Usuario (DNI_NIE, password, ID_Rol)
VALUES 
('00000000A', NULL, 1), -- Administrador
('87654321B', NULL, 4), -- Alumno
('22334455E', NULL, 4), -- Alumno
('66778899F', NULL, 4), -- Alumno
('11223344C', NULL, 3), -- Profesor
('33445566G', NULL, 3), -- Profesor
('77889900H', NULL, 3), -- Profesor
('55667788D', NULL, 2), -- Personal No Docente
('88990011I', NULL, 2), -- Personal No Docente
('99001122J', NULL, 2); -- Personal No Docente

-- Insertar relación Usuario_Rol
INSERT INTO Usuario_Rol (DNI_NIE, ID_Rol)
VALUES 
('00000000A', 1), 
('87654321B', 4), 
('22334455E', 4), 
('66778899F', 4), 
('11223344C', 3), 
('33445566G', 3), 
('77889900H', 3), 
('55667788D', 2), 
('88990011I', 2), 
('99001122J', 2);

-- Insertar alumnos
INSERT INTO Alumno (ID_Alumno, ID_Usuario, Nombre, Apellido1, Apellido2, Direccion, Poblacion, Provincia, Codigo_Postal, Fecha_Nacimiento, Nivel_Estudios, Fecha_Alta, Fecha_Baja, Telefono, Email)
VALUES 
('87654321B', 4, 'Juan', 'Pérez', 'García', 'Calle Falsa 123', 'Madrid', 'Madrid', '28001', '2000-05-10', 'Bachillerato', '2023-01-10', NULL, '600123456', 'juan.perez@email.com'),
('22334455E', 4, 'María', 'López', 'Fernández', 'Avenida Principal 45', 'Barcelona', 'Barcelona', '08001', '1999-08-20', 'Grado Universitario', '2023-02-15', NULL, '600654321', 'maria.lopez@email.com'),
('66778899F', 4, 'Carlos', 'Gómez', 'Sánchez', 'Plaza Mayor 10', 'Valencia', 'Valencia', '46001', '2001-12-05', 'FP Medio', '2023-03-20', NULL, '601987654', 'carlos.gomez@email.com');

-- Insertar profesores
INSERT INTO Profesor (ID_Profesor, ID_Usuario, Nombre, Apellido1, Apellido2, Direccion, Poblacion, Provincia, Codigo_Postal, Fecha_Nacimiento, Nivel_Estudios, Fecha_Alta, Fecha_Baja, Telefono, Email, Especialidad)
VALUES 
('11223344C', 3, 'Ana', 'Martínez', 'Ruiz', 'Calle Secundaria 34', 'Madrid', 'Madrid', '28002', '1985-07-15', 'Doctorado', '2022-01-15', NULL, '602123456', 'ana.martinez@email.com', 'Matemáticas'),
('33445566G', 3, 'David', 'Rodríguez', 'Pérez', 'Calle Central 78', 'Sevilla', 'Sevilla', '41002', '1978-04-22', 'Máster Universitario', '2021-09-10', NULL, '603234567', 'david.rodriguez@email.com', 'Informática'),
('77889900H', 3, 'Laura', 'Fernández', 'Gómez', 'Avenida Larga 12', 'Bilbao', 'Bizkaia', '48001', '1982-11-30', 'Licenciatura', '2020-03-25', NULL, '604345678', 'laura.fernandez@email.com', 'Física');

-- Insertar personal no docente
INSERT INTO Personal_No_Docente (ID_Personal, ID_Usuario, Nombre, Apellido1, Apellido2, Direccion, Poblacion, Provincia, Codigo_Postal, Fecha_Nacimiento, Nivel_Estudios, Fecha_Alta, Fecha_Baja, Telefono, Email)
VALUES 
('55667788D', 2, 'Pedro', 'Sánchez', 'Martín', 'Calle Trabajo 98', 'Granada', 'Granada', '18001', '1975-03-18', 'FP Superior', '2021-06-01', NULL, '605456789', 'pedro.sanchez@email.com'),
('88990011I', 2, 'Marta', 'Gómez', 'López', 'Calle Empresa 55', 'Málaga', 'Málaga', '29001', '1980-09-12', 'Bachillerato', '2020-11-10', NULL, '606567890', 'marta.gomez@email.com'),
('99001122J', 2, 'Luis', 'Ruiz', 'González', 'Avenida Servicio 22', 'Zaragoza', 'Zaragoza', '50001', '1988-06-25', 'Grado Universitario', '2022-04-15', NULL, '607678901', 'luis.ruiz@email.com');

-- Insertar cursos
INSERT INTO Curso (ID_Curso, Nombre, Tipo, Tipo_cuota, Duracion_Horas, Precio_Curso)
VALUES 
('CUR001', 'Programación en PHP', 'Oficial', 'Mensual', 200, 500.00),
('CUR002', 'Redes Informáticas', 'Privado', 'Total', 150, 300.00),
('CUR003', 'Diseño Web con HTML y CSS', 'Oficial', 'Gratuito', 100, 0.00);

-- Insertar relación Alumno-Curso
INSERT INTO Alumno_Curso (ID_Alumno, ID_Curso, Fecha_Matricula, Estado)
VALUES 
('87654321B', 'CUR001', '2024-01-01', 'Activo'),
('22334455E', 'CUR002', '2024-01-02', 'Activo'),
('66778899F', 'CUR003', '2024-01-03', 'Activo');

-- Insertar módulos
INSERT INTO Modulo (ID_Modulo, Nombre, Duracion_Horas)
VALUES ('MOD001', 'Introducción a PHP', 40), ('MOD002', 'Bases de Datos', 60), ('MOD003', 'JavaScript Avanzado', 50);

-- Insertar Unidades formativas
INSERT INTO unidad_formativa (ID_Unidad_Formativa, Nombre, ID_Modulo, Duracion_Unidad)
VALUES 
('UF0001', 'Fundamentos basicos PHP', 'MOD001', 30),
('UF0002', 'Estructuras basicos PHP', 'MOD001', 50),
('UF1001', 'Introduccion SQL', 'MOD002', 30),
('UF1002', 'Consultas SQL', 'MOD002', 30),
('UF2001', 'Introduccion estructuras javascript', 'MOD003', 30),
('UF2002', 'Peticiones Ajax', 'MOD003', 30);


-- Relacionar módulos con cursos
INSERT INTO Curso_Modulo (ID_Curso, ID_Modulo)
VALUES ('CUR001', 'MOD001'), ('CUR002', 'MOD002'), ('CUR003', 'MOD003');



-- Insertar aulas de prueba
INSERT INTO Aula (Capacidad, Nombre)
VALUES 
(30, 'Aula 101 - Informática'),
(25, 'Aula 102 - Redes y Telecomunicaciones'),
(40, 'Aula 103 - Diseño y Multimedia'),
(35, 'Aula 104 - Matemáticas Aplicadas'),
(50, 'Aula 105 - Laboratorio de Física'),
(20, 'Aula 106 - Administración y Finanzas');

-- Insertar asignación de horarios
INSERT INTO Asignacion_Horario (ID_Aula, ID_Curso, Dia, Hora_Inicio, Hora_Fin, Tarde_Inicio, Tarde_Fin)
VALUES 
(1, 'CUR001', 'Lunes', '08:00:00', '10:00:00', NULL, NULL),
(2, 'CUR002', 'Martes', '10:00:00', '12:00:00', NULL, NULL),
(3, 'CUR003', 'Miércoles', '12:00:00', '14:00:00', NULL, NULL);

-- Insertar recibos para alumnos
INSERT INTO Recibo (ID_Alumno, ID_Curso, Importe, Fecha_Emision, Periodo, Estado)
VALUES 
('87654321B', 'CUR001', 500.00, '2024-01-01', '2024-01', 'Pendiente'),
('22334455E', 'CUR002', 300.00, '2024-01-01', '2024-01', 'Pendiente'),
('66778899F', 'CUR003', 0.00, '2024-01-01', '2024-01', 'Cobrado');

