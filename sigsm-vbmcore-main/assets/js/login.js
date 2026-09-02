const formulario = document.querySelector("#formularioLogin");
const mensaje = document.querySelector("#mensaje");

formulario.addEventListener("submit", async function (evento) {

    evento.preventDefault();

    mensaje.textContent = "";

    const datos = new FormData(formulario);

    try {

        const respuesta = await fetch("../php/login.php", {
            method: "POST",
            body: datos
        });

        const resultado = await respuesta.json();

        if (!resultado.ok) {
            mensaje.textContent = resultado.mensaje;
            return;
        }

        localStorage.setItem(
            "usuario",
            document.querySelector("#usuario").value
        );

        localStorage.setItem(
            "clave",
            document.querySelector("#password").value
        );

        const parametros = new URLSearchParams(window.location.search);

        const destino = parametros.get("redirect");

        if (destino) {

            window.location.href = destino;

        } else if (resultado.rol === "FUNCIONARIO") {

            window.location.href = "admins.html";
        }

    } catch (error) {

        console.error(error);

        mensaje.textContent = "Error al conectar con el servidor.";
    }
});