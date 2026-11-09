const lista = document.querySelector("#listaTraslados");
const detalle = document.querySelector("#detalleTraslado");
const mensaje = document.querySelector("#mensaje");
const id = new URLSearchParams(window.location.search).get("id");
let traslados = [];

function crearTarjeta(traslado) {
    const tarjeta = document.createElement("article");
    tarjeta.className = "traslado-card";
    let estado = "Pendiente";
    let boton = `<button class="btn" onclick="cambiarEstado(${traslado.id}, 'iniciar')">Poner en curso</button>`;
    if (traslado.estado === "En traslado") {
        estado = "En curso";
        boton = `<button class="btn" onclick="cambiarEstado(${traslado.id}, 'finalizar')">Registrar llegada</button>`;
    } else if (traslado.estado === "Finalizado") {
        estado = "Llegó";
        boton = "";
    } else if (traslado.estado === "Cancelado") {
        estado = "Cancelado";
        boton = "";
    }
    if (traslado.estado === "Pendiente" || traslado.estado === "En traslado") {
        boton += `<button class="btn btn-peligro" onclick="cambiarEstado(${traslado.id}, 'cancelar')">Cancelar traslado</button>`;
    }
    const editar = traslado.estado === "Cancelado" ? "" : `<a class="btn btn-secundario" href="editarTraslado.php?id=${traslado.id}">Editar</a>`;
    tarjeta.innerHTML = `
        <span class="estado">${estado}</span><h2>Traslado #${traslado.id}</h2>
        <p class="paciente"></p><p class="recorrido"></p><p class="personal"></p>
        <p class="horarios"></p><p class="observaciones"></p>
        <a class="btn btn-secundario" href="detalleTraslado.php?id=${traslado.id}">Ver detalle</a>
        ${editar}
        ${boton}
        <button class="btn btn-peligro" onclick="cambiarEstado(${traslado.id}, 'eliminar')">Eliminar</button>`;
    tarjeta.querySelector(".paciente").textContent = traslado.nombre + " " + traslado.apellido + " · Cédula: " + traslado.cedula;
    tarjeta.querySelector(".recorrido").textContent = traslado.origen + " → " + traslado.destino;
    tarjeta.querySelector(".personal").textContent = "Conductor: " + traslado.conductor + " · Enfermero: " + traslado.enfermero + " · Vehículo: " + traslado.vehiculo;
    tarjeta.querySelector(".horarios").textContent = "Salida: " + (traslado.hora_salida || "Sin registrar") + " · Llegada: " + (traslado.hora_llegada || "Sin registrar");
    tarjeta.querySelector(".observaciones").textContent = traslado.observaciones || "";
    if (traslado.estado === "Pendiente") { tarjeta.querySelector(".estado").classList.add("pendiente"); }
    if (traslado.estado === "En traslado") { tarjeta.querySelector(".estado").classList.add("curso"); }
    if (traslado.estado === "Finalizado") { tarjeta.querySelector(".estado").classList.add("aprobado"); }
    if (traslado.estado === "Cancelado") { tarjeta.querySelector(".estado").classList.add("cancelado"); }
    return tarjeta;
}

function mostrarTraslados() {
    lista.innerHTML = "";
    const estado = document.querySelector("#filtroEstado").value;
    const paciente = document.querySelector("#filtroPaciente").value.trim().toLowerCase();
    const vehiculo = document.querySelector("#filtroVehiculo").value.trim().toLowerCase();
    traslados.forEach(function (traslado) {
        const nombre = (traslado.nombre + " " + traslado.apellido + " " + traslado.cedula).toLowerCase();
        if (estado && traslado.estado !== estado) { return; }
        if (!nombre.includes(paciente) || !traslado.vehiculo.toLowerCase().includes(vehiculo)) { return; }
        lista.appendChild(crearTarjeta(traslado));
    });
    if (lista.children.length === 0) { lista.innerHTML = "<p>No hay traslados para mostrar.</p>"; }
}

async function cargarTraslados() {
    try {
        const accion = lista ? "listar" : "obtener&id=" + id;
        const respuesta = await fetch("../php/traslados.php?action=" + accion);
        const resultado = await respuesta.json();
        if (!resultado.ok) { mensaje.textContent = resultado.mensaje; if (detalle) { detalle.innerHTML = ""; } return; }
        if (lista) {
            traslados = resultado.traslados;
            let pendientes = 0;
            let enCurso = 0;
            let llegaron = 0;
            let cancelados = 0;
            traslados.forEach(function (traslado) {
                if (traslado.estado === "Pendiente") { pendientes++; }
                if (traslado.estado === "En traslado") { enCurso++; }
                if (traslado.estado === "Finalizado") { llegaron++; }
                if (traslado.estado === "Cancelado") { cancelados++; }
            });
            document.querySelector("#cantidadPendientes").textContent = pendientes;
            document.querySelector("#cantidadEnCurso").textContent = enCurso;
            document.querySelector("#cantidadLlegaron").textContent = llegaron;
            document.querySelector("#cantidadCancelados").textContent = cancelados;
            mostrarTraslados();
        } else {
            detalle.innerHTML = "";
            detalle.appendChild(crearTarjeta(resultado.traslado));
        }
    } catch (error) { mensaje.textContent = "No se pudieron cargar los traslados"; }
}

async function cambiarEstado(idTraslado, accion) {
    let confirmacion = "¿Confirmar el cambio de estado?";
    if (accion === "eliminar") { confirmacion = "¿Eliminar este traslado para todos los funcionarios?"; }
    if (accion === "cancelar") { confirmacion = "¿Cancelar este traslado? Se conservará en el historial."; }
    if (!confirm(confirmacion)) { return; }
    const datos = new FormData();
    datos.append("id", idTraslado);
    try {
        const respuesta = await fetch("../php/traslados.php?action=" + accion, {method: "POST", body: datos});
        const resultado = await respuesta.json();
        mensaje.textContent = resultado.mensaje;
        if (resultado.ok && detalle && accion === "eliminar") { window.location.href = "seguimiento.php"; return; }
        cargarTraslados();
    } catch (error) { mensaje.textContent = "No se pudo guardar el cambio"; }
}
if (lista) {
    document.querySelector("#filtroEstado").addEventListener("change", mostrarTraslados);
    document.querySelector("#filtroPaciente").addEventListener("input", mostrarTraslados);
    document.querySelector("#filtroVehiculo").addEventListener("input", mostrarTraslados);
}
cargarTraslados();
setInterval(cargarTraslados, 10000);
