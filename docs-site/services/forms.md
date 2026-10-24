---
title: HTML y formularios
source:
  - docs/services/forms.md
source_revision: 88a81da51b6c8c4c035180e3004023391205f616
status: partial
---

# HTML y formularios

Los builders de HTML pertenecen a paquetes separados del core. `boctulus/simplerest-html-generation` aporta `HtmlBuilder\Html` y `HtmlBuilder\Tag`; `boctulus/simplerest-bootstrap-html` aporta `Bt5Form`, `AdminLte` y `BootstrapPaginator`. Generan markup: no administran estado, no reciben automáticamente `Request` ni sustituyen la [validación del servidor](/services/validation).

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

Los paquetes no incluyen Bootstrap, AdminLTE ni sus assets frontend: debe suministrarlos la aplicación. Consulta la guía pública sobre [microcore y paquetes Composer](/core/microcore).

## Escape y compatibilidad

Los builders concatenan texto y atributos sin aplicar `htmlspecialchars()` ni sanitización. Codifica los valores según su contexto; el markup generado no es una frontera de seguridad.

El `composer.json` revisado de `simplerest-bootstrap-html` no declara dependencia directa de `simplerest-html-generation`, aunque `Bt5Form` extiende su `Html`. El core distribuido tampoco contiene ese builder. README y tests mencionan namespaces de compatibilidad anteriores sin una declaración de alias encontrada en el código/autoload consultado. Una instalación consumidora limpia no se ejecutó, así que la disponibilidad de esos nombres sigue sin verificar.

Se inspeccionaron builders, manifiestos, README y tests de paquete; no se ejecutaron. Tampoco se validaron los assets CSS/JavaScript ni la interoperabilidad con `Validator`.
