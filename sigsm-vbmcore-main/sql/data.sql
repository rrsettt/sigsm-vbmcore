USE vbmhospitalclinicas;

UPDATE usuarios SET activo = 0
WHERE rol_id IN (SELECT id FROM roles WHERE nombre = 'PACIENTE');
DELETE FROM usuarios
WHERE rol_id IN (SELECT id FROM roles WHERE nombre = 'PACIENTE')
AND NOT EXISTS (SELECT 1 FROM documentos WHERE usuario_id = usuarios.id OR revisor_id = usuarios.id)
AND NOT EXISTS (SELECT 1 FROM encuestas WHERE usuario_id = usuarios.id)
AND NOT EXISTS (SELECT 1 FROM traslados WHERE funcionario_id = usuarios.id);
DELETE FROM roles WHERE nombre = 'PACIENTE'
AND NOT EXISTS (SELECT 1 FROM usuarios WHERE rol_id = roles.id);

INSERT INTO roles (nombre, descripcion)
SELECT 'FUNCIONARIO', 'Gestiona documentos, encuestas y traslados'
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE nombre = 'FUNCIONARIO');
INSERT INTO roles (nombre, descripcion)
SELECT 'MEDICO', 'Revisa documentos'
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE nombre = 'MEDICO');

INSERT INTO usuarios (nombre, apellido, usuario, password, rol_id)
SELECT 'Administrador', 'Hospital', 'admin', '$2y$10$sg3A0SPa0j75EXiFl9Y6DechBxLUMku0sSZzLGcWuV9ZjP.BLsFka', roles.id FROM roles
WHERE roles.nombre = 'FUNCIONARIO' AND NOT EXISTS (SELECT 1 FROM usuarios WHERE usuario = 'admin');
INSERT INTO usuarios (nombre, apellido, usuario, password, rol_id)
SELECT 'Medico', 'Hospital', 'Medico', '$2y$10$sg3A0SPa0j75EXiFl9Y6DechBxLUMku0sSZzLGcWuV9ZjP.BLsFka', roles.id FROM roles
WHERE roles.nombre = 'MEDICO' AND NOT EXISTS (SELECT 1 FROM usuarios WHERE usuario = 'Medico');

UPDATE usuarios SET password = '$2y$10$sg3A0SPa0j75EXiFl9Y6DechBxLUMku0sSZzLGcWuV9ZjP.BLsFka' WHERE usuario IN ('admin', 'Medico');

INSERT INTO categorias (nombre, descripcion)
SELECT 'IVE', 'Información sobre interrupción voluntaria del embarazo' WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'IVE');
INSERT INTO categorias (nombre, descripcion)
SELECT 'Prostatectomía', 'Información sobre prostatectomía' WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Prostatectomía');
INSERT INTO categorias (nombre, descripcion)
SELECT 'Nefrología', 'Información sobre nefrología' WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Nefrología');
INSERT INTO categorias (nombre, descripcion)
SELECT 'Trasplantes', 'Información sobre trasplantes' WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Trasplantes');
INSERT INTO categorias (nombre, descripcion)
SELECT 'Tratamientos', 'Documentos de tratamientos' WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Tratamientos');

INSERT INTO encuestas (titulo, descripcion, usuario_id, activa)
SELECT 'Encuesta de satisfacción', 'Contanos cómo fue tu experiencia en el hospital.', usuarios.id, 1
FROM usuarios WHERE usuario = 'admin' AND NOT EXISTS (SELECT 1 FROM encuestas);

INSERT INTO preguntas (encuesta_id, pregunta, orden)
SELECT id, '¿Cómo calificaría la atención recibida?', 1 FROM encuestas
WHERE titulo = 'Encuesta de satisfacción' AND NOT EXISTS (SELECT 1 FROM preguntas WHERE preguntas.encuesta_id = encuestas.id);
INSERT INTO preguntas (encuesta_id, pregunta, orden)
SELECT id, '¿Qué podríamos mejorar?', 2 FROM encuestas
WHERE titulo = 'Encuesta de satisfacción' AND NOT EXISTS (SELECT 1 FROM preguntas WHERE preguntas.encuesta_id = encuestas.id AND orden = 2);

INSERT INTO documentos (titulo, descripcion, archivo, categoria_id, usuario_id, activo)
SELECT 'Actividad guiada', 'Documento de ejemplo', 'Actividad guiada.pdf', categorias.id, usuarios.id, 1
FROM categorias INNER JOIN usuarios ON usuarios.usuario = 'admin'
WHERE categorias.nombre = 'Tratamientos'
AND NOT EXISTS (SELECT 1 FROM documentos WHERE archivo = 'Actividad guiada.pdf');

INSERT INTO pacientes (cedula, nombre, apellido)
SELECT '00000000', 'Paciente', 'Demo'
WHERE NOT EXISTS (SELECT 1 FROM pacientes WHERE cedula = '00000000');

INSERT INTO traslados (paciente_id, funcionario_id, conductor, enfermero, vehiculo, origen, destino, observaciones)
SELECT pacientes.id, usuarios.id, 'Conductor de prueba', 'Enfermero de prueba', 'DEMO-01', 'Hospital de Clínicas', 'Centro', 'Prueba'
FROM pacientes INNER JOIN usuarios ON usuarios.usuario = 'admin'
WHERE pacientes.cedula = '00000000'
AND NOT EXISTS (SELECT 1 FROM traslados WHERE vehiculo = 'DEMO-01');
