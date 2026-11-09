<?php
require_once "../php/sesion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de traslados</title>
    <link rel="stylesheet" href="../assets/css/estiloadmin.css?v=12">
</head>
<body>
<header class="header">
    <a class="logo" href="../index.html"><img src="../assets/img/Logo_Hc.png" alt="Hospital de Clínicas"></a>
    <a class="btn btn-secundario" href="../modulo1/admins.php">← Volver al panel</a>
</header>
<main class="dashboard">
    <section class="titulo-pagina">
        <h1>Solicitudes y traslados</h1>
        <p>Consultá el estado de las ambulancias y registrá sus salidas y llegadas.</p>
    </section>
    <section class="resumen-estados">
        <article class="resumen-card"><span class="estado pendiente">Pendiente</span><strong id="cantidadPendientes">0</strong></article>
        <article class="resumen-card"><span class="estado curso">En curso</span><strong id="cantidadEnCurso">0</strong></article>
        <article class="resumen-card"><span class="estado aprobado">Llegó</span><strong id="cantidadLlegaron">0</strong></article>
        <article class="resumen-card"><span class="estado cancelado">Cancelado</span><strong id="cantidadCancelados">0</strong></article>
    </section>
    <div class="barra-herramientas">
        <a class="btn" href="nuevoTraslado.php">+ Solicitar traslado</a>
        <span class="ayuda">Los estados se actualizan cada 10 segundos.</span>
    </div>
    <div class="filtros-traslados">
    <div>
    <label for="filtroEstado">Estado</label>
    <select id="filtroEstado"><option value="">Todos</option><option>Pendiente</option><option value="En traslado">En curso</option><option value="Finalizado">Llegó</option><option>Cancelado</option></select>
    </div>
    <div><label for="filtroPaciente">Paciente o cédula</label><input type="text" id="filtroPaciente" class="campo" placeholder="Buscar paciente"></div>
    <div><label for="filtroVehiculo">Vehículo</label><input type="text" id="filtroVehiculo" class="campo" placeholder="Buscar vehículo"></div>
    </div>
    <p id="mensaje"></p>
    <section id="listaTraslados" class="lista-traslados"></section>
</main>
<script src="../assets/js/traslados.js?v=12"></script>
<footer class="pie-panel">S.I.G.M.S · Hospital de Clínicas <span>Desarrollado por <strong>VBMCore</strong></span></footer>
</body>
</html>
