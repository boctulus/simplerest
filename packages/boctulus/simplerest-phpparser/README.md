# SimpleRest PHP Parser

`boctulus/simplerest-phpparser` contains the `PHPParser` helper, backed by
`nikic/php-parser`.

```php
use Boctulus\Simplerest\Core\Libs\PHPParser;

$parser = new PHPParser();
$reduced = $parser->reduceCode($source, ['findUser', 'saveUser']);
```

`reduceCode()` keeps the named methods inside class declarations and preserves
other class members. Its comment-retention parameters are reserved and
currently do not change the output.

`PHPParser::convertStaticToNonStatic($source)` is experimental. The source
itself marks this transformation as bug-prone; inspect its output before using
it.

## Requirements

- PHP 8.1 through 8.4
- `nikic/php-parser` 5.4 or later in the 5.x series

Composer uses an explicit classmap entry to preserve the existing
`Boctulus\Simplerest\Core\Libs\PHPParser` class name.

Run the package contract tests with `composer test`.
