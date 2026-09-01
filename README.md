# VBM Core - Hospital de Clínicas

Proyecto académico desarrollado para el Hospital de Clínicas.

El sistema permite gestionar documentación médica que puede ser consultada por los pacientes desde un portal público. También incluye un sistema de encuestas para conocer la opinión de los pacientes.

## Tecnologías utilizadas

- HTML
- CSS
- JavaScript
- PHP
- MySQL
- Apache
- phpMyAdmin

## Funcionalidades

### Portal público

El portal puede ser utilizado por los pacientes sin iniciar sesión.

Permite:

- Consultar documentos publicados.
- Visualizar documentos PDF.
- Acceder a encuestas disponibles.
- Responder encuestas utilizando la cédula.

El acceso al portal está pensado para realizarse mediante un código QR disponible en el Hospital de Clínicas.

### Panel de funcionarios

Los funcionarios deben iniciar sesión para acceder a las funciones administrativas.

Permite:

- Crear documentos.
- Editar documentos.
- Eliminar documentos.
- Publicar documentos en el portal.
- Crear encuestas.
- Administrar preguntas de las encuestas.
- Eliminar encuestas.
- Consultar las respuestas enviadas por los pacientes.

### Autenticación

El sistema utiliza sesiones de PHP para controlar el acceso a las páginas privadas.

Se utilizan los roles:

- FUNCIONARIO
- PACIENTE

Las contraseñas almacenadas en la base de datos utilizan hash.

Para cumplir con los requisitos de la actividad, también se utiliza LocalStorage en el proceso de inicio y cierre de sesión.

## Base de datos

La base de datos utilizada es MySQL.

Entre las principales tablas se encuentran:

- roles
- usuarios
- pacientes
- categorias
- documentos
- encuestas
- preguntas
- respuestas

La base de datos utiliza claves primarias, claves foráneas, campos UNIQUE y otras restricciones para mantener la integridad de los datos.

## Arquitectura

El proyecto separa sus responsabilidades en tres partes principales:

### Presentación

Contiene las páginas y la interacción con el usuario.

- HTML
- CSS
- JavaScript

### Lógica de negocio

Contiene el procesamiento, las validaciones y las acciones del sistema.

- PHP
- Clases PHP

### Acceso a datos

La conexión con MySQL se realiza utilizando PDO.

El archivo principal de conexión se encuentra en:

`datos/conexion.php`

## Instalación

1. Instalar XAMPP.
2. Iniciar Apache y MySQL.
3. Copiar el proyecto dentro de la carpeta `htdocs`.
4. Crear la base de datos desde phpMyAdmin.
5. Importar el archivo `sql/VBMCoreDataBase.sql`.
6. Verificar los datos de conexión en `datos/conexion.php`.
7. Acceder al proyecto mediante `http://localhost/`.

## Estado del proyecto

Actualmente se encuentra implementado el Módulo de Documentación, incluyendo la gestión de documentos, autenticación de funcionarios, portal público y encuestas.

El módulo de Seguimiento/Ambulancias se considera opcional para esta entrega.
