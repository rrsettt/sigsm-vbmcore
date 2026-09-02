document.addEventListener("DOMContentLoaded", function () {
    cargarDocumentos();
    cargarEncuesta();
});


async function cargarDocumentos() {

    const contenedor = document.querySelector("#documentos");

    try {

        const respuesta = await fetch(
            "../php/documentos.php?action=publicos"
        );

        const resultado = await respuesta.json();

        if (!resultado.ok) {
            contenedor.innerHTML =
                "<p>No se pudieron cargar los documentos.</p>";
            return;
        }

        mostrarDocumentos(resultado.documentos);

    } catch (error) {

        console.error(error);

        contenedor.innerHTML =
            "<p>Error al cargar los documentos.</p>";
    }
}


function mostrarDocumentos(documentos) {

    const contenedor = document.querySelector("#documentos");

    contenedor.innerHTML = "";

    if (documentos.length === 0) {

        contenedor.innerHTML =
            "<p>No hay documentos disponibles.</p>";

        return;
    }

    documentos.forEach(function (documento) {

        const tarjeta = document.createElement("article");

        tarjeta.classList.add("documento-card");

        let imagen = "../assets/img/Clinicas.png";

        if (documento.imagen) {
            imagen =
                "../uploads/imagenes/" +
                documento.imagen;
        }

        tarjeta.innerHTML = `
            <img
                src="${imagen}"
                alt="${documento.titulo}"
                class="documento-imagen"
            >

            <div class="documento-contenido">

                <span class="documento-categoria">
                    ${documento.categoria}
                </span>

                <h3>${documento.titulo}</h3>

                <p>${documento.descripcion || ""}</p>

                <a
                    href="../uploads/documentos/${documento.archivo}"
                    target="_blank"
                    class="boton"
                >
                    Ver documento
                </a>

            </div>
        `;

        contenedor.appendChild(tarjeta);
    });
}

async function cargarEncuesta() {

    const texto =
        document.querySelector("#encuestaPortal");

    const boton =
        document.querySelector("#botonEncuesta");

    try {

        const respuesta = await fetch(
            "../php/encuestas.php?action=obtener"
        );

        const resultado = await respuesta.json();

        if (!resultado.ok) {

            texto.textContent =
                "No hay encuestas disponibles.";

            return;
        }

        texto.textContent =
            resultado.encuesta.titulo;

        boton.style.display = "inline-block";

    } catch (error) {

        console.error(error);

        texto.textContent =
            "No se pudo cargar la encuesta.";
    }
}