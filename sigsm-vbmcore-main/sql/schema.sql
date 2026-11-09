CREATE DATABASE IF NOT EXISTS vbmhospitalclinicas
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vbmhospitalclinicas;

CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(150)
);
CREATE TABLE IF NOT EXISTS pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL
);
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_usuario_rol
        FOREIGN KEY (rol_id)
        REFERENCES roles(id)
);
CREATE TABLE IF NOT EXISTS medicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    contacto VARCHAR(100),
    activo BOOLEAN NOT NULL DEFAULT TRUE
);
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(255),
    activo BOOLEAN NOT NULL DEFAULT TRUE
);
CREATE TABLE IF NOT EXISTS documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    archivo VARCHAR(255) NOT NULL,
    imagen VARCHAR(255) NULL,
    categoria_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_publicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    estado_revision VARCHAR(20) NOT NULL DEFAULT 'Pendiente',
    motivo_rechazo TEXT,
    revisor_id INT NULL,
    fecha_revision DATETIME NULL,
    version INT NOT NULL DEFAULT 1,
    CONSTRAINT fk_documento_revisor FOREIGN KEY (revisor_id) REFERENCES usuarios(id),
    CONSTRAINT fk_documento_categoria
        FOREIGN KEY (categoria_id)
        REFERENCES categorias(id),
    CONSTRAINT fk_documento_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
);
CREATE TABLE IF NOT EXISTS encuestas (
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
CREATE TABLE IF NOT EXISTS preguntas (
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
CREATE TABLE IF NOT EXISTS respuestas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pregunta_id INT NOT NULL,
    respuesta TEXT NOT NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_respuesta_pregunta
        FOREIGN KEY (pregunta_id)
        REFERENCES preguntas(id)
        ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS traslados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    medico_id INT NULL,
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
    version INT NOT NULL DEFAULT 1,
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
            estado IN (
                'Pendiente',
                'En traslado',
                'Finalizado',
                'Cancelado'
            )
        )
);

ALTER TABLE documentos
    ADD COLUMN IF NOT EXISTS estado_revision VARCHAR(20) NOT NULL DEFAULT 'Pendiente',
    ADD COLUMN IF NOT EXISTS motivo_rechazo TEXT,
    ADD COLUMN IF NOT EXISTS revisor_id INT NULL,
    ADD COLUMN IF NOT EXISTS fecha_revision DATETIME NULL,
    ADD COLUMN IF NOT EXISTS version INT NOT NULL DEFAULT 1;

ALTER TABLE documentos
    ADD FOREIGN KEY IF NOT EXISTS fk_documento_revisor (revisor_id) REFERENCES usuarios(id);

ALTER TABLE traslados
    ADD COLUMN IF NOT EXISTS version INT NOT NULL DEFAULT 1,
    MODIFY medico_id INT NULL;

ALTER TABLE traslados DROP CONSTRAINT IF EXISTS chk_traslado_estado;
ALTER TABLE traslados ADD CONSTRAINT chk_traslado_estado
    CHECK (estado IN ('Pendiente', 'En traslado', 'Finalizado', 'Cancelado'));

ALTER TABLE respuestas
    DROP FOREIGN KEY IF EXISTS fk_respuesta_encuesta,
    DROP COLUMN IF EXISTS encuesta_id,
    DROP COLUMN IF EXISTS cedula;

ALTER TABLE usuarios
    DROP FOREIGN KEY IF EXISTS fk_usuario_paciente,
    DROP COLUMN IF EXISTS paciente_id;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW documentos_publicos AS
SELECT documentos.id, documentos.titulo, documentos.descripcion, documentos.archivo,
       documentos.imagen, documentos.fecha_publicacion, categorias.nombre AS categoria
FROM documentos INNER JOIN categorias ON categorias.id = documentos.categoria_id
WHERE documentos.activo = 1 AND documentos.estado_revision = 'Aprobado';

CREATE OR REPLACE SQL SECURITY INVOKER VIEW resumen_encuestas AS
SELECT preguntas.encuesta_id, preguntas.pregunta, COUNT(respuestas.id) AS cantidad
FROM preguntas LEFT JOIN respuestas ON respuestas.pregunta_id = preguntas.id
GROUP BY preguntas.id, preguntas.encuesta_id, preguntas.pregunta;

CREATE USER IF NOT EXISTS 'vbm_administrador'@'%'
IDENTIFIED BY 'AdminBD2026!';

CREATE USER IF NOT EXISTS 'vbm_aplicacion'@'%'
IDENTIFIED BY 'AppBD2026!';

GRANT ALL PRIVILEGES ON vbmhospitalclinicas.*
TO 'vbm_administrador'@'%';

GRANT SELECT ON vbmhospitalclinicas.roles
TO 'vbm_aplicacion'@'%';
GRANT SELECT ON vbmhospitalclinicas.categorias
TO 'vbm_aplicacion'@'%';
GRANT SELECT, INSERT ON vbmhospitalclinicas.usuarios
TO 'vbm_aplicacion'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON vbmhospitalclinicas.documentos
TO 'vbm_aplicacion'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON vbmhospitalclinicas.encuestas
TO 'vbm_aplicacion'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON vbmhospitalclinicas.preguntas
TO 'vbm_aplicacion'@'%';
GRANT SELECT, INSERT ON vbmhospitalclinicas.respuestas
TO 'vbm_aplicacion'@'%';
GRANT SELECT, INSERT ON vbmhospitalclinicas.pacientes
TO 'vbm_aplicacion'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON vbmhospitalclinicas.traslados
TO 'vbm_aplicacion'@'%';
GRANT SELECT ON vbmhospitalclinicas.documentos_publicos
TO 'vbm_aplicacion'@'%';
GRANT SELECT ON vbmhospitalclinicas.resumen_encuestas
TO 'vbm_aplicacion'@'%';
