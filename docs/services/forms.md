# HTML y formularios

El marcado HTML se genera con clases de paquetes separados del core. La identidad Composer `boctulus/simplerest-bootstrap-html` (carpeta local `simplerest-bootstrap`) aporta `Boctulus\Simplerest\HtmlBuilder\Html`, `Tag`, `Boctulus\Simplerest\HtmlBuilders\Bt5Form` y `AdminLte`, además de `Boctulus\Simplerest\Integrations\Libs\BootstrapPaginator`. Estas clases producen HTML; no administran el estado de un formulario, no reciben automáticamente `Request` y no sustituyen la [validación del servidor](validation.md).

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

El paquete no incluye Bootstrap, AdminLTE ni otros assets frontend; la aplicación debe suministrarlos. Los antiguos directorios locales `simplerest-html-generation` y `simplerest-bootstrap-html` ya no existen con esos nombres; el manifiesto conserva `boctulus/simplerest-bootstrap-html` y el builder base vive en esa misma carpeta local. Consulta los [límites de paquetes](../architecture/composer-packages.md) para distinguir el runtime del core de las extensiones.

## Escape y contenido

El builder concatena valores de texto y atributos en el HTML generado; no aplica `htmlspecialchars()` ni sanitiza HTML. En el ejemplo, cualquier valor externo debe codificarse según su contexto antes de pasarlo como valor o contenido. El marcado generado no constituye una frontera de seguridad.

## Paquetes y compatibilidad

`Bt5Form` extiende `Boctulus\Simplerest\HtmlBuilder\Html`, incluido en el mismo paquete. El autoload PSR-4 cubre ambas clases; el archivo `src/legacy_aliases.php` carga mediante `autoload.files` los nombres históricos bajo `Core\Libs\HtmlBuilder` y `Core\Libs\BootstrapPaginator` cuando el core no los proporciona. No hay una dependencia entre dos paquetes de builders.

`HtmlBuildersPackageTest` contiene aserciones de autoload para los namespaces nuevos y los nombres históricos. La configuración del autoload está presente en el manifiesto; no se ejecutó una instalación consumidora ni la suite de pruebas.

## Límites de verificación

Se inspeccionaron los builders, los manifiestos Composer, el README y los tests del paquete. Las aserciones de los tests se revisaron, pero no se ejecutaron. No se validó el CSS/JavaScript de los componentes, una instalación standalone ni la interoperabilidad con `Validator`.
