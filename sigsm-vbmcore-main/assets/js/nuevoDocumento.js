const formulario = document.querySelector("#formDocumento");
const categoria = document.querySelector("#categoria");
const mensaje = document.querySelector("#mensaje");

async function cargarCategorias() {

    try {

        const respuesta = await fetch("../php/documentos.php?action=categorias");

        const resultado = await respuesta.json();

        if (!resultado.ok) {
            mensaje.textContent = "No se pudieron cargar las categorías.";
            return;
        }

        resultado.categorias.forEach(function (item) {

            const opcion = document.createElement("option");

            opcion.value = item.id;
            opcion.textContent = item.nombre;

            categoria.appendChild(opcion);
        });

    } catch (error) {

        console.error(error);

        mensaje.textContent = "Error al cargar las categorías.";
    }
}

formulario.addEventListener("submit", async function (evento) {

    evento.preventDefault();

    mensaje.textContent = "Guardando...";

    const datos = new FormData(formulario);

    try {

        const respuesta = await fetch("../php/documentos.php?action=crear", {
            method: "POST",
            body: datos
        });

        const resultado = await respuesta.json();

        if (!resultado.ok) {
            mensaje.textContent = resultado.mensaje;
            return;
        }

        window.location.href = "documentosAdmin.html";

    } catch (error) {

        console.error(error);

        mensaje.textContent = "Error al guardar el documento.";
    }
});

cargarCategorias();