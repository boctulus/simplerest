# `php com` y el registro de comandos

Esta referencia describe el catálogo de comandos operativos. Es independiente de [`CliRouter`](routing.md): el registro `CommandRegistry` atiende grupos de `php com`; si no reconoce el grupo, el entry point puede continuar por el router CLI y después por el front controller.

## Entrada y orden de resolución

Salvo por el atajo de empaquetado, `com` carga `app.php`, retira `--debug`/`-d` de los argumentos y crea `CommandRegistry`. Sin argumentos muestra los grupos; `php com help` muestra el catálogo y `php com help <grupo>` describe los comandos de un grupo. Si el primer argumento coincide con un grupo registrado, despacha allí. Solo si no coincide continúa por `CliRouter` cuando `console_router` está habilitado y, después, por `FrontController` cuando `front_controller` está habilitado.

`php com pack build`, `php com pack run` y `php com pack package` toman una rama especial: cargan `scripts/pack/build-core.php` y terminan sin arrancar `app.php` ni `CommandRegistry`.

Esta rama especial no usa la ayuda normal de `CommandRegistry`: en esta revisión `php com pack build --help` devolvió `Unsupported pack option: --help`. `php com help pack` sí muestra el resumen del grupo; `php com pack dry-run` es el modo de vista previa previsto por el código.

## Descubrimiento y prioridad

`CommandRegistry` lee `config/commands.php`. Descubre archivos `*Command.php` en carpetas por grupo, empezando por la ruta host `COMMANDS_PATH`, seguida por rutas adicionales, paquetes Composer habilitados y rutas configuradas para framework. Omite carpetas cuyo nombre empieza por `.` o `_`, y archivos base cuyo nombre comienza por `Base` o `Abstract`.

En este checkout, `COMMANDS_PATH` apunta a `app/Commands`; la configuración no agrega rutas adicionales, habilita `boctulus/simplerest-scaffolding` y no declara overrides. Los comandos de paquete se buscan en `vendor/<vendor>/<package>/src/Commands` o en las ubicaciones del paquete monorepo. Si un paquete opcional habilitado no existe, ese proveedor se omite.

Un comando debe ser una subclase de `BaseCommand`, declarar su nombre y tener `execute()`. Su `config()` describe argumentos requeridos, opciones, flags y defaults; el constructor puede declarar alias y ejemplos. El parser reconoce `--key=value`, `--key:value`, flags `--key`, flags cortos de una letra y argumentos posicionales. Convierte guiones en claves con guion bajo y aplica defaults. La validación base solo exige las claves declaradas como requeridas; el contrato de tipos o valores admitidos depende del propio comando.

Las colisiones entre proveedores se resuelven por prioridad: aplicación host, extensión y framework. Dos proveedores en el mismo nivel causan una excepción hasta que `config/commands.php` seleccione uno con `overrides`. Los aliases pertenecen al comando ganador. `cross_aliases` declara nombres que apuntan a otro grupo; aquí `make acl` redirige a `acl make`.

## Grupos disponibles en este checkout

La salida observada de `php com help` el 2026-10-24 enumeró estos grupos y cantidades. El catálogo se construye al iniciar y puede variar con la configuración o los paquetes habilitados.

| Grupo de invocación | Comandos mostrados |
| --- | ---: |
| `acl` | 23 |
| `doc` | 1 |
| `file` | 3 |
| `make` | 43 |
| `migrations` | 15 |
| `module` | 1 |
| `mysql` | 4 |
| `pack` | 2 |
| `roles` | 2 |
| `router` | 1 |
| `skill` | 5 |
| `sql` | 11 |
| `system` | 2 |
| `todo` | 7 |
| `users` | 11 |

Usa la ayuda generada para ver el estado actual, sin asumir que el catálogo estático sea idéntico en otra aplicación:

```text
php com help
php com help acl
php com migrations help
php com migrations migrate --help
```

En esta revisión se ejecutaron la ayuda global, la ayuda de grupo para `file`, `migrations`, `sql`, `users`, `todo`, `skill`, `make`, `module`, `pack`, `mysql`, `doc`, `acl` y `system`, y `--help` para comandos seleccionados. Sólo se solicitaron pantallas de ayuda; no se ejecutó ningún subcomando operativo.

