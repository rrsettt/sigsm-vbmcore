<?php
require_once "../php/sesion.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/estiloadmin.css?v=10">

    <title>Respuestas de Encuesta</title>
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

    <h1 id="tituloEncuesta">
        Respuestas de Encuesta
    </h1>

    <p id="mensaje"></p>

    <div class="tabla-contenedor"><table>

        <thead>
            <tr>
                <th>Pregunta</th>
                <th>Respuesta</th>
                <th>Fecha</th>
            </tr>
        </thead>

        <tbody id="tablaRespuestas">

            <tr>
                <td colspan="3">
                    Cargando respuestas...
                </td>
            </tr>

        </tbody>

    </table></div>

</section>

<script>

const tabla = document.querySelector("#tablaRespuestas");
const titulo = document.querySelector("#tituloEncuesta");
const mensaje = document.querySelector("#mensaje");

const parametros = new URLSearchParams(window.location.search);
const id = parametros.get("id");

async function cargarRespuestas() {

    if (!id) {
        mensaje.textContent = "Encuesta no válida.";
        return;
    }

    try {

        const respuesta = await fetch(
            "../php/encuestas.php?action=respuestas&id=" + id
        );

        const resultado = await respuesta.json();

        if (!resultado.ok) {
            mensaje.textContent = resultado.mensaje;
            return;
        }

        titulo.textContent =
            "Respuestas: " + resultado.titulo;

        tabla.innerHTML = "";

        if (resultado.respuestas.length === 0) {

            tabla.innerHTML = `
                <tr>
                    <td colspan="3">
                        Todavía no hay respuestas.
                    </td>
                </tr>
            `;

            return;
        }

        resultado.respuestas.forEach(function(respuesta) {

            const fila = document.createElement("tr");

            fila.innerHTML = `

                <td></td>
                <td></td>
                <td></td>
            `;

            fila.cells[0].textContent = respuesta.pregunta;
            fila.cells[1].textContent = respuesta.respuesta;
            fila.cells[2].textContent = respuesta.fecha;
            tabla.appendChild(fila);
        });

    } catch (error) {

        console.error(error);

        mensaje.textContent =
            "Error al cargar las respuestas.";
    }
}

cargarRespuestas();

</script>

<footer class="pie-panel">S.I.G.M.S · Hospital de Clínicas <span>Desarrollado por <strong>VBMCore</strong></span></footer>
</body>
</html>
