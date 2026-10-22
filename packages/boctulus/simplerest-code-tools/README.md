# SimpleRest Code Tools

Optional code-generation and PHP source tools for SimpleRest development.

Install the package in the host application:

```sh
composer require --dev boctulus/simplerest-code-tools
```

This package provides `PHPParser` and the `make` command group. Enable command
discovery explicitly in the host application's `config/commands.php`:

```php
'packages' => ['boctulus/simplerest-code-tools'],
```

The host application keeps its own `app/Commands/` directory and command entry
point. The package requires `nikic/php-parser` and the SimpleRest core.
