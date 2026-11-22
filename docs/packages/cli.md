# CLI de uso general

`boctulus/simplerest-cli` contiene los grupos de comandos de `php com` que necesita cualquier aplicación SimpleRest: `acl`, `file`, `migrations`, `module`, `mysql`, `roles`, `router`, `sql`, `system` y `users`. Su manifiesto requiere solo `boctulus/simplerest ^3.0`.

El core es una librería (`src/framework`) y no distribuye comandos ni el entry point `com`; véase el [contrato PACK](../architecture/pack-contract.md). Los generadores de archivos (`make`) están en [scaffolding](scaffolding.md).

## Habilitar comandos

Instalar el paquete no habilita sus grupos. La aplicación anfitriona debe incluirlo en `config/commands.php`:

```php
'packages' => [
    'boctulus/simplerest-cli',
    'boctulus/simplerest-scaffolding',
],
```

`CommandRegistry` busca los grupos en `vendor/boctulus/simplerest-cli/src/Commands/<grupo>/` (o en `packages/boctulus/simplerest-cli/` dentro del monorepo). Un comando del host en `app/Commands/<grupo>/` tiene prioridad sobre el del paquete; véase el [catálogo CLI](../cli/commands.md).

## Dependencia con scaffolding

`migrations make` y `migrations migrate --make=schema,model` usan `MakeMigrationFileCommand` de `boctulus/simplerest-scaffolding`. Si ese paquete no está instalado, el comando falla y el mensaje de error indica cómo instalarlo. El paquete lo declara como `suggest`, no como `require`.

## Herramientas del monorepo

Los grupos `pack`, `todo`, `skill` y `doc` siguen en `app/Commands/` del monorepo de desarrollo y no se distribuyen.

## Estado

Creado el 2026-11-22 al mover los grupos desde `app/Commands/` con `git mv`; en el monorepo, `php com` y la ayuda de cada grupo producen la misma salida que antes del movimiento. Todavía no tiene repositorio GitHub ni versión en Packagist. El plan de distribución (entry point `vendor/bin/simplerest init`, publicación de docs y skills, skeleton) está en [`docs/to-do/consumer-cli-skeleton-laravel-style.md`](../to-do/consumer-cli-skeleton-laravel-style.md).
