# SimpleRest Code Tools

Optional code-generation and PHP source tools for SimpleRest development.

This package is not registered on Packagist yet. Its current checkout can be
used locally, but consumers must add an explicit VCS repository after the
matching package split has been committed and made available. Do not rely on
Packagist-only resolution until registration is complete.

This package provides `PHPParser`, the `make` command group, and optional HTML
cleaners. Enable command discovery explicitly in the host application's
`config/commands.php`:

```php
'packages' => ['boctulus/simplerest-code-tools'],
```

The host application keeps its own `app/Commands/` directory and command entry
point. The package requires `nikic/php-parser` and the SimpleRest core.

`Boctulus\Simplerest\CodeTools\Cleaners\AngularCleaner` and
`Boctulus\Simplerest\CodeTools\Cleaners\BootstrapCleaner` live here. The
core retains deprecated aliases in the old `Core\Libs\code_cleaner` namespace;
using either alias requires this package to be installed.
