<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["ok" => false]);
    exit;
}

echo json_encode([
    "ok" => true,
    "usuario" => $_SESSION["usuario"],
    "nombre" => $_SESSION["nombre"],
    "rol" => $_SESSION["rol"]
]);