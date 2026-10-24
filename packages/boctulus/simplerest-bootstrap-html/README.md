# SimpleRest Bootstrap HTML

`boctulus/simplerest-bootstrap-html` builds Bootstrap 5 forms, AdminLTE
components, and Bootstrap pagination markup for SimpleRest applications.

It contains `Bt5Form`, `AdminLte` (which extends `Bt5Form`), and
`BootstrapPaginator`. It does not ship Bootstrap or AdminLTE frontend assets;
the consuming application supplies them.

The package is not registered on Packagist yet. Install it from GitHub for
now:

```bash
composer config repositories.simplerest-bootstrap-html vcs https://github.com/boctulus/simplerest-bootstrap-html
composer require boctulus/simplerest-bootstrap-html:dev-main
```

The existing PHP namespaces remain available:
`Boctulus\Simplerest\HtmlBuilders\Bt5Form`,
`Boctulus\Simplerest\HtmlBuilders\AdminLte`, and
`Boctulus\Simplerest\Integrations\Libs\BootstrapPaginator`. Composer loads
these preserved class names from this optional package; the core does not add
`class_alias()` shims for them.

```php
use Boctulus\Simplerest\HtmlBuilders\AdminLte;
use Boctulus\Simplerest\HtmlBuilders\Bt5Form;

echo Bt5Form::inputText('user@example.test', ['name' => 'email']);
echo AdminLte::alert('Profile saved');
```

Run the package contract tests with `composer test`.
