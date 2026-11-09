<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../modulo1/login.html");
    exit;
}
$rolPermitido = $rolPermitido ?? "FUNCIONARIO";
if ($_SESSION["rol"] !== $rolPermitido) {
    header("Location: ../index.html");
    exit;
}
