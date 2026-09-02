async function verificarSesion() {
    try {
        const respuesta = await fetch("../php/sesion.php");
        const datos = await respuesta.json();

        if (!datos.ok || datos.rol !== "FUNCIONARIO") {
            window.location.href = document.body.dataset.login;
            return;
        }

        document.body.style.display = "block";

    } catch (error) {
        console.error("Error al verificar sesión:", error);
        window.location.href = document.body.dataset.login;
    }
}

verificarSesion();