<?php
require_once "../php/sesion.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/estiloadmin.css?v=10">
    <title>Administrar Encuestas</title>
</head>

<body>

<header class="header">

    <a
        href="../index.html"
        class="logo"
    >
        <img
            src="../assets/img/Logo_Hc.png"
            alt="Hospital de Clínicas"
        >
    </a>

    <a
        href="admins.php"
        class="btn btn-secundario"
    >
        ← Volver al panel
    </a>

</header>

<main class="dashboard">

    <h1>
        Administración de Encuestas
    </h1>

    <a
        href="nuevaEncuesta.php"
        class="btn"
    >
        Nueva Encuesta
    </a>

    <p id="mensaje"></p>

    <div class="tabla-contenedor"><table>

        <thead>
            <tr>
                <th>Título</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody id="tablaEncuestas">

            <tr>
                <td colspan="4">
                    Cargando encuestas...
                </td>
            </tr>

        </tbody>

    </table></div>

</main>
<script src="../assets/js/encuestasAdmin.js?v=10"></script>

<footer class="pie-panel">S.I.G.M.S · Hospital de Clínicas <span>Desarrollado por <strong>VBMCore</strong></span></footer>
</body>
</html>
