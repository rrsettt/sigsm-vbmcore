const formulario = document.querySelector("#formEncuesta");
const preguntas = document.querySelector("#preguntas");
const mensaje = document.querySelector("#mensaje");

document.querySelector("#agregarPregunta").addEventListener("click", function () {

    const input = document.createElement("input");

    input.type = "text";
    input.className = "pregunta";
    input.placeholder = "Nueva pregunta";
    input.required = true;

    preguntas.appendChild(input);
});


formulario.addEventListener("submit", async function (event) {

    event.preventDefault();

    const listaPreguntas = [];

    document.querySelectorAll(".pregunta").forEach(function (input) {

        if (input.value.trim() !== "") {
            listaPreguntas.push(input.value.trim());
        }

    });


    const datos = {
        titulo: document.querySelector("#titulo").value.trim(),
        descripcion: document.querySelector("#descripcion").value.trim(),
        activa: document.querySelector("#activa").checked ? 1 : 0,
        preguntas: listaPreguntas
    };


    try {

        const respuesta = await fetch(
            "../php/encuestas.php?action=crear",
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

        if (resultado.ok) {
            formulario.reset();

            preguntas.innerHTML = `
                <input
                    type="text"
                    class="pregunta"
                    placeholder="Pregunta 1"
                    required
                >
            `;
        }

    } catch (error) {

        console.error(error);

        mensaje.textContent =
            "Error al guardar la encuesta.";
    }

});