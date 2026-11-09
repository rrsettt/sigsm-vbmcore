<?php
session_start();
require_once "../datos/conexion.php";
require_once "../datos/Documento.php";

$documento = new Documento($conexion);
$datos = $documento->obtener((int)($_GET["id"] ?? 0));
if (!$datos) {
    http_response_code(404);
    exit("Documento no encontrado");
}
if (!$datos["activo"] || $datos["estado_revision"] !== "Aprobado") {
    if (!isset($_SESSION["rol"]) || !in_array($_SESSION["rol"], ["FUNCIONARIO", "MEDICO"])) {
        http_response_code(403);
        exit("Debe iniciar sesión para consultar este documento");
    }
}
$ruta = "../uploads/documentos/" . basename($datos["archivo"]);
if (!is_file($ruta)) {
    http_response_code(404);
    exit("No se encontró el archivo PDF");
}
header("Content-Type: application/pdf");
readfile($ruta);
