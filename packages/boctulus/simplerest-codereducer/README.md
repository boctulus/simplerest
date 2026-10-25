# SimpleRest Code Reducer

`boctulus/simplerest-codereducer` packages the `CodeReducer` helper for keeping
selected PHP functions or class methods in source text.

The public class keeps its existing name:

```php
use Boctulus\Simplerest\Core\Libs\CodeReducer;

$reducer = new CodeReducer();
$reduced = $reducer->reduceCode($source, ['findUser'], ['debugDump']);
```

`reduceCode()` treats an empty keep list as “keep all,” then applies the
exclusion list. Its interface-replacement arguments can replace selected
bodies with stubs. `reduceClass()` keeps the named methods from a class source.
Calling `removeComments(true)` also uses the SimpleRest core `Strings` helper.

The package also contains `HtmlMinifier`, preserving its existing class name in
`Boctulus\Simplerest\Core\Libs`. It provides `minify()`, `removeCSS()`, and
`removeCSSClasses()` for rewriting HTML content; it is not an HTML sanitizer.

```php
use Boctulus\Simplerest\Core\Libs\HtmlMinifier;

$text = HtmlMinifier::minify($html, 5);
```

## Requirements

- PHP 8.1 through 8.4
- `boctulus/simplerest` 2.x or 3.x

Composer maps the legacy class with an explicit classmap entry. This keeps the
existing PHP namespaces stable while the implementations live in this package.

Run the package contract tests with `composer test`.

## Limitations

The reducer uses regular expressions rather than a PHP syntax tree. It does not
handle every valid PHP construct reliably; use it only with source shapes that
have been checked for the intended reduction.
