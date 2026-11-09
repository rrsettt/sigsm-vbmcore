<?php
class Encuesta
{

    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar()
    {
        $stmt = $this->conexion->prepare("SELECT id, titulo, descripcion, activa, fecha_creacion FROM encuestas ORDER BY fecha_creacion DESC, id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtener($id)
    {
        $stmt = $this->conexion->prepare("SELECT id, titulo, descripcion, activa FROM encuestas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function activas()
    {
        $stmt = $this->conexion->prepare("SELECT id, titulo, descripcion, activa FROM encuestas WHERE activa = 1 ORDER BY fecha_creacion DESC, id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function preguntas($id)
    {
        $stmt = $this->conexion->prepare("SELECT id, pregunta, orden FROM preguntas WHERE encuesta_id = ? ORDER BY orden");
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    public function crear($titulo, $descripcion, $usuarioId, $activa, $preguntas)
    {

        $this->conexion->beginTransaction();
        try {
            $stmt = $this->conexion->prepare("INSERT INTO encuestas (titulo, descripcion, usuario_id, activa) VALUES (?, ?, ?, ?)");
            $stmt->execute([$titulo, $descripcion, $usuarioId, $activa]);
            $id = $this->conexion->lastInsertId();
            $stmt = $this->conexion->prepare("INSERT INTO preguntas (encuesta_id, pregunta, orden) VALUES (?, ?, ?)");
            foreach ($preguntas as $indice => $pregunta) {
                $stmt->execute([$id, trim($pregunta), $indice + 1]);
            }
            $this->conexion->commit();
            return $id;
        } catch (Exception $error) {
            $this->conexion->rollBack();
            throw $error;
        }
    }

    public function eliminar($id)
    {
        $stmt = $this->conexion->prepare("DELETE FROM encuestas WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function guardarRespuestas($respuestas)
    {
        $this->conexion->beginTransaction();
        try {
            $stmt = $this->conexion->prepare("INSERT INTO respuestas (pregunta_id, respuesta) VALUES (?, ?)");
            foreach ($respuestas as $respuesta) {
                $stmt->execute([$respuesta["pregunta_id"], trim($respuesta["respuesta"])]);
            }
            $this->conexion->commit();
        } catch (Exception $error) {
            $this->conexion->rollBack();
            throw $error;
        }
    }

    public function respuestas($id)
    {
        $stmt = $this->conexion->prepare(
            "SELECT preguntas.pregunta, respuestas.respuesta, respuestas.fecha
             FROM respuestas INNER JOIN preguntas ON respuestas.pregunta_id = preguntas.id
             WHERE preguntas.encuesta_id = ? ORDER BY respuestas.fecha DESC, preguntas.orden"
        );
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    public function cantidadRespuestas($preguntaId)
    {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM respuestas WHERE pregunta_id = ?");
        $stmt->execute([$preguntaId]);
        return $stmt->fetchColumn();
    }

    public function editar($id, $titulo, $descripcion, $activa, $preguntas, $quitar)
    {
        $this->conexion->beginTransaction();
        try {
            $stmt = $this->conexion->prepare("UPDATE encuestas SET titulo = ?, descripcion = ?, activa = ? WHERE id = ?");
            $stmt->execute([$titulo, $descripcion, $activa, $id]);
            foreach ($preguntas as $indice => $pregunta) {
                if (!empty($pregunta["id"])) {
                    $stmt = $this->conexion->prepare("UPDATE preguntas SET pregunta = ?, orden = ? WHERE id = ? AND encuesta_id = ?");
                    $stmt->execute([trim($pregunta["pregunta"]), $indice + 1, $pregunta["id"], $id]);
                } else {
                    $stmt = $this->conexion->prepare("INSERT INTO preguntas (encuesta_id, pregunta, orden) VALUES (?, ?, ?)");
                    $stmt->execute([$id, trim($pregunta["pregunta"]), $indice + 1]);
                }
            }
            $stmt = $this->conexion->prepare("DELETE FROM preguntas WHERE id = ? AND encuesta_id = ?");
            foreach ($quitar as $preguntaId) {
                $stmt->execute([$preguntaId, $id]);
            }
            $this->conexion->commit();
        } catch (Exception $error) {
            $this->conexion->rollBack();
            throw $error;
        }
    }
}
