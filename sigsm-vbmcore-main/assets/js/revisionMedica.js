const tabla = document.querySelector("#tablaRevisiones");
const mensaje = document.querySelector("#mensaje");

async function cargarDocumentos() {
    try {
        const respuesta = await fetch("../php/documentos.php?action=pendientes");
        const resultado = await respuesta.json();
        if (!resultado.ok) { mensaje.textContent = resultado.mensaje; return; }
        tabla.innerHTML = "";
        if (resultado.documentos.length === 0) {
            tabla.innerHTML = '<tr><td colspan="5">No hay documentos pendientes.</td></tr>';
        }
        resultado.documentos.forEach(function (documento) {
            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td></td><td></td>
                <td><a class="btn" href="../php/archivo.php?id=${documento.id}">Abrir PDF</a></td>
                <td><textarea id="motivo${documento.id}" placeholder="Motivo del rechazo"></textarea></td>
                <td>
                    <button class="btn" onclick="revisarDocumento(${documento.id}, ${documento.version}, 'Aprobado')">Aprobar</button>
                    <button class="btn" onclick="revisarDocumento(${documento.id}, ${documento.version}, 'Rechazado')">Rechazar</button>
                </td>`;
            fila.cells[0].textContent = documento.titulo;
            fila.cells[1].textContent = documento.categoria;
            tabla.appendChild(fila);
        });
    } catch (error) { mensaje.textContent = "No se pudieron cargar los documentos"; }
}

async function revisarDocumento(id, version, estado) {
    const motivo = document.querySelector("#motivo" + id).value.trim();
    if (estado === "Rechazado" && motivo === "") {
        mensaje.textContent = "Escriba el motivo del rechazo";
        return;
    }
    if (!confirm("¿Confirmar esta decisión?")) { return; }
    const datos = new FormData();
    datos.append("id", id);
    datos.append("version", version);
    datos.append("estado", estado);
    datos.append("motivo", motivo);
    try {
        const respuesta = await fetch("../php/documentos.php?action=revisar", {method: "POST", body: datos});
        const resultado = await respuesta.json();
        mensaje.textContent = resultado.mensaje;
        cargarDocumentos();
    } catch (error) { mensaje.textContent = "No se pudo guardar la decisión"; }
}
cargarDocumentos();
