# Paquetes extraídos del core

## Criterio del microcore

El microcore es el árbol completo de `src/framework/` (namespace `Boctulus\Simplerest\Core\`). "Micro" significa que el core no requiere nada más que PHP: una librería demasiado específica o que necesita una dependencia externa se convierte en un paquete bajo `packages/boctulus/` que declara esa dependencia en su propio `composer.json`. La única excepción aprobada es `doctrine/inflector` (ver más abajo).

No significa un namespace paralelo, reescrituras mínimas de clases del core ni vaciar `src/framework/`. Esa interpretación se aplicó el 2026-10-23 y se revirtió el 2026-10-24 (commit `f42c3f1dd`).

`unit-tests/microcore/MicrocoreIsolationTest.php` hace cumplir el criterio: el manifiesto raíz requiere solo `php` y `doctrine/inflector`, el core no referencia librerías externas (salvo `Doctrine\Inflector\` en `Traits/SubResourceHandler.php`), cada clase del core carga sin `vendor/` ni paquetes, y no hay `class_alias`.

El [catálogo de paquetes](catalog.md) recorre cada manifiesto local y sus límites de uso. Las decisiones de distribución seleccionadas están en [Límites de paquetes Composer](../architecture/composer-packages.md), y el estado de los gates de release en [Proceso de publicación](../architecture/composer-release-process.md).

## Paquetes extraídos el 2026-10-24

En la extracción inicial, la mayoría de las clases conservaron sus nombres y se cargaron por `classmap`; `simplerest-mail` adoptó el namespace `Boctulus\Simplerest\Mail\` (PSR-4). En la reorganización actual, Bootstrap HTML y Web Inspection cargan aliases condicionales desde `autoload.files` para los FQCN heredados; el core sigue sin incluir aliases. El catálogo refleja los manifiestos y namespaces del checkout actual.

| Paquete | Clases y archivos | Dependencia propia | Se queda en el core |
| --- | --- | --- | --- |
| `boctulus/simplerest-psr7` | `Psr7\ServerRequestAdapter`, `Psr7\ResponseAdapter`, `Psr7\StreamAdapter`, `Psr7\UriAdapter` y los helpers `psr7_*()` (antes `Helpers/psr7.php`, ahora `src/helpers.php` en `autoload.files` del paquete) | `psr/http-message` `^2.0` | `Request` y `Response` |
| `boctulus/simplerest-mail` | Todo el correo, en `Boctulus\Simplerest\Mail\`: `Mail` (envío SMTP con PHPMailer), `MailBase`, `MailAuditor`, `MailFromRemoteWP`, `EmailTemplate` e `Interfaces\IMail`. Antes eran `Core\Libs\...` y `Core\Interfaces\IMail` | `phpmailer/phpmailer` `^6.9 \|\| ^7.1` | Nada de correo |
| `boctulus/simplerest-jwt-auth` | `Api\AuthController` (login, refresh, registro y tokens de email/contraseña) | `firebase/php-jwt` `^7.1`, la librería JWT para PHP; no usa la plataforma Firebase | `Interfaces\IAuth` y `Factory::auth()` |
| `boctulus/simplerest-css` | `Libs\CSS`: descarga y extracción de hojas de estilo de una página, `beautifier()`, `getCSSRules()` y `getCSSClasses()` | `sabberworm/php-css-parser` `^8.4` (antes no estaba declarada en ningún manifiesto) | `Libs\HTML` y `Libs\XML` |
| `boctulus/simplerest-html` | `Libs\HtmlMinifier`: `minify()` (antes `Strings::minimifyHTML()`), `removeCSS()` y `removeCSSClasses()` (antes en `CSS`) | Ninguna externa | `Libs\HTML` y el resto de `Libs\Strings` |

Los cinco paquetes de la extracción inicial requerían `boctulus/simplerest` `^2.0 || ^3.0`. En el checkout actual hay dependencias entre paquetes: `boctulus/simplerest-api-clients` requiere `boctulus/simplerest-mail` `^1.0`, porque `SendinBlue` extiende `MailBase` e implementa `IMail`; `boctulus/simplerest-scaffolding` requiere `boctulus/simplerest-i18n` `^1.0`. `simplerest-phpparser` declara `nikic/php-parser` y no depende del core.

`MailBase`, `IMail`, `MailAuditor` y `MailFromRemoteWP` se quedaron primero en el core porque no tienen dependencias externas. Se movieron al paquete el 2026-10-24, porque la ausencia de dependencias no basta para que una clase pertenezca al core. `MailAuditor` sigue escribiendo en el mismo directorio que antes (`src/logs/mail` del core).

En la extracción inicial, `simplerest-css` y `simplerest-html` se separaron por dominio: `removeCSS()` y `removeCSSClasses()` reescriben HTML (quitan bloques `<style>` y atributos `style`/`class`) y no necesitan analizar CSS. Esa distinción funcional se mantiene. El checkout actual agrupa `HtmlMinifier` con `CodeReducer` en `boctulus/simplerest-codereducer`; la razón para combinar esas herramientas no está documentada aquí y el catálogo registra la propiedad Composer vigente.

## Reorganización posterior en el checkout

La carpeta local `simplerest-bootstrap` conserva la identidad Composer `boctulus/simplerest-bootstrap-html`. Ahora contiene también `HtmlBuilder\Html` y `HtmlBuilder\Tag`, junto con `Bt5Form`, `AdminLte` y `BootstrapPaginator`; ya no hay una carpeta instalable `simplerest-html-generation` en este checkout.

Las herramientas se separaron de `simplerest-scaffolding`: `boctulus/simplerest-codereducer` contiene `CodeReducer` y `HtmlMinifier`; `boctulus/simplerest-phpparser` contiene `PHPParser` y declara `nikic/php-parser`; `boctulus/simplerest-postman-gen` contiene `PostmanGenerator`. `simplerest-scaffolding` conserva el grupo CLI `make` y declara `simplerest-i18n`. `CustomTags` y `EasyHTMLTable`, presentes en la guía del paquete HTML anterior, no aparecen en el código actual bajo `src/` ni `packages/boctulus/`.

Los paquetes `simplerest-machine-id` y `simplerest-string-obfuscation` tampoco tienen manifiestos locales en el checkout actual. `legacy-helpers.md` conserva sus descripciones como referencia histórica, no como catálogo instalable.

## Excepción aprobada: doctrine/inflector

`Traits\SubResourceHandler` usa `doctrine/inflector` en una sola llamada, para pluralizar el alias de una relación pivot (la clave del JSON de subrecursos). Como el trait lo usan `Model` y `ApiController`, no puede ir a un paquete.

Historia del 2026-10-24:

1. El commit `f42c3f1dd` quitó la dependencia y usó el nombre de la tabla relacionada sin pluralizar. Era una regresión para tablas con nombre singular o irregular (`status`, `person`, `category`). En los esquemas de SimpleRest y GoProp todas las tablas de pivots ya estaban en plural, por lo que ningún alias existente cambió.
2. El commit `280dc21e3` restauró el plural con `Libs\Inflector`, una copia de las reglas de Doctrine dentro del core. Se descartó porque es código de terceros dentro del core, que no es de autoría de SimpleRest.
3. Decisión del mantenedor: `doctrine/inflector` `^2.0` vuelve a ser dependencia del core, como única excepción al criterio "solo PHP". `SubResourceHandler` es idéntico a la versión anterior al microcore.

`unit-tests/microcore/MicrocoreIsolationTest.php` admite esa dependencia solo en `Traits/SubResourceHandler.php`. `unit-tests/microcore/PivotAliasInflectorTest.php` compara `pluralize()` con un fixture de 627 casos generado con Doctrine 2.1.0 (todas las tablas de esos esquemas y todas las reglas en inglés): si una actualización dentro de `^2.0` cambia algún plural, la prueba falla antes de que cambie una clave de la API.

## Consumidores en este monorepo

- `composer-dev.json` registra los paquetes locales por `classmap` o PSR-4 y carga los helpers PSR-7/i18n y aliases de Bootstrap HTML/Web Inspection por `files`.
- `app/Controllers/CssExtractorController.php` usa `CSS` y depende ahora de `boctulus/simplerest-css`.
- Las dependencias externas de los paquetes (`psr/http-message`, `phpmailer/phpmailer`, `firebase/php-jwt`, `nikic/php-parser`, Symfony y otras) no están en `composer-dev.json`. Sin instalar esas dependencias, cargar las clases que las usan falla; este archivo de desarrollo no sustituye la instalación Composer de cada paquete.
- `app/Controllers/MyAuthController.php` y `DumbAuthController.php` extienden `AuthController`; los controladores de `app/`, `config/` y `unit-tests/mail/` que usan `Mail` importan `Boctulus\Simplerest\Mail\Mail` y dependen de `boctulus/simplerest-mail`. GoProp (`app/Libs/Mail/Mailer.php`, `app/Services/MessageEmailService.php` y dos tests) todavía usa los nombres `Core\Libs\Mail` y `Core\Libs\EmailTemplate`, y falta migrarlo.

## Pendiente

- `Libs\Factory::auth()` instancia `Boctulus\Simplerest\Controllers\MyAuthController`, una clase de la aplicación. Es un acoplamiento del core con `app/` anterior a esta extracción.
