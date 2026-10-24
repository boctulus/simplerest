# Service providers

`app.php` reads the configured `providers` list in order, constructs each provider, then calls `register()` followed by `boot()`. This is a bootstrap extension point; it does not itself imply that a provider binds services into `Container`.

## Provider contract

`IServiceProvider` declares `register()` and `boot()`. The abstract `ServiceProvider` base implements `boot()` and leaves `register()` abstract, so a child provider supplies the registration behavior.

The base `boot()` reflects the child class file to find its package directory. If the package has `../config/config.php`, it loads that array and places it under `packages.{vendor}.{package}`, where vendor and package are the lowercased first two namespace segments of the provider class. It then includes adjacent `../config/routes.php` and `../config/cli_routes.php` files when they exist.

The base implementation does not check the HTTP or CLI router switches before including those files. Route files therefore need to register declarations safely for the process in which the provider is booted. The provider does not discover arbitrary package files or automatically wire constructor dependencies.

## Evidence and limits

The lifecycle is traced to `app.php`, `IServiceProvider`, `ServiceProvider`, and `Config::loadPackageConfig()`. No provider lifecycle was executed in an isolated application, and no package route file was loaded as part of this audit.
