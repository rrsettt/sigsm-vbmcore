<?php

session_start();

require_once "../datos/conexion.php";
require_once "clases/Documento.php";

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

    $nombre = time() . "_" . basename($archivo["name"]);
    $ruta = "../uploads/" . $carpeta . "/" . $nombre;

    if (!move_uploaded_file($archivo["tmp_name"], $ruta)) {
        return false;
    }

    return $nombre;
}

if ($accion !== "publicos") {
    if (
        !isset($_SESSION["usuario_id"]) ||
        $_SESSION["rol"] !== "FUNCIONARIO"
    ) {
        http_response_code(401);
        responder(false, "No autorizado");
    }
}

if ($accion === "listar") {
    responder(true, "", [
        "documentos" => $documento->listar()
    ]);
}

if ($accion === "publicos") {
    responder(true, "", [
        "documentos" => $documento->listarPublicos()
    ]);
}

if ($accion === "categorias") {
    $sql = "SELECT id, nombre
            FROM categorias
            WHERE activo = 1
            ORDER BY nombre";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    responder(true, "", [
        "categorias" => $stmt->fetchAll()
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
    $titulo = trim($_POST["titulo"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $categoriaId = $_POST["categoria_id"] ?? "";
    $activo = isset($_POST["activo"]) ? 1 : 0;

    if ($titulo === "" || $categoriaId === "") {
        responder(false, "Complete los campos obligatorios");
    }

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
        responder(false, "Solo se permiten archivos PDF");
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
            responder(false, "La portada debe ser JPG, PNG o WEBP");
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

    responder(true, "Documento guardado correctamente");
}

if ($accion === "editar") {
    $id = $_POST["id"] ?? "";
    $titulo = trim($_POST["titulo"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $categoriaId = $_POST["categoria_id"] ?? "";
    $activo = isset($_POST["activo"]) ? 1 : 0;

    if (
        $id === "" ||
        $titulo === "" ||
        $categoriaId === ""
    ) {
        responder(false, "Complete los campos obligatorios");
    }

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
            responder(false, "Solo se permiten archivos PDF");
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
            responder(false, "La portada debe ser JPG, PNG o WEBP");
        }

        $nombreImagen = $nuevaImagen;
    }

    $documento->editar(
        $id,
        $titulo,
        $descripcion,
        $nombreArchivo,
        $nombreImagen,
        $categoriaId,
        $activo
    );

    responder(true, "Documento actualizado correctamente");
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