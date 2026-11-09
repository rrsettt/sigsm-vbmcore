<?php
require_once "../php/sesion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo traslado</title>
    <link rel="stylesheet" href="../assets/css/estiloadmin.css?v=10">
</head>
<body>
<header class="header">
    <a class="logo" href="../index.html"><img src="../assets/img/Logo_Hc.png" alt="Hospital de Clínicas"></a>
    <a class="btn btn-secundario" href="../modulo1/admins.php">← Volver al panel</a>
</header>
<main class="dashboard">
    <h1>Nuevo traslado</h1>

    <form id="formTraslado">
        <input type="hidden" name="id" id="id">
        <input type="hidden" name="version" id="version">
        <label for="cedula">Cédula del paciente</label>
        <input type="text" id="cedula" name="cedula" maxlength="20" required>
        <label for="nombre">Nombre del paciente</label>
        <input type="text" id="nombre" name="nombre" maxlength="50" required>
        <label for="apellido">Apellido del paciente</label>
        <input type="text" id="apellido" name="apellido" maxlength="50" required>
        <label for="conductor">Conductor</label>
        <input type="text" id="conductor" name="conductor" maxlength="100" required>
        <label for="enfermero">Enfermero</label>
        <input type="text" id="enfermero" name="enfermero" maxlength="100" required>
        <label for="vehiculo">Vehículo</label>
        <input type="text" id="vehiculo" name="vehiculo" maxlength="50" required>
        <label for="origen">Origen</label>
        <input type="text" id="origen" name="origen" maxlength="150" required>
        <label for="destino">Destino</label>
        <input type="text" id="destino" name="destino" maxlength="150" required>
        <p>La salida y la llegada se registran al cambiar el estado.</p>
        <label for="observaciones">Observaciones</label><textarea name="observaciones" id="observaciones"></textarea>
        <p id="mensaje"></p>
        <button type="submit" class="btn">Guardar traslado</button>
    </form>
</main>
<script src="../assets/js/nuevoTraslado.js?v=12"></script>
<footer class="pie-panel">S.I.G.M.S · Hospital de Clínicas <span>Desarrollado por <strong>VBMCore</strong></span></footer>
</body>
</html>
