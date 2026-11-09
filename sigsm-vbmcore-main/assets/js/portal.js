let categoriaSeleccionada = "";

document.addEventListener("DOMContentLoaded", function () {
    cargarCategorias();
    cargarDocumentos();
    cargarEncuesta();
});

async function cargarCategorias() {
    const lista = document.querySelector("#listaCategorias");
    try {
        const respuesta = await fetch("php/documentos.php?action=categorias");
        const resultado = await respuesta.json();
        if (!resultado.ok) { lista.textContent = resultado.mensaje; return; }
        lista.innerHTML = "";
        agregarCategoria("Todos los documentos", "");
        resultado.categorias.forEach(function (categoria) {
            agregarCategoria(categoria.nombre, categoria.nombre);
        });
    } catch (error) { lista.textContent = "No se pudieron cargar las categorías"; }
}

function agregarCategoria(nombre, valor) {
    const boton = document.createElement("button");
    boton.type = "button";
    boton.className = "filtro-categoria";
    boton.textContent = nombre;
    boton.setAttribute("aria-pressed", valor === categoriaSeleccionada ? "true" : "false");
    boton.addEventListener("click", function () {
        categoriaSeleccionada = valor;
        document.querySelectorAll(".filtro-categoria").forEach(function (opcion) {
            opcion.setAttribute("aria-pressed", "false");
        });
        boton.setAttribute("aria-pressed", "true");
        cargarDocumentos();
    });
    document.querySelector("#listaCategorias").appendChild(boton);
}

async function cargarDocumentos() {
    const contenedor = document.querySelector("#documentos");
    try {
        const respuesta = await fetch("php/documentos.php?action=publicos");
        const resultado = await respuesta.json();
        if (!resultado.ok) { contenedor.textContent = resultado.mensaje; return; }
        contenedor.innerHTML = "";
        resultado.documentos.forEach(function (documento) {
            if (categoriaSeleccionada !== "" && documento.categoria !== categoriaSeleccionada) { return; }
            const tarjeta = document.createElement("article");
            tarjeta.className = "documento-card";
            tarjeta.innerHTML = `
                <img class="documento-imagen" alt="Portada del documento">
                <div class="documento-contenido"><span class="documento-categoria"></span><h3></h3><p></p><a class="boton">Ver documento</a></div>`;
            tarjeta.querySelector("img").src = documento.imagen ? "uploads/imagenes/" + documento.imagen : "assets/img/documentacion.jpg";
            tarjeta.querySelector("span").textContent = documento.categoria;
            tarjeta.querySelector("h3").textContent = documento.titulo;
            tarjeta.querySelector("p").textContent = documento.descripcion || "";
            tarjeta.querySelector("a").href = "php/archivo.php?id=" + documento.id;
            contenedor.appendChild(tarjeta);
        });
        if (contenedor.children.length === 0) { contenedor.textContent = "No hay documentos disponibles."; }
    } catch (error) {
        contenedor.textContent = "No se pudieron cargar los documentos.";
    }
}

async function cargarEncuesta() {
    const texto = document.querySelector("#encuestaPortal");
    const boton = document.querySelector("#botonEncuesta");
    try {
        const respuesta = await fetch("php/encuestas.php?action=publicas");
        const resultado = await respuesta.json();
        if (!resultado.ok || resultado.encuestas.length === 0) { texto.textContent = "No hay encuestas disponibles"; return; }
        const cantidad = resultado.encuestas.length;
        texto.textContent = cantidad === 1 ? "Hay una encuesta disponible." : "Hay " + cantidad + " encuestas disponibles.";
        boton.textContent = "Ver encuestas";
        boton.hidden = false;
    } catch (error) { texto.textContent = "No se pudo consultar la encuesta"; }
}
