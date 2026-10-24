# Generación y transformación HTML

Este grupo reúne tres contratos de salida que suelen usarse juntos: elementos HTML genéricos, helpers Bootstrap/AdminLTE y transformaciones de markup. No instala JavaScript, CSS, Bootstrap ni AdminLTE en la aplicación anfitriona.

## `simplerest-html-generation`

Las clases principales están bajo `Boctulus\Simplerest\Core\Libs`:

- `HtmlBuilder\Html` crea elementos y documentos HTML; contiene helpers estáticos para etiquetas, controles, tablas y botones, y permite registrar macros.
- `HtmlBuilder\Tag` representa una etiqueta y sus atributos.
- `CustomTags::register($name, $callback)` registra un callback para reemplazar tags personalizados. `parse()`, `parseAll()`, `render()` y `replace()` procesan el texto según esos registros.
- `EasyHTMLTable` compone cabecera y filas con `setHead()`, `setRows()`, `addRow()` y `renderHTML()`.

El paquete produce strings HTML. La aplicación debe decidir cómo incorpora el resultado y qué escape, validación de atributos y política de contenido aplica.

## `simplerest-bootstrap-html`

`Bt5Form` añade formularios y controles Bootstrap 5; `AdminLte` extiende `Bt5Form`; `BootstrapPaginator` genera markup de paginación Bootstrap. Sus nombres de clase se cargan desde el paquete opcional. El paquete no agrega assets frontend al core y la plantilla debe tener cargadas las hojas de estilo y scripts que requiera.

```php
use Boctulus\Simplerest\HtmlBuilders\Bt5Form;

echo Bt5Form::inputText('user@example.test', ['name' => 'email']);
```

La llamada de ejemplo produce markup; por sí sola no demuestra escape contextual ni una protección frente a contenido no confiable.

## `simplerest-html`

`HtmlMinifier::minify($html, $level)` elimina o simplifica partes de una página para análisis de texto. El README limita `$level` al rango `1..7`; `removeCSS()` quita bloques `<style>` y atributos `style`, y `removeCSSClasses()` elimina todos o ciertos valores `class`. Estas funciones reescriben contenido, no son un sanitizador HTML para mostrar entrada hostil.

## Límites y dependencias

- Los paquetes `html-generation`, `bootstrap-html` y `html` requieren `boctulus/simplerest`; no comparten una clase base entre sí.
- `bootstrap-html` no instala assets del navegador.
- La salida de builders y minifiers no se verificó como segura para HTML no confiable.
- Para recuperar y parsear CSS remoto usa `boctulus/simplerest-css`; ese camino requiere `sabberworm/php-css-parser` y controles de destino del llamador.

Los nombres de método enumerados se contrastaron con los puntos de entrada del paquete. No se ejecutaron sus pruebas ni se verificó la salida en un navegador.
