<?php

class Traslado
{

    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar()
    {
        $stmt = $this->conexion->prepare(
            "SELECT traslados.*, pacientes.cedula, pacientes.nombre, pacientes.apellido
             FROM traslados INNER JOIN pacientes ON pacientes.id = traslados.paciente_id
             ORDER BY traslados.id DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtener($id)
    {
        $stmt = $this->conexion->prepare(
            "SELECT traslados.*, pacientes.cedula, pacientes.nombre, pacientes.apellido
             FROM traslados INNER JOIN pacientes ON pacientes.id = traslados.paciente_id
             WHERE traslados.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function crear($cedula, $nombre, $apellido, $conductor, $enfermero, $vehiculo, $origen, $destino, $observaciones, $funcionarioId)
    {
        $this->conexion->beginTransaction();
        try {

            $stmt = $this->conexion->prepare("SELECT id, nombre, apellido FROM pacientes WHERE cedula = ?");
            $stmt->execute([$cedula]);
            $paciente = $stmt->fetch();
            if ($paciente) {
                if ($paciente["nombre"] !== $nombre || $paciente["apellido"] !== $apellido) {
                    throw new Exception("El nombre o apellido no coincide con esa cédula.");
                }
                $pacienteId = $paciente["id"];
            } else {
                $stmt = $this->conexion->prepare("INSERT INTO pacientes (cedula, nombre, apellido) VALUES (?, ?, ?)");
                $stmt->execute([$cedula, $nombre, $apellido]);
                $pacienteId = $this->conexion->lastInsertId();
            }
            $stmt = $this->conexion->prepare(
                "INSERT INTO traslados (paciente_id, funcionario_id, conductor, enfermero, vehiculo, origen, destino, observaciones)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$pacienteId, $funcionarioId, $conductor, $enfermero, $vehiculo, $origen, $destino, $observaciones]);
            $id = $this->conexion->lastInsertId();
            $this->conexion->commit();
            return $id;
        } catch (Exception $error) {
            $this->conexion->rollBack();
            throw $error;
        }
    }

    public function editar($id, $version, $conductor, $enfermero, $vehiculo, $origen, $destino, $observaciones, $salida, $llegada)
    {
        $stmt = $this->conexion->prepare(
            "UPDATE traslados SET conductor = ?, enfermero = ?, vehiculo = ?, origen = ?,
                destino = ?, observaciones = ?, hora_salida = ?, hora_llegada = ?, version = version + 1
             WHERE id = ? AND version = ?"
        );
        $stmt->execute([$conductor, $enfermero, $vehiculo, $origen, $destino, $observaciones, $salida, $llegada, $id, $version]);
        return $stmt->rowCount() === 1;
    }

    public function cambiarEstado($id, $accion)
    {
        if ($accion === "iniciar") {
            $sql = "UPDATE traslados SET estado = 'En traslado', hora_salida = NOW(), version = version + 1
                    WHERE id = ? AND estado = 'Pendiente'";
        } elseif ($accion === "finalizar") {
            $sql = "UPDATE traslados SET estado = 'Finalizado', hora_llegada = NOW(), version = version + 1
                    WHERE id = ? AND estado = 'En traslado'";
        } elseif ($accion === "cancelar") {
            $sql = "UPDATE traslados SET estado = 'Cancelado', version = version + 1
                    WHERE id = ? AND estado IN ('Pendiente', 'En traslado')";
        } elseif ($accion === "eliminar") {
            $sql = "DELETE FROM traslados WHERE id = ?";
        } else {
            return false;
        }
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount() === 1;
    }
}
