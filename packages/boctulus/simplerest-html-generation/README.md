# SimpleRest HTML Generation

`boctulus/simplerest-html-generation` provides server-side helpers for creating
HTML markup: the generic `HtmlBuilder` tag and document classes, a custom-tag
parser, and an HTML table renderer.

It contains `HtmlBuilder\Html`, `HtmlBuilder\Tag`, `CustomTags`, and
`EasyHTMLTable` under their existing `Boctulus\Simplerest\Core\Libs`
namespaces. It does not include Bootstrap or AdminLTE components or frontend
assets; those belong to `boctulus/simplerest-bootstrap-html` and the consuming
application, respectively.
