<?php
class Documento
{

    private $conexion;

    public function categorias()
    {
        $stmt = $this->conexion->prepare("SELECT id, nombre FROM categorias WHERE activo = 1 ORDER BY nombre");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function categoriaValida($id)
    {
        $stmt = $this->conexion->prepare("SELECT id FROM categorias WHERE id = ? AND activo = 1");
        $stmt->execute([$id]);
        return $stmt->fetch() !== false;
    }
    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar()
    {
        $sql = "SELECT
                    documentos.id,
                    documentos.titulo,
                    documentos.descripcion,
                    documentos.archivo,
                    documentos.imagen,
                    documentos.fecha_publicacion,
                    documentos.activo,
                    documentos.estado_revision,
                    documentos.motivo_rechazo,
                    documentos.version,
                    categorias.nombre AS categoria

                FROM documentos
                INNER JOIN categorias
                    ON documentos.categoria_id = categorias.id
                ORDER BY documentos.fecha_publicacion DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function listarPublicos()
    {
        $sql = "SELECT id, titulo, descripcion, archivo, imagen, categoria
                FROM documentos_publicos ORDER BY fecha_publicacion DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function obtener($id)
    {
        $sql = "SELECT
                    id,
                    titulo,
                    descripcion,
                    archivo,
                    imagen,
                    categoria_id,
                    activo, estado_revision, motivo_rechazo, version
                FROM documentos
                WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function crear(
        $titulo,
        $descripcion,
        $archivo,
        $imagen,
        $categoriaId,
        $usuarioId,
        $activo
    ) {
        $sql = "INSERT INTO documentos
                (
                    titulo,
                    descripcion,
                    archivo,
                    imagen,
                    categoria_id,
                    usuario_id,
                    activo
                )
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            $titulo,
            $descripcion,
            $archivo,
            $imagen,
            $categoriaId,
            $usuarioId,
            $activo
        ]);
    }
    public function editar(
        $id,
        $version,
        $titulo,
        $descripcion,
        $archivo,
        $imagen,
        $categoriaId,
        $activo
    ) {
        $sql = "UPDATE documentos
                SET titulo = ?,
                    descripcion = ?,
                    archivo = ?,
                    imagen = ?,
                    categoria_id = ?,
                    activo = ?,
                    estado_revision = 'Pendiente', motivo_rechazo = NULL,
                    revisor_id = NULL, fecha_revision = NULL, version = version + 1
                WHERE id = ? AND version = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            $titulo,
            $descripcion,
            $archivo,
            $imagen,
            $categoriaId,
            $activo,
            $id,
            $version
        ]);
        return $stmt->rowCount() === 1;
    }
    public function eliminar($id)
    {
        $sql = "DELETE FROM documentos WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function revisar($id, $version, $estado, $motivo, $medicoId)
    {
        $stmt = $this->conexion->prepare(
            "UPDATE documentos SET estado_revision = ?, motivo_rechazo = ?,
                revisor_id = ?, fecha_revision = NOW()
             WHERE id = ? AND version = ? AND estado_revision = 'Pendiente'"
        );
        $stmt->execute([$estado, $motivo, $medicoId, $id, $version]);
        return $stmt->rowCount() === 1;
    }

    public function pendientes()
    {
        $stmt = $this->conexion->prepare(
            "SELECT documentos.*, categorias.nombre AS categoria
             FROM documentos INNER JOIN categorias ON categorias.id = documentos.categoria_id
             WHERE documentos.estado_revision = 'Pendiente' ORDER BY documentos.id"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
