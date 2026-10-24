# SimpleRest CSS

`boctulus/simplerest-css` contains the `CSS` helper: it downloads and extracts
a page's stylesheets, parses and beautifies CSS, reads CSS rules for given
classes, and lists the CSS classes used in an HTML document. Removing CSS from
HTML belongs to `HtmlMinifier` in `boctulus/simplerest-html`. It requires
`sabberworm/php-css-parser` (used by `beautifier()` and `getCSSRules()`). It
was extracted from the core so the framework itself requires nothing but PHP.

The existing class name is kept: `Boctulus\Simplerest\Core\Libs\CSS`.
It uses the core `HTML`, `XML`, `Strings`, `Files` and `Url` helpers.
