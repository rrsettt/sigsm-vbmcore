<?php

class Documento
{
    private $conexion;

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
        $sql = "SELECT
                    documentos.id,
                    documentos.titulo,
                    documentos.descripcion,
                    documentos.archivo,
                    documentos.imagen,
                    categorias.nombre AS categoria
                FROM documentos
                INNER JOIN categorias
                    ON documentos.categoria_id = categorias.id
                WHERE documentos.activo = 1
                ORDER BY documentos.fecha_publicacion DESC";

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
                    activo
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
                    activo = ?
                WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $titulo,
            $descripcion,
            $archivo,
            $imagen,
            $categoriaId,
            $activo,
            $id
        ]);
    }

public function eliminar($id)
{
    $sql = "DELETE FROM documentos WHERE id = ?";
    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([$id]);
}
}