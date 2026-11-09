<?php
require_once "../php/sesion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/estiloadmin.css?v=10">
    <title>Nueva Encuesta</title>
</head>

<body>

<header class="header">
    <a class="logo" href="../index.html"><img src="../assets/img/Logo_Hc.png" alt="Hospital de Clínicas"></a>

    <a href="encuestasAdmin.php" class="btn btn-secundario">
        Volver
    </a>
</header>

<main class="dashboard">

    <h1>Nueva Encuesta</h1>

    <form id="formEncuesta" class="formulario-documento">

        <label for="titulo">Título</label>
        <input type="text" id="titulo" required>

        <label for="descripcion">Descripción</label>
        <textarea id="descripcion"></textarea>

        <label>
            <input type="checkbox" id="activa" checked>
            Publicar encuesta
        </label>

        <h3>Preguntas</h3>

        <div id="preguntas">
            <input
                type="text"
                class="pregunta"
                placeholder="Pregunta 1"
                required
            >
        </div>

        <button type="button" id="agregarPregunta" class="btn">
            Agregar pregunta
        </button>

        <p id="mensaje"></p>

        <button type="submit" class="btn">
            Guardar encuesta
        </button>

    </form>

</main>
<script src="../assets/js/nuevaEncuesta.js"></script>
<footer class="pie-panel">S.I.G.M.S · Hospital de Clínicas <span>Desarrollado por <strong>VBMCore</strong></span></footer>
</body>
</html>
