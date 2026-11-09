<?php
require_once "../php/sesion.php";
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Panel de funcionarios · Hospital de Clínicas</title>
        <link rel="stylesheet" href="../assets/css/estiloadmin.css?v=10">
    </head>
    <body data-login="login.html">
        <header class="header">
            <a class="logo" href="../index.html">
                <img src="../assets/img/Logo_Hc.png" alt="Hospital de Clínicas">
            </a>
            <nav class="acciones-header" aria-label="Accesos del panel">
                <a class="btn btn-secundario" href="../index.html">← Volver</a>
                <button class="btn btn-peligro" id="cerrarSesion" type="button">Cerrar sesión</button>
            </nav>
        </header>
        <main class="dashboard">
            <section class="titulo-pagina">
                <h1>Panel de funcionarios</h1>
                <p>Elegí el módulo con el que querés trabajar.</p>
            </section>
            <section class="opciones-admin">
                <article class="opcion-admin">
                    <h2>Documentación</h2>
                    <p>Subí documentos y consultá su revisión médica.</p>
                    <a class="btn" href="documentosAdmin.php">Gestionar documentos</a>
                </article>
                <article class="opcion-admin">
                    <h2>Encuestas</h2>
                    <p>Administrá las preguntas y consultá las respuestas de los pacientes.</p>
                    <a class="btn" href="encuestasAdmin.php">Gestionar encuestas</a>
                </article>
                <article class="opcion-admin">
                    <h2>Traslados</h2>
                    <p>Solicitá traslados, gestioná pendientes y registrá las llegadas.</p>
                    <a class="btn" href="../modulo2/seguimiento.php">Gestionar traslados</a>
                </article>
            </section>
            <section class="panel herramientas-personal">
                <h2>Accesos y cuentas</h2>
                <details>
                    <summary>Crear una cuenta de acceso</summary>
                    <form id="formCuenta">
                        <label for="cuentaNombre">Nombre</label><input id="cuentaNombre" name="nombre" maxlength="50" required>
                        <label for="cuentaApellido">Apellido</label><input id="cuentaApellido" name="apellido" maxlength="50" required>
                        <label for="cuentaUsuario">Usuario</label><input id="cuentaUsuario" name="usuario" maxlength="50" required>
                        <label for="cuentaPassword">Contraseña</label><input type="password" id="cuentaPassword" name="password" minlength="8" maxlength="72" required>
                        <label for="cuentaRol">Rol</label>
                        <select id="cuentaRol" name="rol"><option value="FUNCIONARIO">Funcionario</option><option value="MEDICO">Médico</option></select>
                        <p id="mensajeCuenta" role="status"></p>
                        <button class="btn" type="submit">Crear cuenta</button>
                    </form>
                </details>
            </section>
        </main>
        <footer class="pie-panel">S.I.G.M.S · Hospital de Clínicas <span>Desarrollado por <strong>VBMCore</strong>
            </span>
        </footer>
        <script src="../assets/js/logout.js"></script>
        <script src="../assets/js/usuarios.js"></script>
    </body>
</html>
