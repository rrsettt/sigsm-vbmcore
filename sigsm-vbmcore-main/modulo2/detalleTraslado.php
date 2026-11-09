<?php
require_once "../php/sesion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del traslado</title>
    <link rel="stylesheet" href="../assets/css/estiloadmin.css?v=12">
</head>
<body>
<header class="header">
    <a class="logo" href="../index.html"><img src="../assets/img/Logo_Hc.png" alt="Hospital de Clínicas"></a>
    <a class="btn btn-secundario" href="../modulo1/admins.php">← Volver al panel</a>
</header>
<main class="dashboard">
    <h1>Detalle del traslado</h1>

    <p id="mensaje"></p>
    <section id="detalleTraslado"></section>
    <h2>Referencia del hospital</h2>
    <p>La imagen no muestra la ubicación de la ambulancia.</p>
    <img class="imagen-mapa" src="../assets/img/MapaHospital.png" alt="Mapa del Hospital de Clínicas">
</main>
<script src="../assets/js/traslados.js?v=12"></script>
<footer class="pie-panel">S.I.G.M.S · Hospital de Clínicas <span>Desarrollado por <strong>VBMCore</strong></span></footer>
</body>
</html>
