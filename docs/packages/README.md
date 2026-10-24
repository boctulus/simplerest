# Paquetes extraídos del core

## Criterio del microcore

El microcore es el árbol completo de `src/framework/` (namespace `Boctulus\Simplerest\Core\`). "Micro" significa que el core no requiere nada más que PHP: una librería demasiado específica o que necesita una dependencia externa se convierte en un paquete bajo `packages/boctulus/` que declara esa dependencia en su propio `composer.json`.

No significa un namespace paralelo, reescrituras mínimas de clases del core ni vaciar `src/framework/`. Esa interpretación se aplicó el 2026-10-23 y se revirtió el 2026-10-24 (commit `f42c3f1dd`).

`unit-tests/microcore/MicrocoreIsolationTest.php` hace cumplir el criterio: el manifiesto raíz requiere solo `php`, el core no referencia librerías externas, cada clase del core carga sin `vendor/` ni paquetes, y no hay `class_alias`.

El catálogo completo de paquetes y sus decisiones de distribución está en [Límites de paquetes Composer](../architecture/composer-packages.md).

## Paquetes extraídos el 2026-10-24

Los paquetes conservan los nombres de clase originales y se cargan por `classmap`, con dos excepciones: `HtmlMinifier` reúne métodos que antes estaban en `Strings` y `CSS`, y `simplerest-mail` usa su propio namespace `Boctulus\Simplerest\Mail\` (PSR-4). No hay aliases ni shims. Un consumidor que necesite alguna de estas clases debe instalar el paquete correspondiente; con solo `boctulus/simplerest` la clase no existe.

| Paquete | Clases y archivos | Dependencia propia | Se queda en el core |
| --- | --- | --- | --- |
| `boctulus/simplerest-psr7` | `Psr7\ServerRequestAdapter`, `Psr7\ResponseAdapter`, `Psr7\StreamAdapter`, `Psr7\UriAdapter` y los helpers `psr7_*()` (antes `Helpers/psr7.php`, ahora `src/helpers.php` en `autoload.files` del paquete) | `psr/http-message` `^2.0` | `Request` y `Response` |
| `boctulus/simplerest-mail` | Todo el correo, en `Boctulus\Simplerest\Mail\`: `Mail` (envío SMTP con PHPMailer), `MailBase`, `MailAuditor`, `MailFromRemoteWP`, `EmailTemplate` e `Interfaces\IMail`. Antes eran `Core\Libs\...` y `Core\Interfaces\IMail` | `phpmailer/phpmailer` `^6.9 \|\| ^7.1` | Nada de correo |
| `boctulus/simplerest-jwt-auth` | `Api\AuthController` (login, refresh, registro y tokens de email/contraseña) | `firebase/php-jwt` `^7.1`, la librería JWT para PHP; no usa la plataforma Firebase | `Interfaces\IAuth` y `Factory::auth()` |
| `boctulus/simplerest-css` | `Libs\CSS`: descarga y extracción de hojas de estilo de una página, `beautifier()`, `getCSSRules()` y `getCSSClasses()` | `sabberworm/php-css-parser` `^8.4` (antes no estaba declarada en ningún manifiesto) | `Libs\HTML` y `Libs\XML` |
| `boctulus/simplerest-html` | `Libs\HtmlMinifier`: `minify()` (antes `Strings::minimifyHTML()`), `removeCSS()` y `removeCSSClasses()` (antes en `CSS`) | Ninguna externa | `Libs\HTML` y el resto de `Libs\Strings` |

Cada paquete requiere además `boctulus/simplerest` `^2.0 || ^3.0`. Ningún paquete extraído depende de otro. `boctulus/simplerest-api-clients` requiere `boctulus/simplerest-mail` `^1.0`, porque `SendinBlue` extiende `MailBase` e implementa `IMail`.

`MailBase`, `IMail`, `MailAuditor` y `MailFromRemoteWP` se quedaron primero en el core porque no tienen dependencias externas. Se movieron al paquete el 2026-10-24, porque la ausencia de dependencias no basta para que una clase pertenezca al core. `MailAuditor` sigue escribiendo en el mismo directorio que antes (`src/logs/mail` del core).

`simplerest-css` y `simplerest-html` se separaron por dominio: `removeCSS()` y `removeCSSClasses()` no parsean CSS sino que reescriben HTML (quitan bloques `<style>` y atributos `style`/`class`), por eso pertenecen a `HtmlMinifier`. Así `simplerest-html` no necesita `simplerest-css`.

## Cambio en el core sin paquete: Inflector

`Traits\SubResourceHandler` usaba `doctrine/inflector` en una sola llamada, para pluralizar el alias de una relación pivot. Como el trait lo usan `Model` y `ApiController`, no se extrajo: se eliminó la dependencia externa.

Una primera versión (commit `f42c3f1dd`) usó el nombre de la tabla relacionada sin pluralizar. Era una regresión: cambiaba la clave del JSON de subrecursos para tablas con nombre singular o irregular (`status`, `person`, `category`). En los esquemas de SimpleRest y GoProp todas las tablas de pivots ya estaban en plural, por lo que ningún alias existente cambió.

Desde el 2026-10-24 el alias vuelve a ser el plural de la tabla relacionada. Lo calcula `Libs\Inflector::pluralize()`, una copia sin dependencias de las reglas de plural en inglés de `doctrine/inflector` 2.1.0 (la versión bloqueada antes de la extracción). `unit-tests/microcore/InflectorParityTest.php` compara su salida con un fixture de 627 casos generado con Doctrine 2.1.0, que incluye todas las tablas de esos esquemas y todas las reglas en inglés.

## Consumidores en este monorepo

- `composer-dev.json` registra los paquetes extraídos en `classmap` y los helpers PSR-7 en `files`.
- `app/Controllers/CssExtractorController.php` usa `CSS` y depende ahora de `boctulus/simplerest-css`.
- Las dependencias externas de los paquetes (`psr/http-message`, `phpmailer/phpmailer`, `firebase/php-jwt`) no están en `composer-dev.json`, igual que las de los demás paquetes locales. Sin ellas, cargar las clases que las usan falla, y las pruebas de correo y autenticación que las necesitan siguen fallando en el entorno de desarrollo.
- `app/Controllers/MyAuthController.php` y `DumbAuthController.php` extienden `AuthController`; los controladores de `app/`, `config/` y `unit-tests/mail/` que usan `Mail` importan `Boctulus\Simplerest\Mail\Mail` y dependen de `boctulus/simplerest-mail`. GoProp (`app/Libs/Mail/Mailer.php`, `app/Services/MessageEmailService.php` y dos tests) todavía usa los nombres `Core\Libs\Mail` y `Core\Libs\EmailTemplate`, y falta migrarlo.

## Pendiente

- `Libs\Factory::auth()` instancia `Boctulus\Simplerest\Controllers\MyAuthController`, una clase de la aplicación. Es un acoplamiento del core con `app/` anterior a esta extracción.
