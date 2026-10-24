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
| `boctulus/simplerest-mail` | `Mail` (SMTP con PHPMailer) y `EmailTemplate` | `phpmailer/phpmailer` | `MailBase`, `IMail`, `MailAuditor`, `MailFromRemoteWP` |
| `boctulus/simplerest-jwt-auth` | `Api\AuthController` (login, refresh, registro y tokens) | `firebase/php-jwt`, la librería JWT para PHP | `IAuth` y `Factory::auth()` |
| `boctulus/simplerest-css` | `CSS`: descarga y extracción de hojas de estilo, formateo y reglas CSS | `sabberworm/php-css-parser` | `HTML` y `XML` |
| `boctulus/simplerest-html` | `HtmlMinifier`: minificación de HTML y eliminación de CSS y clases de un HTML | Ninguna externa | `HTML` y `Strings` |

Ningún paquete extraído depende de otro. `simplerest-html` no necesita `simplerest-css`: quitar `<style>` o atributos `class` de un HTML es reescribir HTML, no parsear CSS.

Otros paquetes, como `simplerest-scaffolding`, `simplerest-bootstrap-html`, `simplerest-web-inspection` o `simplerest-api-clients`, siguen el mismo criterio y están en el catálogo de límites de paquetes Composer.

## Relaciones pivot sin Inflector

Los subrecursos ya no usan `doctrine/inflector`. El alias de una relación pivot es el nombre de la tabla relacionada. Con tablas en plural, que es lo habitual, la respuesta no cambia; con una tabla en singular, la clave del JSON de subrecursos es ese nombre en singular. Este caso todavía no tiene una prueba automática.

## Límites conocidos

- `Factory::auth()` crea `MyAuthController`, una clase de la aplicación, por lo que el core sigue acoplado a ese controlador.
- Esta página no acredita una instalación desde Packagist: el core `3.0.0` no está publicado.
