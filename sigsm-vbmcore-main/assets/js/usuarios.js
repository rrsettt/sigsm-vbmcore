const formularioCuenta = document.querySelector("#formCuenta");
const mensajeCuenta = document.querySelector("#mensajeCuenta");

formularioCuenta.addEventListener("submit", async function (evento) {
    evento.preventDefault();
    if (document.querySelector("#cuentaNombre").value.trim() === "" || document.querySelector("#cuentaApellido").value.trim() === "" || document.querySelector("#cuentaUsuario").value.trim() === "" || document.querySelector("#cuentaPassword").value.length < 8) {
        mensajeCuenta.textContent = "Ingrese un usuario y una contraseña de al menos 8 caracteres";
        return;
    }
    try {
        const respuesta = await fetch("../php/usuarios.php", {method: "POST", body: new FormData(formularioCuenta)});
        const resultado = await respuesta.json();
        mensajeCuenta.textContent = resultado.mensaje;
        if (resultado.ok) { formularioCuenta.reset(); }
    } catch (error) { mensajeCuenta.textContent = "No se pudo crear la cuenta"; }
});