## Efectos observables por grupo

La ayuda y la implementación exponen estas diferencias que afectan el uso. La tabla no sustituye `--help` para el contrato completo de argumentos.

| Grupo o comandos | Efecto observado en la implementación |
| --- | --- |
| `sql` | `count`, `describe`, `find`, `find-by`, `list`, `select` y `query` son consultas; `query` documenta `SELECT`, `SHOW` y `DESCRIBE`. `export` escribe CSV o JSON. `statement` ejecuta sentencias no-`SELECT`; el código exige `--force` para `DROP`, `TRUNCATE` y `DELETE` sin `WHERE`, pero no para `INSERT`, `UPDATE` ni `DELETE` con `WHERE`. |
| `migrations` | `list` inspecciona archivos. `make` crea un archivo de migración; `migrate` y sus variantes ejecutan DDL, mientras `rollback`, `redo`, `reset` y `refresh` revierten o vuelven a aplicar migraciones. `clear` vacía el registro `migrations` sin revertir DDL. `fresh` elimina todas las tablas y requiere `--to` y `--force`. |
| `acl` y `users` | Algunos comandos leen el usuario, los roles y los permisos de la base configurada; otros modifican esos registros. `clear-tb` y `replace-tb` alteran permisos de tabla; `replace-tb` deja en `NULL` los permisos omitidos. `users delete-user` sólo establece `is_active = 0` y exige `--force`. |
| `file`, `todo` y `skill` | `file copy` escribe archivos; `file delete` los elimina con `unlink()` y ofrece `--dry-run` y `--force`. Los comandos `todo` crean o modifican tarjetas; `todo delete` mueve el archivo a `docs/to-do/abandoned/`. `skill create` y `skill delete` escriben o eliminan skills; `skill delete` puede afectar varias ubicaciones de agentes y ofrece `--dry-run`. |
| `make` | El grupo habilitado aporta generadores que escriben archivos y comandos de inspección que pueden consultar la base configurada. En `make model`, `--force` permite sobrescribir y `--remove` elimina el archivo generado. Véase [scaffolding y herramientas de desarrollo](../packages/scaffolding.md); no se ejecutó ningún generador. |
| `doc`, `module` y `pack` | `doc from-json` imprime Markdown derivado de un JSON. `module to-package` copia el módulo, genera manifiestos y archivos del paquete, y elimina el módulo original salvo que se pase `--keep-module`. `pack build` genera el directorio de distribución indicado; `pack dry-run` muestra la operación sin aplicarla. No se ejecutó ninguna conversión ni empaquetado. |
| `system` y `mysql` | `system clear` ejecuta `composer dump-autoload -o` y `composer clear-cache`; `opcache-clear` reinicia el OPcache disponible. Los comandos del grupo `mysql` activan, desactivan o vuelcan el log de consultas de SimpleRest; no son controles del servicio MySQL. |

Los comandos que cambian base de datos, archivos, cuentas, permisos, autoload o paquetes no se deben inferir como ejecutados por consultar su ayuda. Para `--force`, destinos de base, rutas y opciones de sobrescritura, revisa la ayuda concreta y el código del comando.

## Límite de verificación

Hay tests fuente para el parser y dispatch de `CommandRegistry`, así como para descubrimiento y prioridad de proveedores Composer; se inspeccionaron, pero no se ejecutaron. Esta revisión ejecutó únicamente los comandos de ayuda citados. Las páginas de [migraciones](../database/migrations.md) y [routing CLI](routing.md) cubren sus contratos por separado.

Fuentes inspeccionadas: [`com`](../../com), [`CommandRegistry`](../../src/framework/Libs/CommandRegistry.php), [`BaseCommand`](../../src/framework/Commands/BaseCommand.php) y [`config/commands.php`](../../config/commands.php). Tests consultados: [dispatch](../../unit-tests/command/CommandRegistryDispatchTest.php), [descubrimiento Composer](../../unit-tests/command/CommandRegistryComposerPackageTest.php) y [parser](../../unit-tests/command/CommandTraitTest.php).
