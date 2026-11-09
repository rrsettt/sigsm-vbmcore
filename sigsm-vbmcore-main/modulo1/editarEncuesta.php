<?php
require_once "../php/sesion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/estiloadmin.css?v=10">
    <title>Editar Encuesta</title>
</head>

<body>

<header class="header">

    <a href="../index.html" class="logo">
        <img src="../assets/img/Logo_Hc.png" alt="Hospital">
    </a>

    <a class="btn btn-secundario" href="encuestasAdmin.php">
        ← Volver
    </a>

</header>

<section class="dashboard">

    <h1>Editar Encuesta</h1>

    <form id="formEncuesta">

        <label for="titulo">
            Título
        </label>

        <input
            type="text"
            id="titulo"
                maxlength="150"
            required
        >

        <br><br>

        <label for="descripcion">
            Descripción
        </label>

        <textarea
            id="descripcion"
            rows="4"
        ></textarea>

        <br><br>

        <label>
            <input
                type="checkbox"
                id="activa"
            >
            Encuesta activa
        </label>

        <h3>Preguntas</h3>

        <div id="preguntas">
            Cargando preguntas...
        </div>

        <br>

        <button
            type="button"
            id="agregarPregunta"
            class="btn"
        >
            Agregar pregunta
        </button>

        <br><br>

        <p id="mensaje"></p>

        <button
            type="submit"
            class="btn"
        >
            Guardar cambios
        </button>

    </form>

</section>
<script src="../assets/js/editarEncuesta.js"></script>

<footer class="pie-panel">S.I.G.M.S · Hospital de Clínicas <span>Desarrollado por <strong>VBMCore</strong></span></footer>
</body>
</html>
