const tabla = document.querySelector("#tablaEncuestas");
const mensaje = document.querySelector("#mensaje");

async function cargarEncuestas() {
    try {
        const respuesta = await fetch(
            "../php/encuestas.php?action=listar"
        );

        const resultado = await respuesta.json();

        if (!resultado.ok) {
            mensaje.textContent = resultado.mensaje;
            return;
        }

        mostrarEncuestas(resultado.encuestas);

    } catch (error) {
        console.error(error);
        mensaje.textContent = "Error al cargar las encuestas.";
    }
}


function mostrarEncuestas(encuestas) {
    tabla.innerHTML = "";

    if (encuestas.length === 0) {
        tabla.innerHTML = `
            <tr>
                <td colspan="4">No hay encuestas registradas.</td>
            </tr>
        `;
        return;
    }

    encuestas.forEach(function (encuesta) {
        const fila = document.createElement("tr");

        fila.innerHTML = `
            <td>${encuesta.titulo}</td>

            <td>${encuesta.descripcion || ""}</td>

            <td>
                ${encuesta.activa == 1 ? "Activa" : "Inactiva"}
            </td>

            <td>
                <a
                    href="editarEncuesta.html?id=${encuesta.id}"
                    class="btn"
                >
                    Editar
                </a>

                <a
                    href="respuestasEncuesta.html?id=${encuesta.id}"
                    class="btn"
                >
                    Ver respuestas
                </a>

                <button
                    class="btn"
                    onclick="eliminarEncuesta(${encuesta.id})"
                >
                    Eliminar
                </button>
            </td>
        `;

        tabla.appendChild(fila);
    });
}


async function eliminarEncuesta(id) {
    const confirmar = confirm(
        "¿Seguro que desea eliminar esta encuesta?"
    );

    if (!confirmar) {
        return;
    }

    const datos = new FormData();
    datos.append("id", id);

    try {
        const respuesta = await fetch(
            "../php/encuestas.php?action=eliminar",
            {
                method: "POST",
                body: datos
            }
        );

        const resultado = await respuesta.json();

        mensaje.textContent = resultado.mensaje;

        if (resultado.ok) {
            cargarEncuestas();
        }

    } catch (error) {
        console.error(error);
        mensaje.textContent = "Error al eliminar la encuesta.";
    }
}


cargarEncuestas();