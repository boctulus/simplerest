# SimpleRest CLI

`boctulus/simplerest-cli` provides the general-purpose `php com` command groups for SimpleRest applications:

`acl`, `file`, `migrations`, `module`, `mysql`, `roles`, `router`, `sql`, `system`, `users`.

The SimpleRest core (`boctulus/simplerest`) is a library and ships no commands. File generators (`make`) live in `boctulus/simplerest-scaffolding`.

## Installation

```bash
composer require boctulus/simplerest-cli
```

Enable the package in the host application's `config/commands.php`:

```php
'packages' => [
    'boctulus/simplerest-cli',
],
```

The host application must provide the `com` entry point and its bootstrap (`app.php`, `config/`).

## Commands that need scaffolding

`migrations make` and `migrations migrate --make=schema,model` generate files through `boctulus/simplerest-scaffolding`. If that package is not installed, these commands stop with an error that explains how to install it.

## Tests

```bash
vendor/bin/phpunit -c phpunit.xml.dist
```

The package tests check the package structure. Command discovery needs a host application, so it is verified with `php com` in a project.
