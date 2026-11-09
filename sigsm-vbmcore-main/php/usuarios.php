<?php
session_start();
require_once "../datos/conexion.php";
require_once "../datos/Usuario.php";
header("Content-Type: application/json; charset=UTF-8");

function responder($ok, $mensaje)
{
    echo json_encode(["ok" => $ok, "mensaje" => $mensaje]);
    exit;
}

if (!isset($_SESSION["usuario_id"]) || $_SESSION["rol"] !== "FUNCIONARIO") {
    http_response_code(403);
    responder(false, "No tiene permiso para crear cuentas");
}
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responder(false, "Debe enviar el formulario");
}
$nombre = trim($_POST["nombre"] ?? "");
$apellido = trim($_POST["apellido"] ?? "");
$nombreUsuario = trim($_POST["usuario"] ?? "");
$password = $_POST["password"] ?? "";
$rol = $_POST["rol"] ?? "";
if ($nombre === "" || $apellido === "" || $nombreUsuario === "" || strlen($password) < 8) {
    responder(false, "Complete los datos y use una contraseña de al menos 8 caracteres");
}
if (strlen($nombre) > 50 || strlen($apellido) > 50 || strlen($nombreUsuario) > 50 || strlen($password) > 72) {
    responder(false, "Revise la longitud de los datos ingresados");
}
if (!in_array($rol, ["FUNCIONARIO", "MEDICO"])) {
    responder(false, "Seleccione un rol válido");
}
$usuario = new Usuario($conexion);
if ($usuario->buscarPorUsuario($nombreUsuario)) {
    responder(false, "Ese nombre de usuario ya existe");
}

$hash = password_hash($password, PASSWORD_DEFAULT);
try {
    $usuario->crear($nombre, $apellido, $nombreUsuario, $hash, $rol);
    responder(true, "Cuenta creada correctamente");
} catch (PDOException $error) {
    responder(false, "No se pudo crear la cuenta. Compruebe que el usuario no esté repetido");
}
