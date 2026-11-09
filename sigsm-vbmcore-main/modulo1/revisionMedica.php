<?php
$rolPermitido = "MEDICO";
require_once "../php/sesion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisión de documentos</title>
    <link rel="stylesheet" href="../assets/css/estiloadmin.css?v=10">
</head>
<body data-login="../modulo1/login.html">
<header class="header">
    <a class="logo" href="../index.html"><img src="../assets/img/Logo_Hc.png" alt="Hospital de Clínicas"></a>
    <nav class="acciones-header">
        <a class="btn btn-secundario" href="../index.html">Volver</a>
        <button class="btn btn-peligro" id="cerrarSesion" type="button">Cerrar sesión</button>
    </nav>
</header>
<main class="dashboard">
    <section class="titulo-pagina cabecera-personal">
        <h1>Portal médico</h1>
        <p>Revisión de documentos pendientes. Abra el PDF antes de decidir y escriba el motivo si lo rechaza.</p>
    </section>
    <p id="mensaje"></p>
    <div class="tabla-contenedor"><table>
        <thead><tr><th>Documento</th><th>Categoría</th><th>PDF</th><th>Motivo</th><th>Decisión</th></tr></thead>
        <tbody id="tablaRevisiones"></tbody>
    </table></div>
</main>
<script src="../assets/js/logout.js"></script>
<script src="../assets/js/revisionMedica.js"></script>
<footer class="pie-panel">S.I.G.M.S · Hospital de Clínicas <span>Desarrollado por <strong>VBMCore</strong></span></footer>
</body>
</html>
