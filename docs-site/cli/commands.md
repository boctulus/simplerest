---
title: "php com y el registro de comandos"
source:
  - docs/cli/commands.md
source_revision: 0d6dfdd0feb07531af7a8ca327a58c291c6a2206
status: partial
---

# `php com` y el registro de comandos

Esta referencia describe el catálogo de comandos operativos. Es independiente de [`CliRouter`](/cli/routing): `CommandRegistry` atiende grupos de `php com`; si no reconoce el grupo, el entry point puede continuar por el router CLI y después por el front controller.

## Entrada y orden de resolución

Salvo por el atajo de empaquetado, `com` carga `app.php`, retira `--debug`/`-d` de los argumentos y crea `CommandRegistry`. Sin argumentos muestra los grupos; `php com help` muestra el catálogo y `php com help <grupo>` describe los comandos de un grupo. Si el primer argumento coincide con un grupo registrado, despacha allí. Solo si no coincide continúa por `CliRouter` cuando `console_router` está habilitado y, después, por `FrontController` cuando `front_controller` está habilitado.

`php com pack build`, `php com pack run` y `php com pack package` toman una rama especial: cargan el script de empaquetado y terminan sin arrancar `app.php` ni `CommandRegistry`.

## Descubrimiento y prioridad

`CommandRegistry` lee `config/commands.php`. Descubre archivos `*Command.php` en carpetas por grupo, empezando por la ruta host `COMMANDS_PATH`, seguida por rutas adicionales, paquetes Composer habilitados y rutas configuradas para framework. Omite carpetas cuyo nombre empieza por `.` o `_`, y archivos base cuyo nombre comienza por `Base` o `Abstract`.

En el checkout auditado, `COMMANDS_PATH` apunta a `app/Commands`; la configuración no agrega rutas adicionales, habilita `boctulus/simplerest-scaffolding` y no declara overrides. Los comandos de paquete se buscan en las ubicaciones `src/Commands` del paquete. Si un paquete opcional habilitado no existe, ese proveedor se omite.

Un comando debe ser una subclase de `BaseCommand`, declarar su nombre y tener `execute()`. Su `config()` describe argumentos requeridos, opciones, flags y defaults; el constructor puede declarar alias y ejemplos. El parser reconoce `--key=value`, `--key:value`, flags `--key`, flags cortos de una letra y argumentos posicionales. Convierte guiones en claves con guion bajo y aplica defaults. La validación base exige las claves declaradas como requeridas; el contrato de tipos o valores admitidos depende del comando.

Las colisiones entre proveedores se resuelven por prioridad: aplicación host, extensión y framework. Dos proveedores en el mismo nivel causan una excepción hasta que `config/commands.php` seleccione uno con `overrides`. Los aliases pertenecen al comando ganador. `cross_aliases` declara nombres que apuntan a otro grupo; en este checkout, `make acl` redirige a `acl make`.

## Grupos disponibles en el checkout auditado

La salida observada de `php com help` el 2026-10-24 enumeró estos grupos y cantidades. El catálogo se construye al iniciar y puede variar con la configuración o los paquetes habilitados.

| Grupo | Comandos mostrados |
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

Consulta la ayuda de la instalación actual:

```text
php com help
php com help acl
php com migrations help
php com migrations migrate --help
```

Esas consultas de ayuda se ejecutaron en el checkout auditado. No se ejecutó ningún subcomando operativo durante esa revisión.

## Límite de verificación

Se inspeccionaron tests del parser y despacho de `CommandRegistry`, así como del descubrimiento y prioridad de proveedores Composer, pero no se ejecutaron. La revisión ejecutó únicamente los comandos de ayuda citados. Consulta por separado [migraciones](/database/migrations) y [routing CLI](/cli/routing): describen contratos diferentes.
