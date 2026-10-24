# SimpleRest HTML

`boctulus/simplerest-html` contains `HtmlMinifier`, which reduces a scraped
HTML page for text processing: it strips tags such as `head`, `script`,
`style`, `iframe` and `svg`, event and presentation attributes, comments, CSS
and CSS classes, `data-*` attributes and empty tags. Level 7 or higher
returns plain text.

```php
use Boctulus\Simplerest\Core\Libs\HtmlMinifier;

$text = HtmlMinifier::minify($html, 5); // 0 < $level < 8
```

`HtmlMinifier::removeCSS()` removes `<style>` blocks and inline `style`
attributes, and `HtmlMinifier::removeCSSClasses()` removes all or selected
`class` values. Both are plain HTML rewriting and need no CSS parser.

`HtmlMinifier::minify()` was `Strings::minimifyHTML()` in the core, and the two
removal methods were `CSS::removeCSS()` and `CSS::removeCSSClasses()`. They
were extracted so the framework itself requires nothing but PHP. The package
depends only on the core: it uses the core `HTML` and `Strings` helpers and
does not require `boctulus/simplerest-css`.
