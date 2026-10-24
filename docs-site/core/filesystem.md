---
title: Filesystem y rutas
source:
  - docs/core/filesystem.md
source_revision: 0cd8a411195c4373648faf320416191e3018ecc3
status: partial
---

# Filesystem y rutas

`Files` agrupa helpers de rutas, lectura y escritura, búsqueda, copia, movimiento, borrado y descarga. Es una envoltura de funciones de PHP: no establece un directorio raíz permitido ni autoriza el acceso a un path.

## Rutas

`normalize()`, `convertSlashes()`, `addTrailingSlash()`, `removeTrailingSlash()` y `removeFirstSlash()` adaptan separadores. `isAbsolutePath()` y `startsWithRoot()` clasifican rutas, pero no resuelven enlaces ni limitan un path a una carpeta.

`getAbsolutePath($path, $relative_to)` concatena el path con la base cuando se entrega `$relative_to`; esa rama no canonicaliza ni comprueba contención. Sin base, devuelve rutas absolutas tal cual o requiere que una ruta relativa exista para resolverla. `getRelativePath()` tiene una condición defectuosa al revisar el primer carácter y no se debe usar para seguridad.

Valida en la aplicación cualquier path que provenga de una request antes de leerlo, escribirlo, borrarlo o servirlo.

## Lectura, escritura y transferencias

`read()` devuelve `false` cuando no puede leer; `readOrFail()` distingue directorios, archivos ausentes y archivos ilegibles con excepciones. `reader()` y `writter()` usan bloqueos de archivo. `write()`, `writeOrFail()`, `append()` y `appendOrFail()` escriben contenido; las operaciones CSV incluyen `arrayToCSV()`, `getCSV()` y `processCSV()`.

`getContent($path, $base_url)` acepta rutas locales y URLs. La ruta URL usa ApiClient con verificación TLS estricta desactivada cuando hay cURL; si no, usa `file_get_contents()`. `download($url, $dest_dir)` guarda los archivos usando el último segmento de cada URL y devuelve el resultado del último. También delega en `ApiClient::download()`, cuya implementación conserva una anomalía de transferencia. No se hizo una petición de red.

`forceDownload($filePath)` emite un archivo como attachment y termina el proceso. El caller debe autorizar la lectura y seleccionar una ruta controlada. Ninguno de estos helpers aporta aislamiento de paths, validación de URL ni una descarga reproducida.

## Operaciones y assets

La clase también incluye `glob()` y recorridos recursivos; `cp()`, `copy()` y `move()`; `delete()`, `globDelete()`, `delTree()` y variantes; creación de directorios; pruebas de escritura; y helpers temporales como `tempDir()`, `getTempFilename()` y `saveToTempFile()`.

`cp()` puede mover un destino existente al directorio de backup antes de evaluar `$simulate`; ese flag no garantiza ausencia de escrituras. En Windows, `writableOrFail()` retorna sin comprobar permisos.

El helper `asset()` construye URLs bajo `public/assets/`. `View::js_file()` y `View::css_file()` agregan referencias de markup; consulta [Vistas PHP](/services/views). La documentación no certifica el acceso a paths de vistas como un mecanismo seguro para servir archivos.

No se escribieron ni borraron archivos, y no se probaron permisos, descargas ni rutas de assets.
