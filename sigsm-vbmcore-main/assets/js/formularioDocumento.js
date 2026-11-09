const formulario = document.querySelector("#formDocumento");
const titulo = document.querySelector("#titulo");
const categoria = document.querySelector("#categoria");
const mensaje = document.querySelector("#mensaje");
const parametros = new URLSearchParams(window.location.search);
const id = parametros.get("id");
const editar = window.location.pathname.includes("editarDocumento");

async function cargarCategorias() {
    const respuesta = await fetch("../php/documentos.php?action=categorias");
    const resultado = await respuesta.json();
    if (!resultado.ok) {
        mensaje.textContent = resultado.mensaje || "No se pudieron cargar las categorías";
        return false;
    }
    resultado.categorias.forEach(function (item) {
        const opcion = document.createElement("option");
        opcion.value = item.id;
        opcion.textContent = item.nombre;
        categoria.appendChild(opcion);
    });
    return true;
}

async function cargarDocumento() {
    const respuesta = await fetch("../php/documentos.php?action=obtener&id=" + id);
    const resultado = await respuesta.json();
    if (!resultado.ok) {
        mensaje.textContent = resultado.mensaje;
        return;
    }
    const documento = resultado.documento;
    document.querySelector("#id").value = documento.id;
    document.querySelector("#version").value = documento.version;
    titulo.value = documento.titulo;
    document.querySelector("#descripcion").value = documento.descripcion || "";
    categoria.value = documento.categoria_id;
    document.querySelector("#activo").checked = documento.activo == 1;
}

formulario.addEventListener("submit", async function (evento) {
    evento.preventDefault();
    if (titulo.value.trim() === "" || categoria.value === "") {
        mensaje.textContent = "Complete el título y seleccione una categoría";
        return;
    }
    mensaje.textContent = "Guardando...";
    try {
        const accion = editar ? "editar" : "crear";
        const respuesta = await fetch("../php/documentos.php?action=" + accion, {
            method: "POST",
            body: new FormData(formulario)
        });
        const resultado = await respuesta.json();
        if (!resultado.ok) {
            mensaje.textContent = resultado.mensaje;
            return;
        }
        window.location.href = "documentosAdmin.php";
    } catch (error) {
        mensaje.textContent = "No se pudo guardar el documento";
    }
});

async function iniciar() {
    if (editar && !id) {
        mensaje.textContent = "No se encontró el documento";
        return;
    }
    try {
        if (!await cargarCategorias()) { return; }
        if (editar) { await cargarDocumento(); }
    } catch (error) {
        mensaje.textContent = "No se pudo cargar el formulario";
    }
}
iniciar();
