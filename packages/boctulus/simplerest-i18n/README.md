# SimpleRest i18n

`boctulus/simplerest-i18n` contains the gettext-style translation support used
by SimpleRest applications: `.po` parsing, translation-domain handling, and
language detection. Locale catalogs remain application data under the host
application's `app/Locale/` directory.

The package keeps the existing class names:

- `Boctulus\Simplerest\Core\Libs\i18n\Translate`
- `Boctulus\Simplerest\Core\Libs\i18n\POParser`
- `Boctulus\Simplerest\Core\Libs\LangDetector`

It also autoloads the existing global `setLang()` and `trans()` helpers.

```php
use Boctulus\Simplerest\Core\Libs\i18n\Translate;

Translate::bind('messages');
echo trans('Welcome');
```

`Translate` uses SimpleRest core services for filesystem access, logging, and
application configuration. `AlternativeGetTextTranslator` also requires the
host application to provide the `PHPTAL_TranslationService` interface before
that class is loaded.
