# SimpleRest HTML Builders

Composer package containing the Bootstrap 5 `Bt5Form` and AdminLTE `AdminLte` HTML builders.

```bash
composer require boctulus/simplerest-html-builders
```

The package requires SimpleRest and uses the core `Html`, `Strings`, and `Arrays` helpers. It has no copied frontend assets.

The package exposes `Boctulus\Simplerest\HtmlBuilders\Bt5Form` and `Boctulus\Simplerest\HtmlBuilders\AdminLte`. SimpleRest 3.x keeps the former core FQCNs as Composer compatibility aliases when this package is installed. Code that checks the reflected class name or stores serialized class names can observe the namespace move.

Run the package contract tests with:

```bash
composer test
```
