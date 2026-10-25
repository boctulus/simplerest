---
title: HTML y formularios
source:
  - docs/services/forms.md
source_revision: 88a81da51b6c8c4c035180e3004023391205f616
status: partial
---

# HTML y formularios

Los builders de HTML pertenecen a paquetes separados del core. La identidad Composer `boctulus/simplerest-bootstrap-html` (carpeta local `simplerest-bootstrap`) aporta `Boctulus\Simplerest\HtmlBuilder\Html`, `Tag`, `Boctulus\Simplerest\HtmlBuilders\Bt5Form` y `AdminLte`, además de `Boctulus\Simplerest\Integrations\Libs\BootstrapPaginator`. Generan markup: no administran estado, no reciben automáticamente `Request` ni sustituyen la [validación del servidor](/services/validation).

## Controles Bootstrap

La clase actual `Bt5Form` usa el namespace `Boctulus\Simplerest\HtmlBuilders`:

```php
use Boctulus\Simplerest\HtmlBuilders\Bt5Form;

echo '<form method="post" action="/profile">';
echo Bt5Form::inputText(null, ['name' => 'email', 'id' => 'email']);
echo Bt5Form::submit('Guardar');
echo '</form>';
```

El builder genérico incluye controles como `inputText`, `password`, `email`, `number`, `date`, `file`, `select`, `checkbox`, `radio`, `area` y `submit`. La clase Bootstrap añade clases y componentes como formularios flotantes, input groups, navegación, modals, toasts, cards y tablas. Consulta el paquete instalado para su lista exacta.

`Html::setIdAsName()` copia `name` en `id`; `Html::macro()` registra métodos propios y `__callStatic()` crea tags no declarados. `Tag::registerBuilder($class)` selecciona el builder de nuevos objetos `Tag`. `AdminLte` agrega controles específicos del tema. `BootstrapPaginator::render()` escribe HTML y espera `last_page` y `current_page` dentro de `$data['paginator']`.

El paquete no incluye Bootstrap, AdminLTE ni sus assets frontend: debe suministrarlos la aplicación. Los antiguos directorios locales `simplerest-html-generation` y `simplerest-bootstrap-html` ya no existen con esos nombres; el manifiesto conserva `boctulus/simplerest-bootstrap-html` y el builder base vive en esa misma carpeta local. Consulta la guía pública sobre [microcore y paquetes Composer](/core/microcore).

## Escape y compatibilidad

Los builders concatenan texto y atributos sin aplicar `htmlspecialchars()` ni sanitización. Codifica los valores según su contexto; el markup generado no es una frontera de seguridad.

`Bt5Form` extiende `Boctulus\Simplerest\HtmlBuilder\Html`, incluido en el mismo paquete. El autoload PSR-4 cubre ambas clases; `src/legacy_aliases.php`, cargado mediante `autoload.files`, ofrece los nombres históricos bajo `Core\Libs\HtmlBuilder` y `Core\Libs\BootstrapPaginator` cuando el core no los proporciona. No hay una dependencia entre dos paquetes de builders.

`HtmlBuildersPackageTest` contiene aserciones de autoload para los namespaces nuevos y los nombres históricos. La configuración está en el manifiesto; no se ejecutó una instalación consumidora ni la suite. Tampoco se validaron los assets CSS/JavaScript ni la interoperabilidad con `Validator`.
