CREATE DATABASE IF NOT EXISTS vbmhospitalclinicas 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci; 
 
USE vbmhospitalclinicas; 
 
SET FOREIGN_KEY_CHECKS = 0; 
 
DROP TABLE IF EXISTS respuestas; 
DROP TABLE IF EXISTS preguntas; 
DROP TABLE IF EXISTS encuestas; 
DROP TABLE IF EXISTS documentos; 
DROP TABLE IF EXISTS categorias; 
DROP TABLE IF EXISTS traslados; 
DROP TABLE IF EXISTS medicos; 
DROP TABLE IF EXISTS pacientes; 
DROP TABLE IF EXISTS usuarios; 
DROP TABLE IF EXISTS roles; 
 
SET FOREIGN_KEY_CHECKS = 1; 
  
CREATE TABLE roles ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    nombre VARCHAR(50) NOT NULL UNIQUE, 
    descripcion VARCHAR(150) 
); 
  
CREATE TABLE pacientes ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    cedula VARCHAR(20) NOT NULL UNIQUE, 
    nombre VARCHAR(50) NOT NULL, 
    apellido VARCHAR(50) NOT NULL 
); 
  
CREATE TABLE usuarios ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    nombre VARCHAR(50) NOT NULL, 
    apellido VARCHAR(50) NOT NULL, 
    usuario VARCHAR(50) NOT NULL UNIQUE, 
    password VARCHAR(255) NOT NULL, 
    rol_id INT NOT NULL, 
    paciente_id INT NULL, 
    activo BOOLEAN NOT NULL DEFAULT TRUE, 
 
    CONSTRAINT fk_usuario_rol 
        FOREIGN KEY (rol_id) 
        REFERENCES roles(id), 
 
    CONSTRAINT fk_usuario_paciente 
        FOREIGN KEY (paciente_id) 
        REFERENCES pacientes(id) 
); 
  
CREATE TABLE medicos ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    cedula VARCHAR(20) NOT NULL UNIQUE, 
    nombre VARCHAR(50) NOT NULL, 
    apellido VARCHAR(50) NOT NULL, 
    contacto VARCHAR(100), 
    activo BOOLEAN NOT NULL DEFAULT TRUE 
); 
 
CREATE TABLE categorias ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    nombre VARCHAR(100) NOT NULL UNIQUE, 
    descripcion VARCHAR(255), 
    activo BOOLEAN NOT NULL DEFAULT TRUE 
); 
  
CREATE TABLE documentos ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    titulo VARCHAR(150) NOT NULL, 
    descripcion TEXT, 
    archivo VARCHAR(255) NOT NULL,
    imagen VARCHAR(255) NULL,
    categoria_id INT NOT NULL, 
    usuario_id INT NOT NULL, 
    fecha_publicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    activo BOOLEAN NOT NULL DEFAULT TRUE, 
 
    CONSTRAINT fk_documento_categoria 
        FOREIGN KEY (categoria_id) 
        REFERENCES categorias(id), 
 
    CONSTRAINT fk_documento_usuario 
        FOREIGN KEY (usuario_id) 
        REFERENCES usuarios(id) 
); 
  
CREATE TABLE encuestas ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    titulo VARCHAR(150) NOT NULL, 
    descripcion TEXT, 
    usuario_id INT NOT NULL, 
    activa BOOLEAN NOT NULL DEFAULT TRUE, 
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
 
    CONSTRAINT fk_encuesta_usuario 
        FOREIGN KEY (usuario_id) 
        REFERENCES usuarios(id) 
); 
  
CREATE TABLE preguntas ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    encuesta_id INT NOT NULL, 
    pregunta TEXT NOT NULL, 
    orden INT NOT NULL, 
 
    CONSTRAINT fk_pregunta_encuesta 
        FOREIGN KEY (encuesta_id) 
        REFERENCES encuestas(id) 
        ON DELETE CASCADE, 
 
    CONSTRAINT chk_pregunta_orden 
        CHECK (orden > 0) 
); 
  
