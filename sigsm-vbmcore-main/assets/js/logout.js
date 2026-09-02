const botonCerrarSesion = document.querySelector("#cerrarSesion");

if (botonCerrarSesion) {

    botonCerrarSesion.addEventListener("click", async function () {

        try {

            await fetch("../php/logout.php", {
                method: "POST"
            });

            localStorage.removeItem("usuario");
            localStorage.removeItem("clave");

            window.location.href = document.body.dataset.login;

        } catch (error) {

            console.error("Error al cerrar sesión:", error);
        }
    });
}