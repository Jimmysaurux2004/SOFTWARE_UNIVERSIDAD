-- Base de datos para Sistema de Tutorías Universitarias
-- Primera iteración - Metodología XP

DROP DATABASE IF EXISTS tutorias_db;
CREATE DATABASE tutorias_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tutorias_db;

-- Tabla de usuarios (estudiantes, docentes, admin)
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    rol ENUM('estudiante', 'docente', 'admin') NOT NULL COMMENT 'Rol del usuario en el sistema',
    email VARCHAR(100) UNIQUE NOT NULL COMMENT 'Email único para login',
    password_hash VARCHAR(255) NOT NULL COMMENT 'Hash de la contraseña',
    nombre VARCHAR(100) NOT NULL COMMENT 'Nombre del usuario',
    apellido VARCHAR(100) NOT NULL COMMENT 'Apellido del usuario',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación del registro'
) COMMENT 'Tabla principal de usuarios del sistema';

-- Tabla de cursos disponibles
CREATE TABLE cursos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(20) UNIQUE NOT NULL COMMENT 'Código único del curso',
    nombre VARCHAR(100) NOT NULL COMMENT 'Nombre descriptivo del curso',
    semestre VARCHAR(10) NOT NULL COMMENT 'Semestre académico del curso'
) COMMENT 'Catálogo de cursos disponibles para tutorías';

-- Tabla de solicitudes de tutorías
CREATE TABLE tutorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    estudiante_id INT NOT NULL COMMENT 'ID del estudiante que solicita',
    curso_id INT NOT NULL COMMENT 'ID del curso para la tutoría',
    descripcion TEXT NOT NULL COMMENT 'Descripción de la necesidad de tutoría',
    estado ENUM('pendiente', 'confirmada', 'finalizada') DEFAULT 'pendiente' COMMENT 'Estado actual de la tutoría',
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la solicitud',
    fecha_asignada DATETIME NULL COMMENT 'Fecha y hora asignada para la tutoría',
    docente_id INT NULL COMMENT 'ID del docente asignado',
    FOREIGN KEY (estudiante_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE,
    FOREIGN KEY (docente_id) REFERENCES usuarios(id) ON DELETE SET NULL
) COMMENT 'Registro de todas las solicitudes de tutoría';

-- Tabla de disponibilidad de docentes
CREATE TABLE disponibilidad_docentes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    docente_id INT NOT NULL COMMENT 'ID del docente',
    fecha DATE NOT NULL COMMENT 'Fecha de disponibilidad',
    hora_inicio TIME NOT NULL COMMENT 'Hora de inicio del bloque disponible',
    hora_fin TIME NOT NULL COMMENT 'Hora de fin del bloque disponible',
    disponible BOOLEAN DEFAULT TRUE COMMENT 'Si el bloque está disponible',
    FOREIGN KEY (docente_id) REFERENCES usuarios(id) ON DELETE CASCADE
) COMMENT 'Horarios de disponibilidad de los docentes';

-- Tabla de historial académico
CREATE TABLE historial_academico (
    id INT PRIMARY KEY AUTO_INCREMENT,
    estudiante_id INT NOT NULL COMMENT 'ID del estudiante',
    curso_id INT NOT NULL COMMENT 'ID del curso',
    veces_llevado INT DEFAULT 1 COMMENT 'Número de veces que llevó el curso',
    aprobado BOOLEAN DEFAULT FALSE COMMENT 'Si aprobó el curso',
    FOREIGN KEY (estudiante_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
) COMMENT 'Historial académico de los estudiantes';

-- Datos de ejemplo para pruebas

-- Usuarios de prueba
INSERT INTO usuarios (rol, email, password_hash, nombre, apellido) VALUES
('estudiante', 'juan.perez@universidad.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan', 'Pérez'),
('estudiante', 'maria.garcia@universidad.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María', 'García'),
('docente', 'prof.rodriguez@universidad.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos', 'Rodríguez'),
('admin', 'admin@universidad.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'Sistema');

-- Cursos de ejemplo
INSERT INTO cursos (codigo, nombre, semestre) VALUES
('MAT101', 'Matemática Básica', '2024-1'),
('FIS201', 'Física General', '2024-1'),
('QUI301', 'Química Orgánica', '2024-2'),
('BIO401', 'Biología Molecular', '2024-2'),
('ING501', 'Ingeniería de Software', '2024-2');

-- Historial académico de ejemplo
INSERT INTO historial_academico (estudiante_id, curso_id, veces_llevado, aprobado) VALUES
(1, 1, 2, FALSE), -- Juan llevó Matemática Básica 2 veces, no aprobó
(1, 2, 1, TRUE),  -- Juan llevó Física General 1 vez, aprobó
(2, 1, 1, TRUE),  -- María llevó Matemática Básica 1 vez, aprobó
(2, 3, 3, FALSE); -- María llevó Química Orgánica 3 veces, no aprobó

-- Disponibilidad del docente
INSERT INTO disponibilidad_docentes (docente_id, fecha, hora_inicio, hora_fin, disponible) VALUES
(3, '2024-01-15', '09:00:00', '11:00:00', TRUE),
(3, '2024-01-15', '14:00:00', '16:00:00', TRUE),
(3, '2024-01-16', '10:00:00', '12:00:00', TRUE),
(3, '2024-01-17', '15:00:00', '17:00:00', TRUE);

-- Tutorías de ejemplo
INSERT INTO tutorias (estudiante_id, curso_id, descripcion, estado, fecha_asignada, docente_id) VALUES
(1, 1, 'Necesito ayuda con ecuaciones cuadráticas', 'confirmada', '2024-01-15 09:00:00', 3),
(2, 3, 'Tengo dificultades con reacciones de sustitución', 'pendiente', NULL, NULL);