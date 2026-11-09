const formulario = document.querySelector("#formEncuesta");
const titulo = document.querySelector("#tituloEncuesta");
const descripcion = document.querySelector("#descripcionEncuesta");
const preguntas = document.querySelector("#preguntas");
const mensaje = document.querySelector("#mensaje");
const selector = document.querySelector("#selectorEncuesta");
let encuestaId = null;

async function cargarEncuestas() {
    try {
        const respuesta = await fetch("../php/encuestas.php?action=publicas");
        const resultado = await respuesta.json();
        if (!resultado.ok) { descripcion.textContent = resultado.mensaje; return; }
        selector.innerHTML = "";
        if (resultado.encuestas.length === 0) {
            titulo.textContent = "No hay encuestas disponibles";
            descripcion.textContent = "Volvé a consultar más adelante.";
            const opcion = document.createElement("option");
            opcion.textContent = "Sin encuestas activas";
            selector.appendChild(opcion);
            return;
        }
        resultado.encuestas.forEach(function (encuesta) {
            const opcion = document.createElement("option");
            opcion.value = encuesta.id;
            opcion.textContent = encuesta.titulo;
            selector.appendChild(opcion);
        });
        selector.disabled = false;
        cargarEncuesta();
    } catch (error) { descripcion.textContent = "No se pudieron cargar las encuestas"; }
}

async function cargarEncuesta() {
    const idSolicitado = selector.value;
    encuestaId = null;
    formulario.style.display = "none";
    preguntas.innerHTML = "";
    mensaje.textContent = "";
    titulo.textContent = "Cargando encuesta...";
    descripcion.textContent = "";
    try {
        const respuesta = await fetch(
            "../php/encuestas.php?action=obtener&id=" + idSolicitado
        );
        const resultado = await respuesta.json();

        if (selector.value !== idSolicitado) { return; }
        if (!resultado.ok) {
            titulo.textContent = "Encuesta no disponible";
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

        if (selector.value !== idSolicitado) { return; }
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

            </label>

            <textarea
                id="pregunta${pregunta.id}"

                data-id="${pregunta.id}"

                rows="3"
                required
            ></textarea>

        `;
        contenedor.querySelector("label").textContent = pregunta.pregunta;
        preguntas.appendChild(contenedor);
    });
}

formulario.addEventListener(
    "submit",
    async function (event) {
        event.preventDefault();
        if (!encuestaId) { mensaje.textContent = "Seleccione una encuesta disponible"; return; }

        mensaje.textContent = "Enviando...";
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
        let incompleta = false;
        respuestas.forEach(function (respuesta) {
            if (respuesta.respuesta === "") { incompleta = true; }
        });
        if (incompleta) { mensaje.textContent = "Debe responder todas las preguntas"; return; }
        selector.disabled = true;
        const boton = formulario.querySelector("button[type=submit]");
        boton.disabled = true;
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
        } finally {
            selector.disabled = false;
            boton.disabled = false;
        }
    }
);
selector.addEventListener("change", cargarEncuesta);
cargarEncuestas();
