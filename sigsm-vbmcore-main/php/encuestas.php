<?php

session_start();

require_once "../datos/conexion.php";

header("Content-Type: application/json; charset=UTF-8");

$accion = $_GET["action"] ?? "";

function responder($ok, $mensaje = "", $datos = [])
{
    echo json_encode(array_merge([
        "ok" => $ok,
        "mensaje" => $mensaje
    ], $datos));

    exit;
}

function autorizado()
{
    return isset($_SESSION["usuario_id"])
        && ($_SESSION["rol"] ?? "") === "FUNCIONARIO";
}

function leerJson()
{
    return json_decode(
        file_get_contents("php://input"),
        true
    ) ?? [];
}

function obtenerPreguntas($conexion, $encuestaId)
{
    $stmt = $conexion->prepare(
        "SELECT id, pregunta, orden
         FROM preguntas
         WHERE encuesta_id = ?
         ORDER BY orden"
    );

    $stmt->execute([$encuestaId]);

    return $stmt->fetchAll();
}

if (!in_array($accion, ["obtener", "responder"]) && !autorizado()) {
    http_response_code(401);
    responder(false, "No autorizado");
}

if ($accion === "listar") {

    $stmt = $conexion->prepare(
        "SELECT id, titulo, descripcion, activa, fecha_creacion
         FROM encuestas
         ORDER BY fecha_creacion DESC"
    );

    $stmt->execute();

    responder(true, "", [
        "encuestas" => $stmt->fetchAll()
    ]);
}

if ($accion === "crear") {

    $datos = leerJson();

    $titulo = trim($datos["titulo"] ?? "");
    $descripcion = trim($datos["descripcion"] ?? "");
    $activa = !empty($datos["activa"]) ? 1 : 0;
    $preguntas = $datos["preguntas"] ?? [];

    if ($titulo === "" || empty($preguntas)) {
        responder(
            false,
            "Ingrese un título y al menos una pregunta"
        );
    }

    $stmt = $conexion->prepare(
        "INSERT INTO encuestas
        (titulo, descripcion, usuario_id, activa)
        VALUES (?, ?, ?, ?)"
    );

    $stmt->execute([
        $titulo,
        $descripcion,
        $_SESSION["usuario_id"],
        $activa
    ]);

    $encuestaId = $conexion->lastInsertId();

    $stmt = $conexion->prepare(
        "INSERT INTO preguntas
        (encuesta_id, pregunta, orden)
        VALUES (?, ?, ?)"
    );

    foreach ($preguntas as $indice => $pregunta) {

        $pregunta = trim($pregunta);

        if ($pregunta !== "") {
            $stmt->execute([
                $encuestaId,
                $pregunta,
                $indice + 1
            ]);
        }
    }

    responder(true, "Encuesta creada correctamente");
}

if ($accion === "eliminar") {

    $id = $_POST["id"] ?? "";

    if ($id === "") {
        responder(false, "Encuesta no válida");
    }

    $stmt = $conexion->prepare(
        "DELETE FROM encuestas WHERE id = ?"
    );

    $stmt->execute([$id]);

    responder(true, "Encuesta eliminada correctamente");
}

if ($accion === "obtener") {

    $stmt = $conexion->prepare(
        "SELECT id, titulo, descripcion
         FROM encuestas
         WHERE activa = 1
         ORDER BY fecha_creacion DESC
         LIMIT 1"
    );

    $stmt->execute();

    $encuesta = $stmt->fetch();

    if (!$encuesta) {
        responder(false, "No hay encuestas disponibles");
    }

    responder(true, "", [
        "encuesta" => $encuesta,
        "preguntas" => obtenerPreguntas(
            $conexion,
            $encuesta["id"]
        )
    ]);
}

if ($accion === "responder") {

    $datos = leerJson();

    $encuestaId = $datos["encuesta_id"] ?? "";
    $cedula = trim($datos["cedula"] ?? "");
    $respuestas = $datos["respuestas"] ?? [];

    if (
        $encuestaId === "" ||
        $cedula === "" ||
        empty($respuestas)
    ) {
        responder(false, "Complete todos los campos");
    }

    foreach ($respuestas as $respuesta) {

        if (
            empty($respuesta["pregunta_id"]) ||
            trim($respuesta["respuesta"] ?? "") === ""
        ) {
            responder(
                false,
                "Debe responder todas las preguntas"
            );
        }
    }

    $stmt = $conexion->prepare(
        "INSERT INTO respuestas
        (encuesta_id, pregunta_id, cedula, respuesta)
        VALUES (?, ?, ?, ?)"
    );

    foreach ($respuestas as $respuesta) {

        $stmt->execute([
            $encuestaId,
            $respuesta["pregunta_id"],
            $cedula,
            trim($respuesta["respuesta"])
        ]);
    }

    responder(true, "Encuesta enviada correctamente");
}

