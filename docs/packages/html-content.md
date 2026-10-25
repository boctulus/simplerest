# Generación y transformación HTML

Este grupo reúne tres contratos de salida que suelen usarse juntos: elementos HTML genéricos, helpers Bootstrap/AdminLTE y transformaciones de markup. No instala JavaScript, CSS, Bootstrap ni AdminLTE en la aplicación anfitriona.

## Builders HTML

En el checkout actual, `boctulus/simplerest-bootstrap-html` (carpeta local `simplerest-bootstrap`) contiene los builders base bajo `Boctulus\Simplerest\HtmlBuilder`:

- `Html` crea elementos y documentos HTML; contiene helpers estáticos para etiquetas, controles, tablas y botones, y permite registrar macros.
- `Tag` representa una etiqueta y sus atributos.

La guía anterior del paquete `simplerest-html-generation` también enumeraba `CustomTags` y `EasyHTMLTable`; esas clases no aparecen en el código actual bajo `src/` ni `packages/boctulus/`, y este checkout no identifica un paquete que las reemplace.

El paquete produce strings HTML. La aplicación debe decidir cómo incorpora el resultado y qué escape, validación de atributos y política de contenido aplica.

## `simplerest-bootstrap-html`

`Boctulus\Simplerest\HtmlBuilders\Bt5Form` añade formularios y controles Bootstrap 5; `AdminLte` extiende `Bt5Form`; `Boctulus\Simplerest\Integrations\Libs\BootstrapPaginator` genera markup de paginación Bootstrap. El manifiesto Composer conserva la identidad `boctulus/simplerest-bootstrap-html`, aunque la carpeta del monorepo sea `simplerest-bootstrap`. `src/legacy_aliases.php`, cargado mediante `autoload.files`, ofrece los nombres históricos `Boctulus\Simplerest\Core\Libs\HtmlBuilder\*` y `Boctulus\Simplerest\Core\Libs\BootstrapPaginator` cuando el core no los define. El paquete no agrega assets frontend al core y la plantilla debe tener cargadas las hojas de estilo y scripts que requiera.

```php
use Boctulus\Simplerest\HtmlBuilders\Bt5Form;

echo Bt5Form::inputText('user@example.test', ['name' => 'email']);
```

La llamada de ejemplo produce markup; por sí sola no demuestra escape contextual ni una protección frente a contenido no confiable.

## `simplerest-codereducer`

`boctulus/simplerest-codereducer` contiene `Boctulus\Simplerest\Core\Libs\HtmlMinifier`, que conserva su FQCN histórico. `minify($html, $level)` elimina o simplifica partes de una página para análisis de texto (`$level` entre 1 y 7); `removeCSS()` quita bloques `<style>` y atributos `style`, y `removeCSSClasses()` elimina todos o ciertos valores `class`. Estas funciones reescriben contenido, no son un sanitizador HTML para mostrar entrada hostil.

## Límites y dependencias

- `bootstrap-html` y `codereducer` requieren `boctulus/simplerest`; sus clases HTML base viven ahora en el mismo paquete que los helpers Bootstrap.
- `bootstrap-html` no instala assets del navegador.
- La salida de builders y minifiers no se verificó como segura para HTML no confiable.
- Para recuperar y parsear CSS remoto usa `boctulus/simplerest-css`; ese camino requiere `sabberworm/php-css-parser` y controles de destino del llamador. La función `HtmlMinifier` de `codereducer` reescribe HTML y no usa ese parser CSS.

Los nombres de método enumerados se contrastaron con los puntos de entrada del paquete. No se ejecutaron sus pruebas ni se verificó la salida en un navegador.
