# SimpleRest CLI

`boctulus/simplerest-cli` provides:

- the general-purpose `php com` command groups: `acl`, `file`, `migrations`, `module`, `mysql`, `roles`, `router`, `sql`, `system`, `users`;
- `vendor/bin/simplerest init`, which creates the application bootstrap (`com`, `app.php`, `index.php`, `config/`, `.env`) in a project that installed SimpleRest with Composer.

The SimpleRest core (`boctulus/simplerest`) is a library and ships no commands or entry point. File generators (`make`) live in `boctulus/simplerest-scaffolding`.

## Installation in an existing project

```bash
composer require boctulus/simplerest-cli boctulus/simplerest-scaffolding
vendor/bin/simplerest init --dry-run   # shows what would be created
vendor/bin/simplerest init
php com
```

`init` never overwrites an existing file; it reports it as `SKIP`. Running it again is safe. It copies `.env.example` to `.env` only when `.env` does not exist.

The generated `config/commands.php` enables `boctulus/simplerest-cli` and `boctulus/simplerest-scaffolding`. An enabled package that is not installed is skipped.

Controllers, models and schemas use the `Boctulus\Simplerest\` namespace. Add it to the project's `composer.json` if it is missing, then run `composer dump-autoload`:

```json
"autoload": { "psr-4": { "Boctulus\\Simplerest\\": "app/" } }
```

`init` does not edit `composer.json`.

## Dependencies

- `boctulus/simplerest-i18n` (required): the generated `app.php` initializes translations (`Translate`, `setLang()`), and the core calls `trans()`.
- `boctulus/simplerest-scaffolding` (suggested): `migrations make` and `migrations migrate --make=schema,model` use it to generate files. Without it, these commands stop with an error that explains how to install it.

## Database access

`acl` and `users` commands look up the users table the first time they run, not when they are listed. `php com` and `php com <group> help` therefore work before a database or schemas exist.

## Tests

```bash
vendor/bin/phpunit -c phpunit.xml.dist
```

The tests check the package structure and the installer. Command discovery needs a host application, so it is verified with `php com` in a project.
