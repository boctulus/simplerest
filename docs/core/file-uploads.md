# Uploads de archivos

SimpleRest expone dos helpers distintos: [`FileUploader`](../../src/framework/Libs/FileUploader.php) procesa los archivos multipart de PHP en `$_FILES`; [`Base64Uploader`](../../src/framework/Libs/Base64Uploader.php) lee el cuerpo crudo de la petición. No comparten validación ni garantías.

## Upload multipart con `FileUploader`

`new FileUploader()` usa `UPLOADS_PATH` y crea el directorio si no existe. `setLocation($path)` cambia ese destino; `setAllowedTypes($extensions, $mimeTypes)` reemplaza las listas permitidas. Si la aplicación tiene `config/config.php` y `config/databases.php`, el constructor puede leer `files.media_accepted.extensions` y `files.media_accepted.mime_types`. Una lista ausente o vacía conserva los defaults del código.

El allowlist integrado incluye extensiones y MIME para imágenes comunes, PDF, documentos Office, CSV, texto y Markdown. `doUpload($input_name)` acepta un campo, un conjunto de campos mediante wildcard final como `document_*`, o todos los campos si se omite el nombre. Procesa campos simples y arrays de archivos de PHP.

Cada archivo debe pasar las tres comprobaciones del helper: extensión del nombre original en la lista, `is_uploaded_file()` para el temporal y MIME detectado por `fileinfo` en la lista permitida. Después se mueve con `move_uploaded_file()` a un nombre nuevo; por defecto usa `uniqid()` y conserva la extensión original. `setFileHandler($fn, ...$params)` permite sustituir el renombrado; el código actual pasa al callback solo el primer parámetro almacenado.

```php
use Boctulus\Simplerest\Core\Libs\FileUploader;

$uploader = (new FileUploader())
    ->setLocation(UPLOADS_PATH)
    ->setAllowedTypes(['pdf'], ['application/pdf']);

$uploader->doUpload('document');
$stored = $uploader->getFileNames();
$failed = $uploader->getErrors();
```

Este fragmento representa la forma de llamada; no se ejecutó una petición multipart. `getFileNames()` contiene, por archivo aceptado, `field`, `index`, `ori_name`, `as_stored`, `full_path` y `size`. `getErrors()` contiene `field`, `index` y `ori_name`. Si `$_FILES` está vacío, `doUpload()` retorna sin errores ni archivos.

La clase no configura una cuota de bytes, análisis antimalware, autorización, ubicación fuera del webroot ni reglas del servidor para impedir que se ejecuten archivos. El límite PHP y el manejo de errores de upload pertenecen al entorno de ejecución. La comprobación de permisos de escritura tampoco verifica ACL de Windows en este helper. Extensión más MIME detectado reducen algunas entradas, pero no constituyen por sí solos una política completa de carga segura.

## Cuerpo base64 con `Base64Uploader`

`Base64Uploader` lee `php://input`, intenta reconocer una cabecera `data:image/...;base64,` y escribe el resultado bajo su ubicación configurada. Su superficie incluye `setLocation()`, `getLocation()`, `setFileHandler()`, `doUpload()`, `getFileNames()` y `getErrors()`.

El código no inicia un callback de renombrado por defecto, no valida estrictamente base64, no comprueba los bytes como imagen y no aplica allowlists de extensión/MIME ni límites de tamaño. `doUpload()` usa el callback almacenado sin comprobar que exista. Trátalo como una implementación incompleta; no lo uses como equivalente a `FileUploader` para datos no confiables. La diferencia está clasificada en el [ledger de auditoría](../audit/README.md#files-and-output-utilities-audit-2026-10-24).

## Integración HTTP

`Request::getFormData()` devuelve `$_POST`; `parseFormData()` no administra `$_FILES`. El upload multipart lo procesa PHP y el helper lee esa estructura directamente. El endpoint API que además guarda metadatos y aplica ACL es otra capa; se documenta en la referencia de [extensiones API y seguridad](../api/README.md) al completar ese dominio.

Se inspeccionó [`FileUploaderTypeValidationTest`](../../unit-tests/files/FileUploaderTypeValidationTest.php), que cubre allowlists, MIME detectado, rechazo de un temporal local y un movimiento fallido mediante fixtures. Las pruebas no se ejecutaron y no demuestran una carga HTTP real.
