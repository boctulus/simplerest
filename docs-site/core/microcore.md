---
title: Microcore y paquetes
source:
  - docs/packages/README.md
  - docs/architecture/composer-packages.md
source_revision: 564c9b7eadbcbc076164a176325b1a0f6eb4e63b
status: partial
---

# Microcore y paquetes

El core de SimpleRest es el árbol completo de `src/framework/`, con el namespace `Boctulus\Simplerest\Core\`. Que sea un microcore significa que no requiere nada más que PHP. No es un núcleo recortado a unas pocas clases ni un namespace aparte.

Cuando una librería es demasiado específica o necesita una dependencia externa, se convierte en un paquete de `packages/boctulus/` que declara esa dependencia en su propio `composer.json`. El core sigue completo y sin dependencias.

## Qué garantiza el core

- El `composer.json` de `boctulus/simplerest` requiere únicamente `php`.
- Cada clase de `src/framework/` carga sin `vendor/` y sin ningún paquete instalado.
- El core no contiene aliases (`class_alias`) ni shims de compatibilidad.

La suite `unit-tests/microcore` comprueba estas tres condiciones. Un consumidor que instala solo `boctulus/simplerest` obtiene `Request`, `Response`, el router, `Model`, `DB`, `ApiController`, `Acl` y el resto del framework.

## Paquetes extraídos del core

Estos paquetes conservan los nombres de clase originales. Para usar sus clases hay que instalar el paquete; con solo el core, esas clases no existen.

| Paquete | Qué contiene | Dependencia | Qué queda en el core |
| --- | --- | --- | --- |
| `boctulus/simplerest-psr7` | Adaptadores PSR-7 de `Request` y `Response`, y los helpers `psr7_*()` | `psr/http-message` | `Request` y `Response` |
| `boctulus/simplerest-mail` | Todo el correo en `Boctulus\Simplerest\Mail\`: `Mail` (SMTP con PHPMailer), `MailBase`, `MailAuditor`, `MailFromRemoteWP`, `EmailTemplate` e `IMail` | `phpmailer/phpmailer` | Nada de correo |
| `boctulus/simplerest-jwt-auth` | `Api\AuthController` (login, refresh, registro y tokens) | `firebase/php-jwt`, la librería JWT para PHP | `IAuth` y `Factory::auth()` |
| `boctulus/simplerest-css` | `CSS`: descarga y extracción de hojas de estilo, formateo y reglas CSS | `sabberworm/php-css-parser` | `HTML` y `XML` |
| `boctulus/simplerest-html` | `HtmlMinifier`: minificación de HTML y eliminación de CSS y clases de un HTML | Ninguna externa | `HTML` y `Strings` |

Ningún paquete extraído depende de otro; `simplerest-api-clients` sí requiere `simplerest-mail`, porque su cliente SendinBlue es un mailer. `simplerest-html` no necesita `simplerest-css`: quitar `<style>` o atributos `class` de un HTML es reescribir HTML, no parsear CSS.

Otros paquetes, como `simplerest-scaffolding`, `simplerest-bootstrap-html`, `simplerest-web-inspection` o `simplerest-api-clients`, siguen el mismo criterio y están en el catálogo de límites de paquetes Composer.

## Relaciones pivot sin doctrine/inflector

Los subrecursos ya no dependen de `doctrine/inflector`, pero el alias de una relación pivot sigue siendo el plural de la tabla relacionada (`status` → `statuses`, `person` → `people`). Lo calcula `Inflector::pluralize()` del core, que reproduce las reglas de plural en inglés de `doctrine/inflector` 2.1.0. Una prueba compara su salida con la de Doctrine, así que la clave del JSON de subrecursos no cambia.

## Límites conocidos

- `Factory::auth()` crea `MyAuthController`, una clase de la aplicación, por lo que el core sigue acoplado a ese controlador.
- Esta página no acredita una instalación desde Packagist: el core `3.0.0` no está publicado.
