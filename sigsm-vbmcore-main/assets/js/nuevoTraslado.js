const formulario = document.querySelector("#formTraslado");
const mensaje = document.querySelector("#mensaje");
const parametros = new URLSearchParams(window.location.search);
const id = parametros.get("id");
const editar = window.location.pathname.includes("editarTraslado");

async function cargarTraslado() {
    if (!editar) { return; }
    try {
        const respuesta = await fetch("../php/traslados.php?action=obtener&id=" + id);
        const resultado = await respuesta.json();
        if (!resultado.ok) { mensaje.textContent = resultado.mensaje; return; }
        const traslado = resultado.traslado;
        document.querySelector("#id").value = traslado.id;
        document.querySelector("#version").value = traslado.version;
        document.querySelector("#cedula").value = traslado.cedula;
        document.querySelector("#nombre").value = traslado.nombre;
        document.querySelector("#apellido").value = traslado.apellido;
        document.querySelector("#conductor").value = traslado.conductor;
        document.querySelector("#enfermero").value = traslado.enfermero;
        document.querySelector("#vehiculo").value = traslado.vehiculo;
        document.querySelector("#origen").value = traslado.origen;
        document.querySelector("#destino").value = traslado.destino;
        document.querySelector("#observaciones").value = traslado.observaciones || "";
        if (traslado.hora_salida) { document.querySelector("#hora_salida").value = traslado.hora_salida.replace(" ", "T").slice(0, 16); }
        if (traslado.hora_llegada) { document.querySelector("#hora_llegada").value = traslado.hora_llegada.replace(" ", "T").slice(0, 16); }
        document.querySelector("#hora_salida").readOnly = traslado.estado === "Pendiente";
        document.querySelector("#hora_llegada").readOnly = traslado.estado !== "Finalizado";
        if (traslado.estado === "Cancelado") {
            formulario.querySelector("button[type=submit]").disabled = true;
            mensaje.textContent = "No se puede editar un traslado cancelado";
        }
    } catch (error) { mensaje.textContent = "No se pudo cargar el traslado"; }
}

formulario.addEventListener("submit", async function (evento) {
    evento.preventDefault();
    let incompleto = false;
    formulario.querySelectorAll("[required]").forEach(function (campo) {
        if (campo.value.trim() === "") { incompleto = true; }
    });
    if (incompleto) {
        mensaje.textContent = "Complete los campos obligatorios";
        return;
    }
    try {
        const accion = editar ? "editar" : "crear";
        const respuesta = await fetch("../php/traslados.php?action=" + accion, {method: "POST", body: new FormData(formulario)});
        const resultado = await respuesta.json();
        mensaje.textContent = resultado.mensaje;
        if (resultado.ok) { window.location.href = "detalleTraslado.php?id=" + resultado.id; }
    } catch (error) { mensaje.textContent = "No se pudo guardar el traslado"; }
});
cargarTraslado();
