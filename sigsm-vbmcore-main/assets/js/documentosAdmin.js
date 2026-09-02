const tablaDocumentos = document.querySelector("#tablaDocumentos");

async function cargarDocumentos() {

    try {

        const respuesta = await fetch("../php/documentos.php?action=listar");

        const resultado = await respuesta.json();

        if (!resultado.ok) {

            tablaDocumentos.innerHTML = `
                <tr>
                    <td colspan="5">
                        No se pudieron cargar los documentos.
                    </td>
                </tr>
            `;

            return;
        }

        mostrarDocumentos(resultado.documentos);

    } catch (error) {

        console.error(error);

        tablaDocumentos.innerHTML = `
            <tr>
                <td colspan="5">
                    Error al conectar con el servidor.
                </td>
            </tr>
        `;
    }
}

function mostrarDocumentos(documentos) {

    tablaDocumentos.innerHTML = "";

    if (documentos.length === 0) {

        tablaDocumentos.innerHTML = `
            <tr>
                <td colspan="5">
                    No hay documentos registrados.
                </td>
            </tr>
        `;

        return;
    }

    documentos.forEach(function (documento) {

        const fila = document.createElement("tr");

        const fecha = new Date(
            documento.fecha_publicacion
        ).toLocaleDateString("es-UY");

        const estado = documento.activo == 1
            ? "Publicado"
            : "Inactivo";

        fila.innerHTML = `
            <td>${documento.titulo}</td>
            <td>${documento.categoria}</td>
            <td>${fecha}</td>
            <td>${estado}</td>
            <td>
                <a
                    class="btnEditar"
                    href="editarDocumento.html?id=${documento.id}"
                >
                    Editar
                </a>

                ${
                    documento.activo == 1
                    ? `
                        <button
                            class="btnEliminar"
                            onclick="eliminarDocumento(${documento.id})"
                        >
                            Eliminar
                        </button>
                    `
                    : ""
                }
            </td>
        `;

        tablaDocumentos.appendChild(fila);
    });
}

async function eliminarDocumento(id) {

    const confirmar = confirm(
        "¿Seguro que desea eliminar este documento?"
    );

    if (!confirmar) {
        return;
    }

    const datos = new FormData();

    datos.append("id", id);

    try {

        const respuesta = await fetch(
            "../php/documentos.php?action=eliminar",
            {
                method: "POST",
                body: datos
            }
        );

        const resultado = await respuesta.json();

        if (resultado.ok) {
            cargarDocumentos();
        } else {
            alert(resultado.mensaje);
        }

    } catch (error) {

        console.error(error);

        alert("Error al eliminar el documento.");
    }
}

cargarDocumentos();