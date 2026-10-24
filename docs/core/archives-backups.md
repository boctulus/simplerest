# ZIP y backups

Esta página reúne los helpers de archivo comprimido y exportación MySQL. Ambos escriben archivos o invocan herramientas externas; no se ejecutaron durante la auditoría.

## [`ZipManager`](../../src/framework/Libs/ZipManager.php)

`zip($ori, $dst, $exclude = null, $overwrite = true)` requiere la extensión PHP `zip`. Puede añadir un archivo o recorrer un directorio, aplicar exclusiones y escribir en el destino. Para archivos individuales usa `file_get_contents()` antes de añadir el contenido, por lo que no ofrece una ruta de streaming para archivos grandes. Si no puede abrir la salida, retorna `false`.

`unzip($file_path, $destination = null, $verify = true)` extrae con `ZipArchive::extractTo()`. Cuando falta el destino, prepara una carpeta única bajo `Files::tempDir()`. El valor que retorna es el primer componente de ruta del primer registro ZIP, no una ruta validada de salida. El código no comprueba de antemano nombres de entrada ni aplica una política de contención a cada miembro; tampoco presentes la extracción de archivos no confiables como segura. La rama alternativa que detecta el comando `unzip` no contiene una extracción implementada.

El verificador recibe el destino original, incluso cuando `unzip()` calculó un directorio temporal porque el argumento venía como `null`. El efecto depende del entorno y no se reprodujo. `setIncludeMacOSXDir()` controla un intento de limpieza de `__MACOSX`; la concatenación de su ruta no inserta un separador. No se garantiza que esa limpieza funcione.

## Backups de archivos con [`Files`](../../src/framework/Libs/Files.php)

`Files::setBackupDirectory($path)` guarda un destino estático y crea el directorio. `Files::cp($ori, $dst, $simulate, $overwrite, $callable)` mueve al backup un destino existente antes de copiar o simular. Si se activa `$simulate`, el cambio de ubicación previo todavía puede ocurrir. No uses el flag como garantía de una operación sin escrituras. La copia recursiva `Files::copy()` tiene su propio recorrido y exclusiones; revisa los paths antes de usarla.

## [`MySQLDatabaseBackup`](../../src/framework/Libs/MySQLDatabaseBackup.php)

La clase declara tres operaciones distintas:

| Método | Efecto trazado en fuente | Condiciones y límites |
| --- | --- | --- |
| `export($host, $username, $password, $databaseName, $outputFile)` | Construye una llamada a `mysqldump` mediante `shell_exec()` y escapa sus argumentos. | Depende de la disponibilidad de `mysqldump`, `shell_exec()` y credenciales MySQL; el secreto se pasa como argumento del proceso. El retorno de `shell_exec()` no certifica un backup íntegro. |
| `exportUsingCnf($cnf_file_path, $databaseName, $outputFile)` | Construye otra llamada a `mysqldump` basada en un archivo defaults. | La ruta del archivo defaults se interpola en la cadena de shell sin escapar; el método es `void` y no devuelve el resultado del comando. No pases una ruta no confiable. |
| `exportTableAsCSV($tableName)` | Consulta `secure_file_priv`, elimina si existe la salida CSV y ejecuta `SELECT ... INTO OUTFILE`. | Requiere permisos/configuración MySQL para escribir en esa ubicación. El nombre de tabla se interpola en SQL y no se valida como identificador. No pases datos no confiables. |

Un comentario de ejemplo dentro de la clase contiene valores con forma de credencial; se omitieron de esta documentación y deben revisarse por separado. Ningún comando shell, conexión ni exportación de tabla se ejecutó.

## Evidencia

La guía refleja `ZipManager`, `Files` y `MySQLDatabaseBackup` del checkout. Requiere verificación adicional por plataforma y con un destino desechable antes de usar cualquiera de estas operaciones en una aplicación.