if ($accion === "respuestas") {

    $id = $_GET["id"] ?? "";

    if ($id === "") {
        responder(false, "Encuesta no válida");
    }

    $stmt = $conexion->prepare(
        "SELECT titulo
         FROM encuestas
         WHERE id = ?"
    );

    $stmt->execute([$id]);

    $encuesta = $stmt->fetch();

    if (!$encuesta) {
        responder(false, "Encuesta no encontrada");
    }

    $stmt = $conexion->prepare(
        "SELECT
            respuestas.cedula,
            preguntas.pregunta,
            respuestas.respuesta,
            respuestas.fecha
         FROM respuestas
         INNER JOIN preguntas
            ON respuestas.pregunta_id = preguntas.id
         WHERE respuestas.encuesta_id = ?
         ORDER BY respuestas.fecha DESC,
                  preguntas.orden"
    );

    $stmt->execute([$id]);

    responder(true, "", [
        "titulo" => $encuesta["titulo"],
        "respuestas" => $stmt->fetchAll()
    ]);
}

if ($accion === "editarDatos") {

    $id = $_GET["id"] ?? "";

    if ($id === "") {
        responder(false, "Encuesta no válida");
    }

    $stmt = $conexion->prepare(
        "SELECT id, titulo, descripcion, activa
         FROM encuestas
         WHERE id = ?"
    );

    $stmt->execute([$id]);

    $encuesta = $stmt->fetch();

    if (!$encuesta) {
        responder(false, "Encuesta no encontrada");
    }

    responder(true, "", [
        "encuesta" => $encuesta,
        "preguntas" => obtenerPreguntas(
            $conexion,
            $id
        )
    ]);
}

if ($accion === "editar") {

    $datos = leerJson();

    $id = $datos["id"] ?? "";
    $titulo = trim($datos["titulo"] ?? "");
    $descripcion = trim($datos["descripcion"] ?? "");
    $activa = !empty($datos["activa"]) ? 1 : 0;
    $preguntas = $datos["preguntas"] ?? [];

    if (
        $id === "" ||
        $titulo === "" ||
        empty($preguntas)
    ) {
        responder(
            false,
            "Complete los datos de la encuesta"
        );
    }

    $conexion->beginTransaction();

    try {

        $stmt = $conexion->prepare(
            "UPDATE encuestas
             SET titulo = ?,
                 descripcion = ?,
                 activa = ?
             WHERE id = ?"
        );

        $stmt->execute([
            $titulo,
            $descripcion,
            $activa,
            $id
        ]);

        $idsActuales = [];

        foreach ($preguntas as $indice => $pregunta) {

            $preguntaId = $pregunta["id"] ?? "";
            $texto = trim($pregunta["pregunta"] ?? "");

            if ($texto === "") {
                continue;
            }

            if ($preguntaId !== "") {

                $stmt = $conexion->prepare(
                    "UPDATE preguntas
                     SET pregunta = ?, orden = ?
                     WHERE id = ?
                     AND encuesta_id = ?"
                );

                $stmt->execute([
                    $texto,
                    $indice + 1,
                    $preguntaId,
                    $id
                ]);

                $idsActuales[] = $preguntaId;

            } else {

                $stmt = $conexion->prepare(
                    "INSERT INTO preguntas
                    (encuesta_id, pregunta, orden)
                    VALUES (?, ?, ?)"
                );

                $stmt->execute([
                    $id,
                    $texto,
                    $indice + 1
                ]);

                $idsActuales[] = $conexion->lastInsertId();
            }
        }

        $stmt = $conexion->prepare(
            "SELECT id
             FROM preguntas
             WHERE encuesta_id = ?"
        );

        $stmt->execute([$id]);

        $preguntasGuardadas = $stmt->fetchAll(
            PDO::FETCH_COLUMN
        );

        foreach ($preguntasGuardadas as $preguntaId) {

            if (!in_array($preguntaId, $idsActuales)) {

                $stmt = $conexion->prepare(
                    "SELECT COUNT(*)
                     FROM respuestas
                     WHERE pregunta_id = ?"
                );

                $stmt->execute([$preguntaId]);

                if ($stmt->fetchColumn() > 0) {
                    throw new Exception(
                        "No se puede quitar una pregunta que ya tiene respuestas"
                    );
                }

                $stmt = $conexion->prepare(
                    "DELETE FROM preguntas
                     WHERE id = ?"
                );

                $stmt->execute([$preguntaId]);
            }
        }

        $conexion->commit();

        responder(
            true,
            "Encuesta actualizada correctamente"
        );

    } catch (Exception $error) {

        $conexion->rollBack();

        responder(
            false,
            $error->getMessage()
        );
    }
}

responder(false, "Acción no válida");