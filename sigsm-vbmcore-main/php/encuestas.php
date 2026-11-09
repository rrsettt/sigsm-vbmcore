<?php
session_start();
require_once "../datos/conexion.php";
require_once "../datos/Encuesta.php";
header("Content-Type: application/json; charset=UTF-8");
$accion = $_GET["action"] ?? "";
$encuesta = new Encuesta($conexion);

function responder($ok, $mensaje = "", $datos = [])
{
    echo json_encode(array_merge(["ok" => $ok, "mensaje" => $mensaje], $datos));
    exit;
}
function leerJson()
{
    return json_decode(file_get_contents("php://input"), true) ?? [];
}

if (!in_array($accion, ["publicas", "obtener", "responder"])) {
    if (!isset($_SESSION["usuario_id"]) || $_SESSION["rol"] !== "FUNCIONARIO") {
        http_response_code(401);
        responder(false, "No autorizado");
    }
}
if (in_array($accion, ["crear", "editar", "eliminar", "responder"]) && $_SERVER["REQUEST_METHOD"] !== "POST") {
    responder(false, "Debe enviar el formulario");
}

if ($accion === "listar") {
    responder(true, "", ["encuestas" => $encuesta->listar()]);
}
if ($accion === "publicas") {
    responder(true, "", ["encuestas" => $encuesta->activas()]);
}
if ($accion === "obtener") {
    if (isset($_GET["id"])) {
        $actual = $encuesta->obtener((int)$_GET["id"]);
    } else {
        $activas = $encuesta->activas();
        $actual = $activas[0] ?? null;
    }
    if (!$actual || !$actual["activa"]) { responder(false, "La encuesta no está disponible"); }
    responder(true, "", ["encuesta" => $actual, "preguntas" => $encuesta->preguntas($actual["id"])]);
}
if ($accion === "editarDatos" || $accion === "respuestas") {
    $id = (int)($_GET["id"] ?? 0);
    $actual = $encuesta->obtener($id);
    if (!$actual) { responder(false, "Encuesta no encontrada"); }
    if ($accion === "respuestas") {
        responder(true, "", ["titulo" => $actual["titulo"], "respuestas" => $encuesta->respuestas($id)]);
    }
    responder(true, "", ["encuesta" => $actual, "preguntas" => $encuesta->preguntas($id)]);
}
if ($accion === "eliminar") {
    $id = (int)($_POST["id"] ?? 0);
    if (!$encuesta->obtener($id)) { responder(false, "Encuesta no encontrada"); }
    $encuesta->eliminar($id);
    responder(true, "Encuesta eliminada correctamente");
}

if ($accion === "responder") {
    $datos = leerJson();
    $id = (int)($datos["encuesta_id"] ?? 0);
    $respuestas = $datos["respuestas"] ?? [];
    $actual = $encuesta->obtener($id);
    if (!$actual || !$actual["activa"]) { responder(false, "La encuesta ya no está disponible"); }
    $preguntas = $encuesta->preguntas($id);
    if (empty($preguntas) || count($preguntas) !== count($respuestas)) {
        responder(false, "Debe responder todas las preguntas");
    }
    foreach ($respuestas as $respuesta) {
        if (empty($respuesta["pregunta_id"]) || trim($respuesta["respuesta"] ?? "") === "") {
            responder(false, "Debe responder todas las preguntas");
        }
    }
    foreach ($preguntas as $pregunta) {
        $encontrada = false;
        foreach ($respuestas as $respuesta) {
            if ($respuesta["pregunta_id"] == $pregunta["id"]) { $encontrada = true; }
        }
        if (!$encontrada) { responder(false, "Las preguntas cambiaron. Recargue la encuesta"); }
    }
    try {
        $encuesta->guardarRespuestas($respuestas);
        responder(true, "Respuestas anónimas guardadas correctamente");
    } catch (PDOException $error) {
        responder(false, "No se pudieron guardar las respuestas. Recargue la encuesta");
    }
}

if ($accion === "crear" || $accion === "editar") {
    $datos = leerJson();
    $titulo = trim($datos["titulo"] ?? "");
    $descripcion = trim($datos["descripcion"] ?? "");
    $activa = !empty($datos["activa"]) ? 1 : 0;
    $preguntas = $datos["preguntas"] ?? [];
    if ($titulo === "" || strlen($titulo) > 150 || empty($preguntas)) {
        responder(false, "Ingrese un título de hasta 150 caracteres y al menos una pregunta");
    }
    foreach ($preguntas as $pregunta) {
        $texto = $accion === "crear" ? $pregunta : ($pregunta["pregunta"] ?? "");
        if (trim($texto) === "") { responder(false, "Complete todas las preguntas"); }
    }
    if ($accion === "crear") {
        try {
            $encuesta->crear($titulo, $descripcion, $_SESSION["usuario_id"], $activa, $preguntas);
            responder(true, "Encuesta creada correctamente");
        } catch (PDOException $error) {
            responder(false, "No se pudo crear la encuesta");
        }
    }

    $id = (int)($datos["id"] ?? 0);
    if (!$encuesta->obtener($id)) { responder(false, "Encuesta no encontrada"); }
    $guardadas = $encuesta->preguntas($id);
    $idsGuardados = [];
    foreach ($guardadas as $pregunta) { $idsGuardados[] = $pregunta["id"]; }
    $idsActuales = [];
    foreach ($preguntas as $pregunta) {
        $preguntaId = $pregunta["id"] ?? "";
        if ($preguntaId !== "") {
            if (!in_array($preguntaId, $idsGuardados) || in_array($preguntaId, $idsActuales)) {
                responder(false, "Revise las preguntas de esta encuesta");
            }
            $idsActuales[] = $preguntaId;
        }
    }
    $quitar = [];
    foreach ($idsGuardados as $preguntaId) {
        if (!in_array($preguntaId, $idsActuales)) {
            if ($encuesta->cantidadRespuestas($preguntaId) > 0) {
                responder(false, "No se puede quitar una pregunta que ya tiene respuestas");
            }
            $quitar[] = $preguntaId;
        }
    }
    try {
        $encuesta->editar($id, $titulo, $descripcion, $activa, $preguntas, $quitar);
        responder(true, "Encuesta actualizada correctamente");
    } catch (PDOException $error) {
        responder(false, "No se pudo editar la encuesta. Recargue los datos");
    }
}
responder(false, "Acción no válida");
