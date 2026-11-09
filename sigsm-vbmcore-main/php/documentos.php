<?php
session_start();
require_once "../datos/conexion.php";
require_once "../datos/Documento.php";
header("Content-Type: application/json; charset=UTF-8");
$accion = $_GET["action"] ?? "";
$documento = new Documento($conexion);

function responder($ok, $mensaje = "", $datos = [])
{
    echo json_encode(array_merge([
        "ok" => $ok,
        "mensaje" => $mensaje
    ], $datos));
    exit;
}
function subirArchivo($archivo, $carpeta, $extensiones)
{
    if (!$archivo || $archivo["error"] !== UPLOAD_ERR_OK) {
        return null;
    }
    $extension = strtolower(
        pathinfo($archivo["name"], PATHINFO_EXTENSION)
    );
    if (!in_array($extension, $extensiones)) {
        return false;
    }
    if ($archivo["size"] > 10 * 1024 * 1024) { return false; }

    if ($extension === "pdf" && substr(file_get_contents($archivo["tmp_name"]), 0, 5) !== "%PDF-") { return false; }
    if ($extension !== "pdf" && getimagesize($archivo["tmp_name"]) === false) { return false; }
    $nombre = uniqid() . "_" . basename($archivo["name"]);
    $ruta = "../uploads/" . $carpeta . "/" . $nombre;
    if (!move_uploaded_file($archivo["tmp_name"], $ruta)) {
        return false;
    }
    return $nombre;
}

