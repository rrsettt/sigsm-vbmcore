<?php

class Usuario
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }
    public function buscarPorUsuario($usuario)
    {
        $sql = "SELECT
                    usuarios.*,
                    roles.nombre AS rol
                FROM usuarios
                INNER JOIN roles
                    ON usuarios.rol_id = roles.id
                WHERE usuarios.usuario = ?
                AND usuarios.activo = 1";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario]);

        return $stmt->fetch();
    }
    public function verificarPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
}