<?php

require_once "../datos/conexion.php";

try {

    $consulta = $conexion->prepare("
        SELECT
            id,
            titulo,
            descripcion,
            fecha_publicacion,
            activo
        FROM documentos
        WHERE activo = TRUE
        ORDER BY fecha_publicacion DESC
    ");

    $consulta->execute();

    $documentos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    header("Content-Type: application/json; charset=UTF-8");

    echo json_encode($documentos);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "error" => "No se pudieron obtener los documentos."
    ]);

}