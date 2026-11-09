<?php
session_start();
require_once "../datos/conexion.php";
require_once "../datos/Usuario.php";
header("Content-Type: application/json; charset=UTF-8");
$nombreUsuario = trim($_POST["usuario"] ?? "");
$password = $_POST["password"] ?? "";
if ($nombreUsuario === "" || $password === "") {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Complete todos los campos"
    ]);
    exit;
}
$usuario = new Usuario($conexion);
$datos = $usuario->buscarPorUsuario($nombreUsuario);
if (
    !$datos ||
    !$usuario->verificarPassword(
        $password,
        $datos["password"]
    )
) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Usuario o contraseña incorrectos"
    ]);

    exit;
}

if (!in_array($datos["rol"], ["FUNCIONARIO", "MEDICO"])) {
    echo json_encode(["ok" => false, "mensaje" => "Esta cuenta no tiene acceso al personal"]);
    exit;
}
session_regenerate_id(true);
$_SESSION["usuario_id"] = $datos["id"];
$_SESSION["usuario"] = $datos["usuario"];
$_SESSION["nombre"] = $datos["nombre"];
$_SESSION["rol"] = $datos["rol"];
echo json_encode([
    "ok" => true,
    "rol" => $datos["rol"]
]);