CREATE TABLE respuestas ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    encuesta_id INT NOT NULL, 
    pregunta_id INT NOT NULL, 
    cedula VARCHAR(20) NOT NULL, 
    respuesta TEXT NOT NULL, 
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
 
    CONSTRAINT fk_respuesta_encuesta 
        FOREIGN KEY (encuesta_id) 
        REFERENCES encuestas(id) 
        ON DELETE CASCADE, 
 
    CONSTRAINT fk_respuesta_pregunta 
        FOREIGN KEY (pregunta_id) 
        REFERENCES preguntas(id) 
        ON DELETE CASCADE 
); 
  
CREATE TABLE traslados ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
 
    paciente_id INT NOT NULL, 
    medico_id INT NOT NULL, 
    funcionario_id INT NOT NULL, 
 
    conductor VARCHAR(100) NOT NULL, 
    enfermero VARCHAR(100) NOT NULL, 
    vehiculo VARCHAR(50) NOT NULL, 
 
    origen VARCHAR(150) NOT NULL, 
    destino VARCHAR(150) NOT NULL, 
 
    hora_salida DATETIME NULL, 
    hora_llegada DATETIME NULL, 
 
    estado VARCHAR(30) NOT NULL DEFAULT 'Pendiente', 
 
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
 
    observaciones TEXT, 
 
    CONSTRAINT fk_traslado_paciente 
        FOREIGN KEY (paciente_id) 
        REFERENCES pacientes(id), 
 
    CONSTRAINT fk_traslado_medico 
        FOREIGN KEY (medico_id) 
        REFERENCES medicos(id), 
 
    CONSTRAINT fk_traslado_funcionario 
        FOREIGN KEY (funcionario_id) 
        REFERENCES usuarios(id), 
 
    CONSTRAINT chk_traslado_estado 
        CHECK ( 
            estado IN ('Pendiente', 'En traslado', 'Finalizado', 'Cancelado') 
        ) 
); 
  
INSERT INTO roles (nombre, descripcion)  
VALUES 
('FUNCIONARIO','Gestiona documentos, encuestas y traslados'), 
('PACIENTE','Usuario del portal de documentacion'); 
 
INSERT INTO pacientes (cedula, nombre, apellido)  
VALUES 
('50000001','Dante','Gomez'), 
('50000002','Tiago','Velozo'); 
 
INSERT INTO usuarios (
    nombre, 
    apellido, 
    usuario, 
    password, 
    rol_id, 
    paciente_id, 
    activo
)  
VALUES (
    'Administrador',
    'Hospital',
    'admin',
    '$2y$12$IFyZV8gr6sZca0d7TR3.M.E3L5QgB70UUqoLpDNX08r/ucTs9xk76',
    1,
    NULL,
    TRUE
); 
 
INSERT INTO categorias (nombre, descripcion)  
VALUES 
('IVE','Información sobre interrupción voluntaria del embarazo'), 
('Prostatectomía','Información relacionada con prostatectomía'), 
('Nefrología','Información relacionada con nefrología'), 
('Trasplantes','Información relacionada con trasplantes'), 
('Tratamientos','Información general sobre tratamientos'); 
 
INSERT INTO documentos
(titulo, descripcion, archivo, imagen, categoria_id, usuario_id, activo)
VALUES
(
    'Actividad guiada',
    'Documento de actividad guiada disponible para consulta.',
    'Actividad guiada.pdf',
    'vbmcorelogo.png',
    5,
    1,
    1
);

INSERT INTO encuestas
(titulo, descripcion, usuario_id, activa)
VALUES
(
    'Encuesta de satisfaccion',
    'Encuesta para conocer la experiencia personal del paciente.',
    1,
    1
);

INSERT INTO preguntas
(encuesta_id, pregunta, orden)
VALUES
(1, '¿Cómo calificaria la atencion recibida?', 1),
(1, '¿Cómo calificaria la interfaz del portal?', 2);