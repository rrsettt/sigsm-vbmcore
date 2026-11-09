<?php
require_once "../php/sesion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/estiloadmin.css?v=10">
    <title>Editar Documento</title>
</head>

<body>

    <header class="header">

        <a
            href="../index.html"
            class="logo"
        >
            <img
                src="../assets/img/Logo_Hc.png"
                alt="Hospital de Clínicas"
            >
        </a>

        <a
            class="btn btn-secundario"
            href="documentosAdmin.php"
        >
            Volver
        </a>

    </header>

    <main class="dashboard">

        <h1>Editar Documento</h1>

        <form
            id="formDocumento"
            class="formulario-documento"
            enctype="multipart/form-data"
        >
            <input type="hidden" id="version" name="version">

            <input
                type="hidden"
                id="id"
                name="id"
            >

            <label for="titulo">
                Título
            </label>

            <input
                type="text"
                id="titulo"
                maxlength="150"
                name="titulo"
                required
            >

            <label for="descripcion">
                Descripción
            </label>

            <textarea
                id="descripcion"
                name="descripcion"
            ></textarea>

            <label for="categoria">
                Categoría
            </label>

            <select
                id="categoria"
                name="categoria_id"
                required
            >

                <option value="">
                    Seleccione una categoría
                </option>

            </select>

            <label for="archivo">
                Cambiar PDF
            </label>

            <input
                type="file"
                id="archivo"
                name="archivo"
                accept=".pdf"
            >

            <label for="imagen">
                Cambiar imagen de portada
            </label>

            <input
                type="file"
                id="imagen"
                name="imagen"
                accept=".jpg,.jpeg,.png,.webp"
            >

            <label class="check-publicar">

                <input
                    type="checkbox"
                    id="activo"
                    name="activo"
                    value="1"
                >

                Mostrar cuando sea aprobado

            </label>

            <p id="mensaje"></p>

            <div class="acciones-formulario">

                <a
                    class="btn"
                    href="documentosAdmin.php"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="btn"
                >
                    Guardar Cambios
                </button>

            </div>

        </form>

    </main>
    <script src="../assets/js/formularioDocumento.js?v=12"></script>

<footer class="pie-panel">S.I.G.M.S · Hospital de Clínicas <span>Desarrollado por <strong>VBMCore</strong></span></footer>
</body>

</html>
