const formulario = document.querySelector("#formEncuesta");
const titulo = document.querySelector("#tituloEncuesta");
const descripcion = document.querySelector("#descripcionEncuesta");
const preguntas = document.querySelector("#preguntas");
const mensaje = document.querySelector("#mensaje");

let encuestaId = null;

async function cargarEncuesta() {
    try {
        const respuesta = await fetch(
            "../php/encuestas.php?action=obtener"
        );

        const resultado = await respuesta.json();

        if (!resultado.ok) {
            descripcion.textContent = resultado.mensaje;
            return;
        }

        encuestaId = resultado.encuesta.id;

        titulo.textContent =
            resultado.encuesta.titulo;

        descripcion.textContent =
            resultado.encuesta.descripcion || "";

        mostrarPreguntas(resultado.preguntas);

        formulario.style.display = "block";

    } catch (error) {
        console.error(error);

        descripcion.textContent =
            "Error al cargar la encuesta.";
    }
}


function mostrarPreguntas(lista) {
    preguntas.innerHTML = "";

    lista.forEach(function (pregunta) {

        const contenedor =
            document.createElement("div");

        contenedor.innerHTML = `
            <label for="pregunta${pregunta.id}">
                ${pregunta.pregunta}
            </label>

            <textarea
                id="pregunta${pregunta.id}"
                data-id="${pregunta.id}"
                rows="3"
                required
            ></textarea>

            <br><br>
        `;

        preguntas.appendChild(contenedor);
    });
}


formulario.addEventListener(
    "submit",
    async function (event) {

        event.preventDefault();

        mensaje.textContent = "Enviando...";

        const cedula =
            document.querySelector("#cedula")
                .value
                .trim();

        const campos =
            document.querySelectorAll(
                "#preguntas textarea"
            );

        const respuestas = [];

        campos.forEach(function (campo) {

            respuestas.push({
                pregunta_id: campo.dataset.id,
                respuesta: campo.value.trim()
            });

        });


        try {

            const respuesta = await fetch(
                "../php/encuestas.php?action=responder",
                {
                    method: "POST",

                    headers: {
                        "Content-Type":
                            "application/json"
                    },

                    body: JSON.stringify({
                        encuesta_id: encuestaId,
                        cedula: cedula,
                        respuestas: respuestas
                    })
                }
            );

            const resultado =
                await respuesta.json();

            mensaje.textContent =
                resultado.mensaje;

            if (resultado.ok) {
                formulario.reset();
            }

        } catch (error) {

            console.error(error);

            mensaje.textContent =
                "Error al enviar las respuestas.";
        }
    }
);


cargarEncuesta();