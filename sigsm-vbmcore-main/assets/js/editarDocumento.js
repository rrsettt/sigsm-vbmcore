const formulario = document.querySelector("#formDocumento");
const categoria = document.querySelector("#categoria");
const mensaje = document.querySelector("#mensaje");

const parametros = new URLSearchParams(window.location.search);

const id = parametros.get("id");

async function cargarCategorias() {

    try {

        const respuesta = await fetch(
            "../php/documentos.php?action=categorias"
        );

        const resultado = await respuesta.json();

        if (!resultado.ok) {

            mensaje.textContent =
                "No se pudieron cargar las categorías.";

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

        mensaje.textContent =
            "Error al cargar las categorías.";
    }
}

async function cargarDocumento() {

    try {

        const respuesta = await fetch(
            "../php/documentos.php?action=obtener&id=" + id
        );

        const resultado = await respuesta.json();

        if (!resultado.ok) {

            mensaje.textContent = resultado.mensaje;

            return;
        }

        const documento = resultado.documento;

        document.querySelector("#id").value =
            documento.id;

        document.querySelector("#titulo").value =
            documento.titulo;

        document.querySelector("#descripcion").value =
            documento.descripcion || "";

        document.querySelector("#categoria").value =
            documento.categoria_id;

        document.querySelector("#activo").checked =
            documento.activo == 1;

    } catch (error) {

        console.error(error);

        mensaje.textContent =
            "Error al cargar el documento.";
    }
}

formulario.addEventListener(
    "submit",
    async function (evento) {

        evento.preventDefault();

        mensaje.textContent = "Guardando...";

        const datos = new FormData(formulario);

        try {

            const respuesta = await fetch(
                "../php/documentos.php?action=editar",
                {
                    method: "POST",
                    body: datos
                }
            );

            const resultado = await respuesta.json();

            if (!resultado.ok) {

                mensaje.textContent =
                    resultado.mensaje;

                return;
            }

            window.location.href =
                "documentosAdmin.html";

        } catch (error) {

            console.error(error);

            mensaje.textContent =
                "Error al editar el documento.";
        }
    }
);

async function iniciar() {

    if (!id) {

        mensaje.textContent =
            "No se encontró el documento.";

        return;
    }

    await cargarCategorias();

    await cargarDocumento();
}

iniciar();