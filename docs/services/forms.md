# HTML y formularios

El marcado HTML se genera con clases de paquetes separados del core. `boctulus/simplerest-html-generation` aporta `HtmlBuilder\Html` y `HtmlBuilder\Tag`; `boctulus/simplerest-bootstrap-html` aporta `Bt5Form`, `AdminLte` y `BootstrapPaginator`. Estas clases producen HTML; no administran el estado de un formulario, no reciben automáticamente `Request` y no sustituyen la [validación del servidor](validation.md).

## Controles Bootstrap

La ruta nueva de `Bt5Form` es `Boctulus\Simplerest\HtmlBuilders\Bt5Form`. Un ejemplo de uso presente en los tests del paquete genera un control y devuelve el marcado como string:

```php
use Boctulus\Simplerest\HtmlBuilders\Bt5Form;

echo '<form method="post" action="/profile">';
echo Bt5Form::inputText(null, ['name' => 'email', 'id' => 'email']);
echo Bt5Form::submit('Guardar');
echo '</form>';
```

`Bt5Form` extiende el `Html` genérico, que ofrece controles como `inputText`, `password`, `email`, `number`, `date`, `file`, `select`, `checkbox`, `radio`, `area` y `submit`. La clase Bootstrap añade clases y componentes como `formCheck`, `formFloating`, `inputGroup`, `switch`, navegación, offcanvas, tabs, modals, toasts, cards, alerts, accordions y tablas. La lista exacta debe leerse en la versión del paquete instalada.

`Html::setIdAsName()` configura el builder para copiar `name` en `id`. `Html::macro()` registra un método de generación propio y `__callStatic()` delega los nombres no declarados a la creación genérica de tags. `Tag::registerBuilder($class)` selecciona el builder para los objetos `Tag`; debe llamarse antes de instanciarlos.

`AdminLte` extiende `Bt5Form` y agrega componentes de ese tema, como alertas, botones, ribbons, controles Select2 y máscaras. El paquete también incluye `BootstrapPaginator::render($data, $short_after, $show_last)`, que lee `last_page` y `current_page` de `$data['paginator']` y escribe el HTML directamente. El paginador usa la URL actual y la clave de página de configuración.

El paquete no incluye Bootstrap, AdminLTE ni otros assets frontend; la aplicación debe suministrarlos. `boctulus/simplerest-html-generation` tampoco incluye esos assets. Consulta los [límites de paquetes](../architecture/composer-packages.md) para distinguir el runtime del core de las extensiones.

## Escape y contenido

El builder concatena valores de texto y atributos en el HTML generado; no aplica `htmlspecialchars()` ni sanitiza HTML. En el ejemplo, cualquier valor externo debe codificarse según su contexto antes de pasarlo como valor o contenido. El marcado generado no constituye una frontera de seguridad.

## Paquetes y compatibilidad sin resolver

`Bt5Form` extiende `Boctulus\Simplerest\Core\Libs\HtmlBuilder\Html`, cuya clase está en el paquete `simplerest-html-generation`. Sin embargo, el `composer.json` revisado de `simplerest-bootstrap-html` solo declara dependencia directa de `boctulus/simplerest`; no declara `simplerest-html-generation`. El core Composer distribuye `src/framework/`, que no contiene ese builder. Por tanto, el manifiesto de Bootstrap HTML no describe por sí solo toda la dependencia de su clase base.

El README y un test del paquete también mencionan nombres de compatibilidad antiguos bajo `Core\Libs\HtmlBuilder\`, pero no encontré una declaración de alias en el código fuente ni en el autoload del manifiesto consultado. La disponibilidad de esos nombres en un consumidor limpio queda sin verificar. No se ejecutó una instalación consumidora ni la suite `HtmlBuildersPackageTest`.

## Límites de verificación

Se inspeccionaron los builders, los manifiestos Composer, el README y los tests del paquete. Las aserciones de los tests se revisaron, pero no se ejecutaron. No se validó el CSS/JavaScript de los componentes, una instalación standalone ni la interoperabilidad con `Validator`.
