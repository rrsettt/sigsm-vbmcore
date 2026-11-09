const tablaDocumentos = document.querySelector("#tablaDocumentos");

async function cargarDocumentos() {
    try {
        const respuesta = await fetch("../php/documentos.php?action=listar");
        const resultado = await respuesta.json();
        if (!resultado.ok) { alert(resultado.mensaje); return; }
        mostrarDocumentos(resultado.documentos);
    } catch (error) { tablaDocumentos.innerHTML = '<tr><td colspan="6">No se pudieron cargar los documentos.</td></tr>'; }
}

function mostrarDocumentos(documentos) {
    tablaDocumentos.innerHTML = "";
    const revision = document.querySelector("#filtroRevision").value;
    documentos.forEach(function (documento) {
        if (revision !== "" && documento.estado_revision !== revision) { return; }
        const fila = document.createElement("tr");
        fila.innerHTML = `
            <td></td><td></td><td></td><td></td><td></td>
            <td><a class="btnEditar" href="editarDocumento.php?id=${documento.id}">Editar</a>
                <button class="btnEliminar" onclick="eliminarDocumento(${documento.id})">Eliminar</button></td>`;
        fila.cells[0].textContent = documento.titulo;
        fila.cells[1].textContent = documento.categoria;
        fila.cells[2].textContent = new Date(documento.fecha_publicacion).toLocaleDateString("es-UY");
        fila.cells[3].textContent = documento.estado_revision;
        fila.cells[4].textContent = documento.motivo_rechazo || "";
        tablaDocumentos.appendChild(fila);
    });
    if (tablaDocumentos.children.length === 0) {
        tablaDocumentos.innerHTML = '<tr><td colspan="6">No hay documentos para mostrar.</td></tr>';
    }
}

async function eliminarDocumento(id) {
    if (!confirm("¿Seguro que desea eliminar este documento?")) { return; }
    const datos = new FormData();
    datos.append("id", id);
    try {
        const respuesta = await fetch("../php/documentos.php?action=eliminar", {method: "POST", body: datos});
        const resultado = await respuesta.json();
        alert(resultado.mensaje);
        cargarDocumentos();
    } catch (error) { alert("No se pudo eliminar el documento"); }
}
document.querySelector("#filtroRevision").addEventListener("change", cargarDocumentos);
cargarDocumentos();
