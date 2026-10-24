# Filesystem y rutas

[`Files`](../../src/framework/Libs/Files.php) reúne helpers para rutas, lectura y escritura, búsqueda, copia, movimiento, borrado y descargas. Es una capa de conveniencia alrededor de funciones de PHP; no define un directorio raíz permitido ni autoriza por sí misma el acceso a una ruta.

## Rutas

`normalize()`, `convertSlashes()`, `addTrailingSlash()`, `removeTrailingSlash()` y `removeFirstSlash()` ayudan a adaptar separadores. `isAbsolutePath()` y `startsWithRoot()` clasifican formas de ruta; no resuelven enlaces simbólicos ni verifican que un destino quede dentro de un directorio permitido.

`getAbsolutePath($path, $relative_to)` devuelve `$path` sin cambios si ya parece absoluto. Si se proporciona `$relative_to`, concatena las cadenas sin canonicalizar la ruta. Sin base, requiere que el path exista y usa `realpath()`. `getRelativePath()` tiene una condición de primer carácter que asigna `'/'`; su comportamiento de quitar el separador inicial no debe usarse como comprobación de seguridad. Las diferencias están registradas en el [ledger de auditoría](../audit/README.md#files-and-output-utilities-audit-2026-10-24).

No uses estas funciones para decidir si un path aportado por un usuario puede leerse, escribirse, borrarse o servirse. Resuelve y valida el destino dentro de la aplicación antes de llamar a los helpers de filesystem.

## Leer y escribir

`read()` devuelve `false` ante rutas inexistentes, ilegibles o que sean directorios; `readOrFail()` distingue esos casos mediante excepciones de archivo. `reader()`/`writter()` implementan lectura y escritura con `flock()`. `write()` devuelve un booleano; `writeOrFail()` comprueba el directorio padre y devuelve los bytes escritos. `append()` y `appendOrFail()` agregan contenido. Para CSV están `arrayToCSV()`, `getCSV()` y `processCSV()`; para texto, `arrayToFile()` y `toArray()`.

`getContent($path, $base_url)` acepta una ruta local o una URL. Las URLs pasan por ApiClient cuando cURL está disponible; esa ruta desactiva la verificación TLS estricta y comparte la anomalía de transferencia de [`ApiClient::download()`](api-client.md). Si no hay cURL, se usa `file_get_contents()`. No se hizo ninguna petición de red.

`download($url, $dest_dir)` guarda cada URL usando el nombre del último segmento y retorna el resultado del último archivo. También delega en `ApiClient::download()` con TLS estricto desactivado. `forceDownload($filePath)` envía un archivo como attachment y termina el proceso; el caller debe controlar el path y la autorización. Ninguna de estas funciones demuestra una política de URL segura, una descarga verificada ni un confinamiento de rutas.

## Búsqueda y operaciones de archivos

| Grupo | Métodos principales | Alcance |
| --- | --- | --- |
| Existencia y directorios | `exists()`, `existsOrFail()`, `fileExists()`, `directoryExists()`, `dirExists()`, `getDir()` | Consultan existencia o derivan el directorio de una ruta. |
| Búsqueda | `glob()`, `recursiveGlob()`, `recursiveGlobWithRootPath()`, `scanDirectory()`, `deepScan()`, `getFiles()` | Enumeran archivos según patrones o extensiones. |
| Copia y movimiento | `cp()`, `copy()`, `move()` | Copian archivos/directorios, aplican exclusiones y aceptan callbacks donde la firma lo permite. |
| Borrado | `delete()`, `deleteOrFail()`, `globDelete()`, `rmDirOrFail()`, `delTree()`, `delTreeOrFail()`, `deleteDirectory()` | Eliminan archivos o árboles; confirma siempre el path y el alcance antes de invocarlos. |
| Directorios y permisos | `mkDir()`, `mkDirOrFail()`, `mkDestination()`, `isWritable()`, `writableOrFail()` | Crean destinos o hacen comprobaciones de escritura. En Windows, `writableOrFail()` retorna sin validar permisos. |
| Temporal | `tempDir()`, `getTempFilename()`, `saveToTempFile()`, `getFromTempFile()`, `touch()` | Usan el `tmp_dir` configurado o crean/leen archivos temporales. |
| Exportación y diagnóstico | `varExport()`, `JSONExport()`, `dump()`, `dumpArrayToJSONL()`, `arrayToCSV()` | Serializan o escriben datos en rutas indicadas por el caller. |

`cp()` puede mover un destino existente al directorio de backup configurado antes de llegar a su rama `$simulate`; por ello, ese flag no garantiza que no haya escrituras. Configura y protege los directorios antes de usar estas operaciones. No se ejecutó ningún método destructivo ni se escribió un archivo durante esta auditoría.

## Assets y archivos de vistas

El helper global `asset()` construye URLs bajo `public/assets/`; `View::js_file()` y `View::css_file()` agregan referencias de markup a los assets. Consulta [Vistas PHP](../services/views.md) para su integración con vistas. Eso no convierte `Files` en un servidor de archivos ni valida si el asset está permitido.

El acceso HTTP legacy a rutas `/app/views/` y `/app/shortcodes/` está implementado por otro camino de [`RequestHandler`](../../src/framework/Handlers/RequestHandler.php); no debe tratarse como una ruta estática confinada. La falta de comprobación de contención canónica está registrada en el [ledger de auditoría](../audit/README.md#files-and-output-utilities-audit-2026-10-24) y queda pendiente de decisión de seguridad.

## Estado de evidencia

La guía se basa en lectura de fuente de `Files`, `RequestHandler`, `View` y helpers de vistas. No se probaron permisos multiplataforma, descargas, escritura, borrado ni publicación de assets.
