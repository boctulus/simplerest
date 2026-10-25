---

title: "Localización del paquete i18n"
source:
  - docs/packages/localization.md
source_revision: 379f2383edbe4978e934195d6a6ed4102651d154
status: partial
---

# Localización del paquete i18n

`boctulus/simplerest-i18n` implementa catálogo gettext, lectura de archivos `.po` y detección de idioma. El paquete depende de SimpleRest; los archivos de locale pertenecen a la aplicación anfitriona y se mantienen en `app/Locale/`.

## Clases y helpers

- `Translate` enlaza un dominio y busca mensajes en sus catálogos; expone `bind()` y usa el helper global `trans()` para resolver texto.
- `POParser` analiza `.po` y puede conservar una caché si se configura una ruta.
- `LangDetector::detect($text)` estima un código de idioma con reglas locales.
- `setLang()` y `trans()` se cargan desde `src/helpers.php` por Composer `autoload.files`.
- `AlternativeGetTextTranslator` implementa `PHPTAL_TranslationService`; la interfaz debe existir antes de cargar esa clase. El paquete no declara PHPTAL como dependencia Composer.

Ejemplo observado en el README:

```php
use Boctulus\Simplerest\Core\Libs\i18n\Translate;

Translate::bind('messages');
echo trans('Welcome');
```

La ruta, formato, derechos de escritura y contenido de los catálogos son responsabilidad de la aplicación. No se cargó locale ni se ejecutó traducción en esta revisión.
