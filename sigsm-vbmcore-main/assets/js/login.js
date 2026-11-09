const formulario = document.querySelector("#formularioLogin");
const mensaje = document.querySelector("#mensaje");
formulario.addEventListener("submit", async function (evento) {
    evento.preventDefault();
    mensaje.textContent = "";
    if (document.querySelector("#usuario").value.trim() === "" || document.querySelector("#password").value === "") {
        mensaje.textContent = "Complete todos los campos";
        return;
    }
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
        if (resultado.rol === "FUNCIONARIO") {
            window.location.href = "admins.php";
        } else if (resultado.rol === "MEDICO") {
            window.location.href = "revisionMedica.php";
        } else {
            mensaje.textContent = "Esta cuenta no tiene acceso al personal";
        }

    } catch (error) {
        console.error(error);
        mensaje.textContent =
            "Error al conectar con el servidor.";
    }
});
