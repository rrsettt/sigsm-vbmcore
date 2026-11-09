<?php
session_start();
require_once "../datos/conexion.php";
require_once "../datos/Traslado.php";
header("Content-Type: application/json; charset=UTF-8");

function responder($ok, $mensaje = "", $datos = [])
{
    echo json_encode(array_merge(["ok" => $ok, "mensaje" => $mensaje], $datos));
    exit;
}
if (!isset($_SESSION["usuario_id"]) || $_SESSION["rol"] !== "FUNCIONARIO") {
    http_response_code(401);
    responder(false, "Debe iniciar sesión como funcionario");
}
$traslado = new Traslado($conexion);
$accion = $_GET["action"] ?? "";

if ($accion === "listar") {
    responder(true, "", ["traslados" => $traslado->listar()]);
}
if ($accion === "obtener") {
    $datos = $traslado->obtener((int)($_GET["id"] ?? 0));
    if (!$datos) { responder(false, "El traslado ya no existe"); }
    responder(true, "", ["traslado" => $datos]);
}
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responder(false, "Debe enviar el formulario");
}

if ($accion === "crear" || $accion === "editar") {
    $cedula = trim($_POST["cedula"] ?? "");
    $nombre = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $conductor = trim($_POST["conductor"] ?? "");
    $enfermero = trim($_POST["enfermero"] ?? "");
    $vehiculo = trim($_POST["vehiculo"] ?? "");
    $origen = trim($_POST["origen"] ?? "");
    $destino = trim($_POST["destino"] ?? "");
    $observaciones = trim($_POST["observaciones"] ?? "");
    if ($conductor === "" || $enfermero === "" || $vehiculo === "" || $origen === "" || $destino === "") {
        responder(false, "Complete conductor, enfermero, vehículo, origen y destino");
    }
    if (strlen($conductor) > 100 || strlen($enfermero) > 100 || strlen($vehiculo) > 50 || strlen($origen) > 150 || strlen($destino) > 150) {
        responder(false, "Revise la longitud de los datos del traslado");
    }
    if ($accion === "crear") {
        if ($cedula === "" || $nombre === "" || $apellido === "") {
            responder(false, "Complete los datos del paciente");
        }
        if (strlen($cedula) > 20 || strlen($nombre) > 50 || strlen($apellido) > 50) {
            responder(false, "Revise la longitud de los datos del paciente");
        }
        try {
            $id = $traslado->crear($cedula, $nombre, $apellido, $conductor, $enfermero, $vehiculo, $origen, $destino, $observaciones, $_SESSION["usuario_id"]);
            responder(true, "Traslado registrado como pendiente", ["id" => $id]);
        } catch (PDOException $error) {
            responder(false, "No se pudo guardar el traslado.");
        } catch (Exception $error) {
            responder(false, $error->getMessage());
        }
    }

    $id = (int)($_POST["id"] ?? 0);
    $version = (int)($_POST["version"] ?? 0);
    $actual = $traslado->obtener($id);
    if (!$actual) { responder(false, "El traslado ya no existe"); }
    if ($actual["estado"] === "Cancelado") { responder(false, "No se puede editar un traslado cancelado"); }
    $salida = $_POST["hora_salida"] ?? "";
    $llegada = $_POST["hora_llegada"] ?? "";
    if (!fechaValida($salida) || !fechaValida($llegada)) {
        responder(false, "Revise los horarios ingresados");
    }
    if ($actual["estado"] === "Pendiente" && ($salida !== "" || $llegada !== "")) {
        responder(false, "Primero debe poner el traslado en curso");
    }
    if ($actual["estado"] !== "Pendiente" && $salida === "") {
        responder(false, "Debe conservar la hora de salida");
    }
    if (($actual["estado"] === "En traslado" && $llegada !== "") ||
        ($actual["estado"] === "Finalizado" && $llegada === "") ||
        ($llegada !== "" && strtotime($llegada) < strtotime($salida))) {
        responder(false, "Revise la llegada y el estado del traslado");
    }
    $salida = $salida === "" ? null : str_replace("T", " ", $salida) . ":00";
    $llegada = $llegada === "" ? null : str_replace("T", " ", $llegada) . ":00";
    $guardado = $traslado->editar($id, $version, $conductor, $enfermero, $vehiculo, $origen, $destino, $observaciones, $salida, $llegada);
    if (!$guardado) { responder(false, "Otro funcionario modificó el traslado. Vuelva a abrirlo"); }
    responder(true, "Traslado actualizado", ["id" => $id]);
}

if (in_array($accion, ["iniciar", "finalizar", "cancelar", "eliminar"])) {
    $guardado = $traslado->cambiarEstado((int)($_POST["id"] ?? 0), $accion);
    responder($guardado, $guardado ? "Cambio guardado" : "El traslado ya cambió o fue eliminado");
}
responder(false, "Acción no válida");

function fechaValida($valor)
{
    if ($valor === "") { return true; }
    $fecha = strtotime($valor);
    return $fecha !== false && date("Y-m-d\TH:i", $fecha) === $valor;
}
