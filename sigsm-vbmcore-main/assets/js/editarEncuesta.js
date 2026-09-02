const formulario = document.querySelector("#formEncuesta");
const titulo = document.querySelector("#titulo");
const descripcion = document.querySelector("#descripcion");
const activa = document.querySelector("#activa");
const preguntas = document.querySelector("#preguntas");
const mensaje = document.querySelector("#mensaje");

const parametros = new URLSearchParams(window.location.search);
const encuestaId = parametros.get("id");

async function cargarEncuesta() {
    if (!encuestaId) {
        mensaje.textContent = "Encuesta no válida.";
        return;
    }

    try {
        const respuesta = await fetch(
            "../php/encuestas.php?action=editarDatos&id=" + encuestaId
        );

        const resultado = await respuesta.json();

        if (!resultado.ok) {
            mensaje.textContent = resultado.mensaje;
            return;
        }

        titulo.value = resultado.encuesta.titulo;
        descripcion.value = resultado.encuesta.descripcion || "";
        activa.checked = resultado.encuesta.activa == 1;

        preguntas.innerHTML = "";

        resultado.preguntas.forEach(function (pregunta) {
            agregarPregunta(
                pregunta.pregunta,
                pregunta.id
            );
        });

    } catch (error) {
        mensaje.textContent = "Error al cargar la encuesta.";
    }
}

function agregarPregunta(texto = "", id = "") {
    const contenedor = document.createElement("div");

    contenedor.dataset.id = id;

    const input = document.createElement("input");
    input.type = "text";
    input.className = "pregunta";
    input.value = texto;
    input.required = true;

    const boton = document.createElement("button");
    boton.type = "button";
    boton.textContent = "Quitar";
    boton.className = "eliminarPregunta";

    boton.addEventListener("click", function () {
        contenedor.remove();
    });

    contenedor.appendChild(input);
    contenedor.appendChild(boton);
    contenedor.appendChild(document.createElement("br"));
    contenedor.appendChild(document.createElement("br"));

    preguntas.appendChild(contenedor);
}

document
    .querySelector("#agregarPregunta")
    .addEventListener("click", function () {
        agregarPregunta();
    });

formulario.addEventListener("submit", async function (event) {
    event.preventDefault();

    const listaPreguntas = [];

    preguntas.querySelectorAll("div").forEach(function (contenedor) {
        const input = contenedor.querySelector(".pregunta");

        if (input && input.value.trim() !== "") {
            listaPreguntas.push({
                id: contenedor.dataset.id,
                pregunta: input.value.trim()
            });
        }
    });

    if (listaPreguntas.length === 0) {
        mensaje.textContent =
            "Debe existir al menos una pregunta.";
        return;
    }

    const datos = {
        id: encuestaId,
        titulo: titulo.value.trim(),
        descripcion: descripcion.value.trim(),
        activa: activa.checked ? 1 : 0,
        preguntas: listaPreguntas
    };

    try {
        const respuesta = await fetch(
            "../php/encuestas.php?action=editar",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(datos)
            }
        );

        const resultado = await respuesta.json();

        mensaje.textContent = resultado.mensaje;

    } catch (error) {
        mensaje.textContent =
            "Error al guardar los cambios.";
    }
});

cargarEncuesta();