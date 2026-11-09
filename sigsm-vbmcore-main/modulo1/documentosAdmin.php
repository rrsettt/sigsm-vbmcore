<?php
require_once "../php/sesion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/estiloadmin.css?v=10">
    <title>Panel de Documentación</title>

</head>

<body>

<header class="header">

    <a
        href="../index.html"
        class="logo"
    >
        <img
            src="../assets/img/Logo_Hc.png"
            alt="Hospital"
        >
    </a>

    <a
        class="btn btn-secundario"
        href="admins.php"
    >
        ← Volver al panel
    </a>

</header>

<section class="dashboard">

    <h1>
        Panel de Administración de Documentación
    </h1>

    <a
        class="btn"
        href="nuevoDocumento.php"
    >
        Nuevo Documento
    </a>

    <label for="filtroRevision">Revisión</label>
    <select id="filtroRevision"><option value="">Todas</option><option>Pendiente</option><option>Aprobado</option><option>Rechazado</option></select>
    <div class="tabla-contenedor"><table>

        <thead>
            <tr>
                <th>Título</th>
                <th>Categoría</th>
                <th>Fecha</th>
                <th>Revisión</th>
                <th>Motivo del rechazo</th>
                <th>Acción</th>
            </tr>
        </thead>

        <tbody id="tablaDocumentos">

            <tr>
                <td colspan="6">
                    Cargando documentos...
                </td>
            </tr>

        </tbody>

    </table></div>

</section>
<script src="../assets/js/documentosAdmin.js"></script>

<footer class="pie-panel">S.I.G.M.S · Hospital de Clínicas <span>Desarrollado por <strong>VBMCore</strong></span></footer>
</body>
</html>
