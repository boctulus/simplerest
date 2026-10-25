---
title: Microcore y paquetes
source:
  - docs/packages/README.md
  - docs/architecture/composer-packages.md
source_revision: 564c9b7eadbcbc076164a176325b1a0f6eb4e63b
status: partial
---

# Microcore y paquetes

El core de SimpleRest es el árbol completo de `src/framework/`, con el namespace `Boctulus\Simplerest\Core\`. Que sea un microcore significa que no requiere nada más que PHP, con una única excepción: `doctrine/inflector`. No es un núcleo recortado a unas pocas clases ni un namespace aparte.

Cuando una librería es demasiado específica o necesita una dependencia externa, se convierte en un paquete de `packages/boctulus/` que declara esa dependencia en su propio `composer.json`. El core sigue completo.

## Qué garantiza el core

- El `composer.json` de `boctulus/simplerest` requiere únicamente `php` y `doctrine/inflector`.
- Cada clase de `src/framework/` carga sin `vendor/` y sin ningún paquete instalado.
- El core no contiene aliases (`class_alias`) ni shims de compatibilidad.

La suite `unit-tests/microcore` comprueba estas tres condiciones. Un consumidor que instala solo `boctulus/simplerest` obtiene `Request`, `Response`, el router, `Model`, `DB`, `ApiController`, `Acl` y el resto del framework.

## Paquetes extraídos del core

La mayoría de estos paquetes conserva los nombres de clase originales; Mail usa un namespace propio y algunos paquetes actuales cargan aliases condicionales para FQCN heredados. Para usar sus clases hay que instalar el paquete; con solo el core, esas clases no existen.

| Paquete | Qué contiene | Dependencia | Qué queda en el core |
| --- | --- | --- | --- |
| `boctulus/simplerest-psr7` | Adaptadores PSR-7 de `Request` y `Response`, y los helpers `psr7_*()` | `psr/http-message` | `Request` y `Response` |
| `boctulus/simplerest-mail` | Todo el correo en `Boctulus\Simplerest\Mail\`: `Mail` (SMTP con PHPMailer), `MailBase`, `MailAuditor`, `MailFromRemoteWP`, `EmailTemplate` e `IMail` | `phpmailer/phpmailer` | Nada de correo |
| `boctulus/simplerest-jwt-auth` | `Api\AuthController` (login, refresh, registro y tokens) | `firebase/php-jwt`, la librería JWT para PHP | `IAuth` y `Factory::auth()` |
| `boctulus/simplerest-css` | `CSS`: descarga y extracción de hojas de estilo, formateo y reglas CSS | `sabberworm/php-css-parser` | `HTML` y `XML` |
| `boctulus/simplerest-codereducer` | `CodeReducer` para PHP y `HtmlMinifier` para reescribir HTML | Ninguna externa | `HTML` y `Strings` |

En el checkout actual, `simplerest-api-clients` depende de `simplerest-mail` y `simplerest-scaffolding` depende de `simplerest-i18n`. La separación funcional entre CSS y HTML sigue vigente: quitar `<style>` o atributos `class` reescribe HTML y no necesita `simplerest-css`, aunque `HtmlMinifier` ahora se distribuye en `simplerest-codereducer`.

La carpeta local `simplerest-bootstrap` conserva la identidad Composer `boctulus/simplerest-bootstrap-html` y contiene `Html`/`Tag` junto con sus helpers Bootstrap. `simplerest-phpparser` declara `nikic/php-parser` sin depender del core; `simplerest-postman-gen` contiene `PostmanGenerator`. Consulte el [catálogo de paquetes](/packages/catalog) para el inventario actual.

Otros paquetes, como `simplerest-scaffolding`, `simplerest-bootstrap-html` o `simplerest-web-inspection`, siguen el mismo criterio y están en el catálogo de límites de paquetes Composer.

## Relaciones pivot y doctrine/inflector

El alias de una relación pivot es el plural de la tabla relacionada (`status` → `statuses`, `person` → `people`) y lo calcula `doctrine/inflector`, la única dependencia del core además de PHP. Se usa solo en los subrecursos, que forman parte de `Model` y `ApiController`, por eso no puede ir a un paquete. Una prueba fija los plurales de todas las tablas conocidas, así que una actualización de la librería no puede cambiar en silencio la clave del JSON de subrecursos.

## Límites conocidos

- `Factory::auth()` crea `MyAuthController`, una clase de la aplicación, por lo que el core sigue acoplado a ese controlador.
- Esta página no acredita una instalación desde Packagist: el core `3.0.0` no está publicado.
