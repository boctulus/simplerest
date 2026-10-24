# Ciclo de migraciones

Esta página describe el registro y los comandos de migración que existen en este checkout. La ayuda de CLI se consultó y el código se trazó; no se ejecutó ninguna migración ni se abrió una base de datos.

## Ubicación y formato

La ruta predeterminada de `MIGRATIONS_PATH` es `database/migrations/`, declarada en `config/constants.php`. `migrate` selecciona archivos PHP del directorio, omite archivos y carpetas ocultos y ordena por nombre cuando no recibe un archivo específico. `--dir` permite usar un subdirectorio de esa ruta o una ruta absoluta; `--file` selecciona un archivo.

El template actual genera una clase de migración con `up()` y `down()`. `IMigration` declara `up()`; el rollback comprueba por separado que exista `down()`. Al cargar un archivo, el comando acepta una instancia `IMigration` retornada por el archivo o busca una clase derivada del nombre del archivo y la instancia. No se validó un archivo recién generado.

Al iniciar una migración normal, `up()` corre dentro de una transacción iniciada por el comando; al terminar, el código confirma y después inserta el registro de aplicación. Una excepción `Exception` durante `up()` activa rollback y el bloque `finally` vuelve a habilitar constraints. Esto no garantiza atomicidad para todo DDL ni para cada driver. La ruta de rollback ejecuta `down()` y elimina el registro, pero no la envuelve en el mismo bloque transaccional.

## Registro de aplicadas

Si falta la tabla `migrations`, `doMigrate()` carga y ejecuta primero `0000_00_00_00000000_migrations.php` para crearla, y registra ese archivo. Esta inicialización sucede antes de evaluar la bandera `--simulate` para las migraciones ordinarias.

Por eso `--simulate` no es una garantía de que no haya escrituras: para las migraciones ordinarias omite la llamada a `up()` y el registro de esa migración, pero antes incluye el archivo y construye la instancia; además, si falta la tabla de seguimiento ejecuta el DDL de inicialización. Úsalo únicamente comprendiendo esa rama y el código del archivo.

La tabla de seguimiento se crea y consulta desde la conexión predeterminada; cada fila guarda nombre de archivo, path y, para una conexión distinta de la predeterminada, el identificador de conexión. `--to=<conexión>` selecciona un identificador registrado para ejecutar la migración. Sin esa opción, el comando usa la conexión predeterminada. El contenido de los registros no se consultó.

## Comandos disponibles

`php com migrations help` enumeró 15 comandos en la ayuda ejecutada el 2026-10-24:

| Comando | Alias | Comportamiento y notas |
| --- | --- | --- |
| `clear` | `clear-log` | Borra filas del registro `migrations`; no ejecuta `down()` ni revierte DDL. Sin `--to`, la rama actual borra las entradas del registro completo. |
| `fresh` | — | Destructivo: requiere `--to` y `--force`. Borra tablas de la conexión elegida y limpia el registro; `--migrate` vuelve a aplicar migraciones. Si se indica `--file` o `--dir`, la fuente deriva a `redo` en vez de borrar todas las tablas. |
| `list` | `ls` | Enumera archivos disponibles; admite filtros `--dir`, `--table` y `--contains`. No es un informe del estado aplicado en la base. |
| `make` | `create`, `new` | Genera un archivo; admite `--dir`, `--table`, `--class-name`, `--to` y `--create`. Puede escribir el archivo de salida. |
| `migrate` | `up`, `run` | Ejecuta pendientes. Admite `--file`, `--dir`, `--to`, `--step`, `--skip`, `--make`, `--retry` y `--simulate`. |
| `migrate-module` | `module-migrate`, `migrate:module` | Ejecuta el directorio `modules/<módulo>/database/migrations`; admite `--step` y `--to`. |
| `migrate-package` | `package-migrate`, `migrate:package` | Ejecuta `packages/boctulus/<paquete>/database/migrations`; admite `--step` y `--to`. |
| `redo` | — | Llama a rollback y luego a migrate con las mismas opciones; usa `--file` para señalar una migración concreta. |
| `refresh` | — | Llama a `reset` y luego a `migrate`; puede ejecutar todos los métodos `down()` y `up()` del alcance seleccionado. |
| `refresh-module` | `module-refresh`, `refresh:module` | Revierte y reaplica las migraciones de un módulo. |
| `refresh-package` | `package-refresh`, `refresh:package` | Revierte y reaplica las migraciones de un paquete. |
| `reset` | `rollback-all` | Revierte todas las migraciones registradas mediante `down()`; no equivale a borrar tablas directamente. |
| `rollback` | `down` | Revierte por default una migración; admite `--step`, `--all`, `--file`, `--dir`, `--to` y `--simulate`. |
| `rollback-module` | `module-rollback`, `rollback:module` | Revierte migraciones de un módulo; admite `--step` o `--all`. |
| `rollback-package` | `package-rollback`, `rollback:package` | Revierte migraciones de un paquete; admite `--step` o `--all`. |

La ayuda del grupo no enumera comandos `status` ni `log`. Para confirmar el catálogo de otra aplicación consulta la ayuda generada de esa instalación.

## Flags que requieren especial cuidado

- En `migrations migrate`, `--force` es tratado por el código como alias de `--retry`: vuelve a ejecutar migraciones que ya figuran aplicadas. No funciona como confirmación de seguridad.
- En `migrations fresh`, `--force` es la confirmación exigida junto con una conexión `--to` explícita. Es un significado distinto al de `migrate --force`.
- `--ignore` con `--file` registra la migración como ignorada sin ejecutar `up()` cuando ya existe la tabla de seguimiento.
- `--simulate` omite `up()` para las migraciones ordinarias, pero tiene el límite de inicialización e inclusión indicado arriba.
- `clear`, `reset`, `refresh`, `redo`, `rollback`, `fresh` y los equivalentes de módulo/paquete pueden cambiar datos, DDL o el registro. No se ejecutaron.

No confundas `php com migrations ...` con el grupo de scaffolding `php com make ...`, con el [registro de comandos CLI](../cli/commands.md) ni con el DSL del [constructor DDL](schema-builder.md). La selección de PDO y conexiones se explica en [Conexiones](connections.md).

## Verificación

Se ejecutaron solo `php com migrations help`, `php com migrations migrate --help`, `php com migrations make --help` y la ayuda de `fresh`; estos comandos mostraron metadatos de ayuda y no ejecutaron una migración. Se inspeccionaron `BaseMigrationsCommand`, los wrappers de comando, `Migration`, `IMigration` y los templates.

No se encontró una suite dedicada a ejecución de migraciones bajo `unit-tests/`; los tests de `CommandRegistry` consultados cubren parser, dispatch y proveedores de comandos, no efectos de DDL. No se verificaron una conexión, una transacción real, rollback, simulación, ejecución por módulo/paquete ni compatibilidad por driver.

Fuentes inspeccionadas: [`MIGRATIONS_PATH`](../../config/constants.php#L96), [`BaseMigrationsCommand`](../../app/Commands/migrations/BaseMigrationsCommand.php), [`Migration`](../../src/framework/Libs/Migration.php), [`IMigration`](../../src/framework/Interfaces/IMigration.php), [template de migración](../../src/framework/Templates/Migration.php) e [inicializador del registro](../../database/migrations/0000_00_00_00000000_migrations.php).
