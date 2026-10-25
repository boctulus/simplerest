# SimpleRest Bootstrap HTML

`boctulus/simplerest-bootstrap-html` builds Bootstrap 5 forms, AdminLTE
components, and Bootstrap pagination markup for SimpleRest applications.
The monorepo stores it in `packages/boctulus/simplerest-bootstrap`; its
Composer identity remains `boctulus/simplerest-bootstrap-html`.

It also contains the base `Html` and `Tag` builders. `AdminLte` extends
`Bt5Form`, and `BootstrapPaginator` renders Bootstrap pagination. It does not
ship Bootstrap or AdminLTE frontend assets; the consuming application supplies
them.

The package is not registered on Packagist yet. Install it from GitHub for
now:

```bash
composer config repositories.simplerest-bootstrap-html vcs https://github.com/boctulus/simplerest-bootstrap-html
composer require boctulus/simplerest-bootstrap-html:dev-main
```

The current package namespaces are:
`Boctulus\Simplerest\HtmlBuilder\Html` and `Tag`,
`Boctulus\Simplerest\HtmlBuilders\Bt5Form`,
`Boctulus\Simplerest\HtmlBuilders\AdminLte`, and
`Boctulus\Simplerest\Integrations\Libs\BootstrapPaginator`. Composer loads
these classes from this optional package. `src/legacy_aliases.php`, loaded by
Composer `autoload.files`, conditionally aliases the previous
`Boctulus\Simplerest\Core\Libs\HtmlBuilder\*` and
`Boctulus\Simplerest\Core\Libs\BootstrapPaginator` names when the core does
not already provide them.

```php
use Boctulus\Simplerest\HtmlBuilders\AdminLte;
use Boctulus\Simplerest\HtmlBuilders\Bt5Form;

echo Bt5Form::inputText('user@example.test', ['name' => 'email']);
echo AdminLte::alert('Profile saved');
```

Run the package contract tests with `composer test`.
