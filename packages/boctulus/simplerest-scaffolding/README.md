# SimpleRest Scaffolding

`boctulus/simplerest-scaffolding` provides the `make` command group for
generating SimpleRest application code: models, controllers, APIs, migrations,
middleware, modules, packages, and related project files. It also includes
`PHPParser`, `CodeReducer`, `Obfuscator`, `PostmanGenerator`, and optional
Angular and Bootstrap HTML cleaners.

The package is not registered on Packagist yet. Install it from GitHub for
now:

```bash
composer config repositories.simplerest-scaffolding vcs https://github.com/boctulus/simplerest-scaffolding
composer require boctulus/simplerest-scaffolding:dev-main
```

Enable command discovery explicitly in the host application's
`config/commands.php` after installing the package:

```php
'packages' => ['boctulus/simplerest-scaffolding'],
```

For example, once the host app has database configuration and a registered
command entry point, generate a model with:

```bash
php com make model users
```

The host application keeps its own `app/Commands/` directory and command entry
point. The package requires `nikic/php-parser` and the SimpleRest core.

`Boctulus\Simplerest\CodeTools\Cleaners\AngularCleaner` and
`Boctulus\Simplerest\CodeTools\Cleaners\BootstrapCleaner` live here. The
legacy `Core\Libs` classes are class-mapped from this package; the microcore
does not provide aliases for them. The `make trans` command uses the separate
`boctulus/simplerest-i18n` package. `PHPParser` also contains an experimental
static-to-instance conversion helper with known limitations; it is not
presented as a production-safe generator.
