---
title: Uploads de archivos
source:
  - docs/core/file-uploads.md
source_revision: 0cd8a411195c4373648faf320416191e3018ecc3
status: partial
---

# Uploads de archivos

`FileUploader` procesa archivos multipart recibidos por PHP en `$_FILES`. `Base64Uploader` lee el body crudo. Son helpers separados y no comparten validaciones.

## Multipart

`new FileUploader()` usa `UPLOADS_PATH` y crea el directorio si hace falta. `setLocation($path)` cambia el destino; `setAllowedTypes($extensions, $mimeTypes)` reemplaza las listas permitidas. La configuración `files.media_accepted.extensions` y `files.media_accepted.mime_types` se lee si existen los archivos de configuración esperados; una lista ausente o vacía conserva los defaults.

`doUpload($input_name)` procesa un campo, un nombre con wildcard final como `document_*`, o todos los campos si se omite. Para aceptar un archivo, comprueba la extensión, que el temporal sea un upload HTTP de PHP y que el MIME detectado por `fileinfo` esté permitido. Después usa `move_uploaded_file()` y genera un nombre nuevo conservando la extensión. `setFileHandler()` cambia el callback de renombrado.

`getFileNames()` devuelve metadata de los archivos movidos: campo, índice, nombre original, nombre guardado, path completo y tamaño. `getErrors()` devuelve campo, índice y nombre original de los fallidos. Si `$_FILES` está vacío, el helper retorna sin datos.

La clase no establece cuota de bytes, autorización, análisis antimalware, carpeta fuera del webroot ni reglas del servidor para impedir ejecución. Extensión y MIME no reemplazan una política completa de carga. El tamaño efectivo también depende de PHP.

## Base64

`Base64Uploader` expone `setLocation()`, `setFileHandler()`, `doUpload()`, `getFileNames()` y `getErrors()`. Lee `php://input` y escribe el resultado. La implementación no valida estrictamente base64, no inspecciona los bytes como imagen, no aplica allowlists ni límite de tamaño y no inicializa un renombrador por defecto. `doUpload()` puede fallar si no se configuró `setFileHandler()`; no se presenta como equivalente de multipart.

`Request::getFormData()` devuelve `$_POST`; el manejo multipart usa directamente `$_FILES`. No se ejecutó una petición de upload. Los fixtures de tipos y moves se inspeccionaron, pero las pruebas no se ejecutaron.
