![Logo del Proyecto](https://i.imgur.com/MywHnhc.png)

# S.I.G.M.S VBMCore

➤ Proyecto de Egreso realizado para el Hospital de Clínicas con el fin de facilitar el acceso a documentación y mejorar la gestión de traslados.

El sistema cuenta con dos módulos funcionales: Documentación y Seguimiento de Traslados.

## Módulo de Documentación (📃)

- Portal público de documentación organizado por categorías.
- Consulta de documentos PDF desde el navegador.
- Encuestas anónimas para pacientes.
- Administración de documentos y encuestas por funcionarios.
- Consulta de respuestas de las encuestas.
- Revisión de documentos por médicos.
- Aprobación o rechazo de documentos con motivo del rechazo.
- Publicación de documentos aprobados y habilitados.
- Creación de cuentas de funcionarios y médicos.

Cuando un documento se modifica, vuelve a quedar pendiente de revisión. Si dos médicos intentan revisarlo al mismo tiempo, se conserva la primera decisión guardada.

El portal y las encuestas son de acceso público y no requieren una cuenta.

## Módulo de Seguimiento de Traslados (🚑)

- Registro manual de los datos del paciente.
- Registro de conductor, enfermero, vehículo, origen y destino.
- Listado y edición de traslados.
- Estados: Pendiente, En curso, Llegó y Cancelado.
- Cancelación de traslados pendientes o en curso, conservando el registro en el historial.
- Registro de horarios de salida y llegada.
- Filtros por estado, paciente, cédula o vehículo.
- Actualización automática del listado cada diez segundos.
- Eliminación de traslados con confirmación.

Los funcionarios actualizan los estados manualmente. El mapa es una imagen de referencia; el sistema no dispone de ubicación GPS.

## Tecnologías utilizadas

- HTML5
- CSS3
- JavaScript
- PHP 8 con PDO MySQL
- MariaDB
- Apache
- XAMPP para las pruebas locales

## Instalación local

1. Colocar la carpeta `sigsm-vbmcore-main` dentro de `htdocs`.
2. Iniciar Apache y MySQL desde XAMPP.
3. Si existe una instalación anterior, respaldar la base de datos y la carpeta `uploads`.
4. Importar primero `sql/schema.sql` y después `sql/data.sql` desde phpMyAdmin utilizando `root` u otra cuenta con permisos para crear usuarios y asignar privilegios.
5. Verificar los datos de conexión en `datos/conexion.php`.
6. Comprobar que PHP pueda escribir en `uploads/documentos` y `uploads/imagenes`. Configurar `upload_max_filesize` de al menos `10M` y `post_max_size` de al menos `24M`.
7. Apache debe permitir las reglas de los archivos `.htaccess` incluidos.
8. Acceder mediante `http://localhost/sigsm-vbmcore-main/index.html`.

`schema.sql` crea o actualiza la estructura de la base y configura las cuentas de MariaDB. `data.sql` carga los datos iniciales. Importarlos sobre una instalación existente conserva los registros; no reinicia la base desde cero.

También restablece las contraseñas de las cuentas web de demostración y puede volver a agregar los ejemplos eliminados.

## Credenciales de prueba

### Acceso a la aplicación

| Usuario | Contraseña | Perfil |
| --- | --- | --- |
| `admin` | `Clinicas2026` | Funcionario |
| `Medico` | `Clinicas2026` | Médico |

Ambos perfiles ingresan desde **Acceso al personal**. El sistema los dirige al panel correspondiente.

### Acceso a MariaDB

| Usuario | Contraseña | Función |
| --- | --- | --- |
| `vbm_administrador` | `AdminBD2026!` | Administración de la base del proyecto |
| `vbm_aplicacion` | `AppBD2026!` | Conexión de PHP con permisos limitados |

Estas cuentas no se utilizan en el login de la página. PHP utiliza `vbm_aplicacion` desde `datos/conexion.php`.

La cuenta de aplicación tiene permisos de consulta y modificación según las tablas que necesita. No puede crear ni eliminar tablas.

La cuenta administradora tiene permisos sobre la base del proyecto, sin privilegios globales.

Las credenciales incluidas son para demostración y deben configurarse para la instalación definitiva.

## Organización del proyecto

- `index.html`: portal público.
- `modulo1`: documentación, encuestas y paneles del personal.
- `modulo2`: gestión y seguimiento de traslados.
- `assets`: CSS, JavaScript e imágenes.
- `php`: procesamiento, validaciones, sesiones y permisos.
- `datos`: conexión y clases de acceso a la base de datos.
- `sql`: `schema.sql` para la estructura y los permisos; `data.sql` para los datos iniciales.
- `uploads`: documentos e imágenes cargadas.
- `docs`: manual de usuario y documentación del proyecto.

La organización separa presentación, lógica del sistema y acceso a datos.

## Instalación en contenedores

El entorno previsto utiliza Apache con PHP y MariaDB.

En `datos/conexion.php`, el servidor debe ser el nombre del servicio de MariaDB definido por el equipo. Dentro del contenedor web, `localhost` no representa al contenedor de base de datos.

Se deben conservar mediante volúmenes tanto la base de datos como la carpeta `uploads`. Los respaldos deben incluir ambos.

## Estado del proyecto

Ambos módulos se encuentran funcionales y fueron probados localmente.

Queda pendiente verificar la instalación en los contenedores definitivos y preparar el acceso por QR con la dirección final del portal. El QR debe apuntar a `index.html`.

El manual de usuario se encuentra en `docs/Manual-de-usuario-VBMCore.pdf`.
