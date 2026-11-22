# CLI de uso general

`boctulus/simplerest-cli` contiene los grupos de comandos de `php com` que necesita cualquier aplicación SimpleRest: `acl`, `file`, `migrations`, `module`, `mysql`, `roles`, `router`, `sql`, `system` y `users`. También incluye `vendor/bin/simplerest init`, que crea el bootstrap de la aplicación. Su manifiesto requiere `boctulus/simplerest ^3.0` y `boctulus/simplerest-i18n ^1.0`.

## Proyecto instalado con `composer require`

Un proyecto que sólo requiere el core no tiene `com`, `app.php` ni `config/`. `init` los crea:

```powershell
composer require boctulus/simplerest-cli boctulus/simplerest-scaffolding
vendor/bin/simplerest init --dry-run
vendor/bin/simplerest init
php com
```

`init` copia los stubs de `stubs/project/` del paquete (`com`, `app.php`, `index.php`, `.env.example` y `config/`), crea `.env` a partir de `.env.example` cuando no existe y crea los directorios `app/*`, `database/migrations`, `logs`, `cache` y `storage/security`. Nunca sobrescribe un archivo existente: lo informa como `SKIP`, así que se puede ejecutar varias veces. No modifica `composer.json`; si falta el autoload `"Boctulus\\Simplerest\\": "app/"`, hay que agregarlo a mano.

Los stubs son plantillas propias, no copias de la configuración del monorepo: no habilitan providers, conexiones de base de datos de otras aplicaciones, rutas de prueba ni claves de API. `config/constants.php` resuelve `CORE_PATH` con `Composer\InstalledVersions` (`vendor/boctulus/simplerest/src/framework/`). `config/acl.php` no se genera porque depende de un paquete ACL; lo aporta el paquete ACL que se instale.

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

## Acceso a la base de datos

Los comandos `acl` y `users` resuelven la tabla de usuarios y su clave primaria la primera vez que se ejecutan (`usersTable()`, `idField()`), no en el constructor. Antes del 2026-11-22 lo hacían en el constructor, así que `php com` y cualquier `help` fallaban en un proyecto sin base de datos o sin `UsersSchema`.

## Herramientas del monorepo

Los grupos `pack`, `todo`, `skill` y `doc` siguen en `app/Commands/` del monorepo de desarrollo y no se distribuyen.

## Estado

Creado el 2026-11-22 al mover los grupos desde `app/Commands/` con `git mv`; en el monorepo, `php com` y la ayuda de cada grupo producen la misma salida que antes del movimiento.

`init` se verificó el 2026-11-22 en un proyecto vacío con PHP 8.3.36 y Composer 2.8.5. El proyecto tenía el core `3.0.5` de Packagist, y `cli`, `i18n` y `scaffolding` como repositorios `path`.
- `php com` listó los 12 grupos esperados. `make` tiene 42 comandos, contra 44 en el monorepo, porque `make acl` y `make package` son alias que define sólo la config del monorepo.
- `php com migrations help` y `php com router list` funcionaron.
- Un segundo `init` no escribió nada.
- Con `php -S`, `GET /system/health` devolvió HTTP 200 `{"ok":true}`. `GET /` devolvió 404 porque todavía no hay `HomeController`.

### Piezas base (agregadas el 2026-11-22)

`init` también genera:
- `.htaccess`, sin las directivas `php_value`, que fallan en servidores sin `mod_php`;
- `app/Models/MyModel.php` y `app/Models/main/UsersModel.php`, con los nombres de campo estáticos y el hash de contraseña que usan `AuthController` y `users`;
- `app/Controllers/MyAuthController.php`, solo con los hooks. Extiende `AuthController` de `boctulus/simplerest-jwt-auth`, sugerido por el paquete; `Factory::auth()` lo instancia en cada pedido a la API;
- `app/Controllers/HomeController.php`, `app/Views/home.php` y el layout `app/Views/templates/tpl_basic.php`, que es el layout por defecto de `WebController`;
- `config/acl.php.example`;
- las 15 migraciones base: `migrations`, `users`, `roles`, `user_roles`, permisos de tabla, especiales y de negación, `sp_permissions`, `api_keys`, `webhooks` y sus alteraciones posteriores.

`make any all` no sobrescribe el `UsersModel` generado por `init`.

Para que la API responda hace falta un ACL concreto: `Core\Security\Acl` es abstracto. Hay que instalar un paquete ACL, copiar `config/acl.php.example` a `config/acl.php` y registrar el provider del paquete en `config/config.php`. `config/acl.php.example` está escrito para `boctulus/simplerest-fine-grained-acl`; para `boctulus/simplerest-basic-acl` sigue el ejemplo de su README. Los dos son públicos en Packagist desde el 2026-11-23. Desde `simplerest-cli` `v1.0.1`, el rol `guest` del ejemplo no tiene permisos. En `v1.0.0` tenía `read_all` y `write_all`, o sea escritura anónima en todas las tablas; un proyecto creado con `v1.0.0` debe revisar su `config/acl.php`.

Si falta una ruta de `config/autoload.php` o el directorio de helpers del core, `app.php` termina con un mensaje que nombra esa ruta, no con una excepción de `DirectoryIterator`.

Recorrido completo verificado el mismo día (detalle en el [incidente](../issues/consumer-composer-require-bootstrap-2026-11-22.md#acceptance-run-2026-11-22)):
1. `init`.
2. `migrations migrate` en una base vacía.
3. `make any all --schema --model --api`.
4. CRUD generado con filtros, `fields` y `orderBy` por HTTP.

Publicado el 2026-11-22: `boctulus/simplerest-cli` `v1.0.0`, junto con `simplerest-i18n`, `simplerest-jwt-auth` y `simplerest-scaffolding` `v1.0.0` y el core `3.0.6`. La evidencia está en el [proceso de releases](../architecture/composer-release-process.md). Para un proyecto nuevo:

```powershell
composer require boctulus/simplerest boctulus/simplerest-cli boctulus/simplerest-scaffolding boctulus/simplerest-jwt-auth
php vendor/bin/simplerest init
php com
```

La API generada requiere además un paquete ACL (`composer require boctulus/simplerest-fine-grained-acl`, más `config/acl.php` y el provider). Hay un detalle: fine-grained sincroniza los roles de `config/acl.php` con la tabla `roles` la primera vez que se carga el ACL, así que `php com acl assign-role --role=admin` falla con "Rol no existe" hasta que se haga un primer pedido a la API.

Recorrido verificado el 2026-11-23 con todo instalado solo desde Packagist, en una base vacía:
- migraciones, generador y `users create-user`/`enable`/`verify-email`;
- `POST /api/v1/auth/login` con `username`, que devolvió 200 con un token JWT;
- como admin, `GET /api/v1/roles` 200 (con `orderBy` y `fields`) y `POST` 201;
- como guest, 401. El plan de distribución (entry point `vendor/bin/simplerest init`, publicación de docs y skills, skeleton) está en [`docs/to-do/consumer-cli-skeleton-laravel-style.md`](../to-do/consumer-cli-skeleton-laravel-style.md).
