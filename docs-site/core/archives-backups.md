---
title: ZIP y backups
source:
  - docs/core/archives-backups.md
source_revision: 0cd8a411195c4373648faf320416191e3018ecc3
status: partial
---

# ZIP y backups

Estos helpers escriben archivos o llaman herramientas externas. La referencia describe sus ramas de fuente; no se ejecutaron durante esta auditoría.

## `ZipManager`

`zip($ori, $dst, $exclude, $overwrite)` requiere la extensión PHP `zip`. Añade un archivo o recorre una carpeta y puede omitir paths. Para un archivo individual usa `file_get_contents()` antes de añadir el contenido, por lo que no ofrece streaming para archivos grandes.

`unzip($file_path, $destination, $verify)` extrae mediante `ZipArchive::extractTo()`. Si se omite el destino, calcula una carpeta temporal. El valor retornado es el primer componente de la ruta del primer registro ZIP, no un path validado de salida. El código no comprueba de antemano nombres de entrada ni aplica una política de contención por miembro; no presentes esta operación como una extracción segura de archivos no confiables. La alternativa que detecta `unzip` no contiene una extracción implementada.

El verificador recibe el destino original incluso si se calculó una carpeta temporal porque el argumento era `null`. El impacto no se reprodujo. La limpieza opcional de `__MACOSX` concatena el path sin insertar separador; no se garantiza su resultado.

## Copias y backups de archivos

`Files::setBackupDirectory()` fija una carpeta. `Files::cp()` puede mover un destino existente ahí antes de comprobar `$simulate`; no uses ese flag como una simulación sin efectos.

## Backups MySQL

`MySQLDatabaseBackup::export()` construye una llamada a `mysqldump` con argumentos escapados y ejecuta `shell_exec()`. Depende de que el comando esté disponible; la salida no confirma que el backup sea válido y la contraseña se pasa al proceso.

`exportUsingCnf()` usa una ruta `--defaults-file` interpolada en la cadena de shell sin escapar y no devuelve el resultado del comando. No le pases una ruta no confiable.

`exportTableAsCSV($tableName)` consulta `secure_file_priv`, elimina una salida existente y ejecuta `SELECT ... INTO OUTFILE`. Requiere permisos del servidor MySQL; el nombre de tabla se interpola en SQL sin validación.

No se ejecutó un comando, conexión, archivo ZIP ni exportación CSV.