if (!in_array($accion, ["publicos", "categorias"])) {
    if (!isset($_SESSION["usuario_id"])) {
        http_response_code(401);
        responder(false, "Debe iniciar sesión");
    }
    $rol = "FUNCIONARIO";
    if ($accion === "pendientes" || $accion === "revisar") { $rol = "MEDICO"; }
    if ($_SESSION["rol"] !== $rol) {
        http_response_code(403);
        responder(false, "No tiene permiso para esta acción");
    }
}
if (in_array($accion, ["crear", "editar", "eliminar", "revisar"]) && $_SERVER["REQUEST_METHOD"] !== "POST") {
    responder(false, "Debe enviar el formulario");
}
if ($accion === "crear" || $accion === "editar") {
    $titulo = trim($_POST["titulo"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $categoriaId = $_POST["categoria_id"] ?? "";
    $activo = isset($_POST["activo"]) ? 1 : 0;
    if ($titulo === "" || strlen($titulo) > 150) { responder(false, "Ingrese un título de hasta 150 caracteres"); }
    if (!$documento->categoriaValida($categoriaId)) { responder(false, "Seleccione una categoría válida"); }
}
if ($accion === "pendientes") {
    responder(true, "", ["documentos" => $documento->pendientes()]);
}
if ($accion === "revisar") {
    $id = (int)($_POST["id"] ?? 0);
    $version = (int)($_POST["version"] ?? 0);
    $estado = $_POST["estado"] ?? "";
    $motivo = trim($_POST["motivo"] ?? "");
    if (!$id || !$version || !in_array($estado, ["Aprobado", "Rechazado"])) {
        responder(false, "Revise los datos del documento");
    }
    if ($estado === "Rechazado" && $motivo === "") {
        responder(false, "Escriba el motivo del rechazo");
    }
    if ($estado === "Aprobado") { $motivo = ""; }
    if (!$documento->revisar($id, $version, $estado, $motivo, $_SESSION["usuario_id"])) {
        $actual = $documento->obtener($id);
        http_response_code(409);
        if (!$actual) { responder(false, "El documento fue eliminado"); }
        if ($actual["version"] != $version) { responder(false, "El documento fue editado. Actualice la lista"); }
        responder(false, "Este documento ya fue " . $actual["estado_revision"]);
    }
    responder(true, "Decisión guardada");
}

if ($accion === "listar") {
    responder(true, "", [
        "documentos" => $documento->listar()
    ]);
}

if ($accion === "publicos") {
    try {
        responder(true, "", ["documentos" => $documento->listarPublicos()]);
    } catch (PDOException $error) {
        responder(false, "No se pudieron cargar los documentos.");
    }
}

if ($accion === "categorias") {
    responder(true, "", [
        "categorias" => $documento->categorias()
    ]);
}

if ($accion === "obtener") {
    $id = $_GET["id"] ?? "";
    if ($id === "") {
        responder(false, "Documento no válido");
    }
    $resultado = $documento->obtener($id);
    if (!$resultado) {
        responder(false, "Documento no encontrado");
    }
    responder(true, "", [
        "documento" => $resultado
    ]);
}

if ($accion === "crear") {
    if (
        !isset($_FILES["archivo"]) ||
        $_FILES["archivo"]["error"] !== UPLOAD_ERR_OK
    ) {
        responder(false, "Debe seleccionar un archivo PDF");
    }
    $nombreArchivo = subirArchivo(
        $_FILES["archivo"],
        "documentos",
        ["pdf"]
    );
    if ($nombreArchivo === false) {
        responder(false, "Seleccione un PDF válido de hasta 10 MB");
    }
    $nombreImagen = null;
    if (
        isset($_FILES["imagen"]) &&
        $_FILES["imagen"]["error"] === UPLOAD_ERR_OK
    ) {
        $nombreImagen = subirArchivo(
            $_FILES["imagen"],
            "imagenes",
            ["jpg", "jpeg", "png", "webp"]
        );

        if ($nombreImagen === false) {
            responder(false, "Seleccione una portada JPG, PNG o WEBP válida de hasta 10 MB");
        }
    }
    $documento->crear(
        $titulo,
        $descripcion,
        $nombreArchivo,
        $nombreImagen,
        $categoriaId,
        $_SESSION["usuario_id"],
        $activo
    );

    responder(true, "Documento guardado. Quedó pendiente de revisión");
}

if ($accion === "editar") {
    $id = $_POST["id"] ?? "";
    $actual = $documento->obtener($id);

    if (!$actual) {
        responder(false, "Documento no encontrado");
    }
    $nombreArchivo = $actual["archivo"];
    $nombreImagen = $actual["imagen"];
    if (
        isset($_FILES["archivo"]) &&
        $_FILES["archivo"]["error"] === UPLOAD_ERR_OK
    ) {
        $nuevoArchivo = subirArchivo(
            $_FILES["archivo"],
            "documentos",
            ["pdf"]
        );

        if ($nuevoArchivo === false) {
            responder(false, "Seleccione un PDF válido de hasta 10 MB");
        }
        $nombreArchivo = $nuevoArchivo;
    }
    if (
        isset($_FILES["imagen"]) &&
        $_FILES["imagen"]["error"] === UPLOAD_ERR_OK
    ) {

        $nuevaImagen = subirArchivo(
            $_FILES["imagen"],
            "imagenes",
            ["jpg", "jpeg", "png", "webp"]
        );

        if ($nuevaImagen === false) {
            responder(false, "Seleccione una portada JPG, PNG o WEBP válida de hasta 10 MB");
        }

        $nombreImagen = $nuevaImagen;
    }
    $guardado = $documento->editar(
        $id,
        (int)($_POST["version"] ?? 0),
        $titulo,
        $descripcion,
        $nombreArchivo,
        $nombreImagen,
        $categoriaId,
        $activo
    );

    if (!$guardado) { responder(false, "El documento cambió. Vuelva a abrirlo"); }
    responder(true, "Documento actualizado. Quedó pendiente de revisión");
}

if ($accion === "eliminar") {
    $id = $_POST["id"] ?? "";

    if ($id === "") {
        responder(false, "Documento no válido");
    }
    $documento->eliminar($id);

    responder(true, "Documento eliminado correctamente");
}
responder(false, "Acción no válida");
