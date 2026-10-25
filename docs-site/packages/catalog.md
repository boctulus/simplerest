---

title: "Catálogo de paquetes locales"
source:
  - docs/packages/catalog.md
source_revision: 379f2383edbe4978e934195d6a6ed4102651d154
status: partial
---

# Catálogo de paquetes locales

Este inventario refleja el checkout del 2026-10-24: hay 23 directorios bajo `packages/boctulus/`, de los cuales 22 contienen `composer.json`. El directorio restante, `simplerest-integrations`, sólo conserva un aviso de migración y no es instalable por Composer. El paquete raíz `boctulus/simplerest` se documenta por separado.

Se inspeccionaron los manifiestos, README y puntos de entrada PHP. Los 22 manifiestos declaran licencia MIT salvo los dos paquetes ACL, que declaran `proprietary`. Esta lista describe el código local y sus dependencias declaradas; no demuestra registro en Packagist, que un repositorio o tag siga disponible, instalación en consumidor, compatibilidad con servicios externos ni una promesa de soporte. Los resultados de publicación fechados están en el proceso de release.

## Paquetes Composer

Todas las versiones locales declaran PHP `>=8.1,<8.5`. Salvo las excepciones indicadas, los paquetes que dependen del core declaran `boctulus/simplerest ^2.0 || ^3.0`. La columna de dependencias enumera requisitos directos externos al core; las dependencias sugeridas u opcionales se aclaran en su guía.

| Área | Composer | API o responsabilidad | Dependencias declaradas | Estado y límite documentado |
| --- | --- | --- | --- | --- |
| Clientes | `boctulus/simplerest-api-clients` | Google Drive, Hugging Face, OneSignal y WooCommerce. | `ext-curl`, `simplerest-mail ^1.0`; sugiere Google API Client y Guzzle. | Llama servicios externos, puede descargar o publicar datos y requiere credenciales o SDK opcionales. |
| Datos de prueba | `boctulus/simplerest-az-seeder` | `AZSeederService`: crea productos asociados a un usuario de prueba y ofrece métodos de limpieza. | Ninguna adicional al core. Sólo acepta core `^2.0`. | Escribe y borra filas; no usar contra datos reales. |
| Seguridad | `boctulus/simplerest-basic-acl` | Implementación básica de `Acl` y registro de servicio. | Ninguna adicional al core. | Licencia `proprietary`; paquete privado. Véase [implementaciones ACL](/security/acl-implementations). |
| HTML | `boctulus/simplerest-bootstrap-html` | `HtmlBuilder\Html`, `HtmlBuilder\Tag`, `Bt5Form`, `AdminLte` y `BootstrapPaginator`. | Ninguna adicional al core. | Carpeta local `simplerest-bootstrap`; conserva identidad Composer `simplerest-bootstrap-html`. La app proporciona Bootstrap/AdminLTE y sus assets. Véase [generación y transformación HTML](/packages/html-content). |
| Desarrollo | `boctulus/simplerest-codereducer` | `CodeReducer` para PHP y `HtmlMinifier` para reescritura HTML. | Ninguna adicional al core. | El reducer usa expresiones regulares; el minifier no es un sanitizador. Véase [generación y transformación HTML](/packages/html-content). |
| CSS | `boctulus/simplerest-css` | Clase `CSS` para descargar, analizar y consultar hojas de estilo. | `sabberworm/php-css-parser ^8.4`. | Puede recuperar contenido remoto. Véase [generación y transformación HTML](/packages/html-content). |
| Seguridad | `boctulus/simplerest-fine-grained-acl` | Implementación de ACL con permisos a nivel de recurso. | Ninguna adicional al core. | Licencia `proprietary`; paquete privado. Véase [implementaciones ACL](/security/acl-implementations) y [motor de autorización](/security/authorization-engine). |
| PDF | `boctulus/simplerest-fpdf` | `PdfBase`, adaptador alrededor de FPDF. | `setasign/fpdf ^1.8`. | Puede producir una respuesta o escribir en almacenamiento según la aplicación. Véase [salida de imágenes y PDF](/packages/media-output). |
| Imágenes | `boctulus/simplerest-gd-image` | `Imaginator`, creación y composición de imágenes GD. | `ext-gd`. | Requiere la extensión GD de PHP. Véase [salida de imágenes y PDF](/packages/media-output). |
| Geolocalización | `boctulus/simplerest-geolocation` | `GoogleMaps::getCoordinates()` y `GeoLite2::getLocation()`. | Ninguna adicional al core. | Combina petición a Google Maps con consulta a tablas GeoLite2 que debe provisionar la aplicación. Véase [geolocalización](/integrations/geolocation). |
| Idiomas | `boctulus/simplerest-i18n` | `Translate`, `POParser`, `LangDetector` y helpers `setLang()`/`trans()`. | Ninguna adicional al core. | Los catálogos `.po` son datos de la aplicación; un adaptador alternativo requiere la interfaz `PHPTAL_TranslationService`. Véase [localización](/packages/localization). |
| Tareas | `boctulus/simplerest-jobs` | `BackgroundService`, `CronJob`, `CronJobMananger` (ortografía histórica) y `Task`. | Ninguna adicional al core. | No incluye cola ni scheduler; la aplicación decide cómo iniciar cada proceso. Véase [trabajos en segundo plano](/packages/background-jobs). |
| Autenticación | `boctulus/simplerest-jwt-auth` | `Core\Api\AuthController` para login, refresh, registro y tokens de email/contraseña. | `firebase/php-jwt ^7.1`. | La dependencia es la biblioteca JWT para PHP, no la plataforma Firebase. Véase [JWT](/security/jwt). |
| IA | `boctulus/simplerest-llm-providers` | Interfaz/factory para OpenAI, Anthropic, xAI y Ollama. | Ninguna adicional al core. | Requiere credenciales y red para proveedores remotos; el wiring de config tiene límites registrados. Véase [proveedores LLM](/integrations/llm-providers). |
| Correo | `boctulus/simplerest-mail` | Envío SMTP, correo remoto por WordPress, auditoría local y plantillas. | `phpmailer/phpmailer ^6.9 || ^7.1`. | Puede enviar mensajes o escribir archivos de auditoría. Véase [correo](/integrations/mail). |
| HTTP | `boctulus/simplerest-psr7` | Adaptadores PSR-7 de `Request`/`Response`, `Stream`, `Uri` y helpers `psr7_*()`. | `psr/http-message ^2.0`. | Adaptación opcional; no sustituye el ciclo HTTP del core. Véase [adaptadores PSR-7 y SSE](/packages/http-adapters). |
| Desarrollo | `boctulus/simplerest-phpparser` | `PHPParser`, transformaciones PHP respaldadas por `nikic/php-parser`. | `nikic/php-parser ^5.4`. | Conserva el FQCN histórico; revisar la salida de sus transformaciones. |
| Desarrollo | `boctulus/simplerest-postman-gen` | `PostmanGenerator`, genera colecciones Postman desde endpoints SimpleRest. | Ninguna adicional al core. | `generate()` escribe el archivo configurado. |
| Desarrollo | `boctulus/simplerest-scaffolding` | Grupo CLI `make` y generadores de archivos. | `simplerest-i18n ^1.0`. | Los comandos se habilitan expresamente en `config/commands.php`; los generadores escriben archivos. Véase [scaffolding](/packages/scaffolding) y el [catálogo CLI](/cli/commands). |
| Shopify | `boctulus/simplerest-shopify-connector` | `Shopify` y conversión de producto a WooCommerce. | Ninguna adicional al core. Sólo acepta core `^2.0`. | Conector opcional que usa configuración y red del proveedor. Véase [Shopify Connector](/integrations/shopify). |
| HTTP | `boctulus/simplerest-sse` | `SSE` para emitir Server-Sent Events. | No declara dependencia Composer del core ni de terceros. | La aplicación y el servidor deben permitir respuestas de streaming largas. Véase [adaptadores PSR-7 y SSE](/packages/http-adapters). |
| Inspección | `boctulus/simplerest-web-inspection` | Escáner de CMS/tecnologías y selectores HTML Symfony. | `ext-curl`, `symfony/css-selector ^6.4`, `symfony/dom-crawler ^6.4`. | Puede pedir una URL remota; el llamador debe restringir destinos. Véase [inspección web](/integrations/web-inspection). |

## Bundle histórico

`packages/boctulus/simplerest-integrations/` no tiene `composer.json` ni implementación PHP. Es un aviso de migración al separar clientes en paquetes Composer independientes. No se debe instalar ni tratar como un alias del catálogo actual.

## Guías por contrato

- [Generación y transformación HTML](/packages/html-content): builders HTML, helpers Bootstrap/AdminLTE y transformaciones de markup.
- [Salida de imágenes y PDF](/packages/media-output): APIs de imagen GD y adaptación FPDF, con extensiones y salidas distintas.
- [Adaptadores PSR-7 y SSE](/packages/http-adapters): adaptación de mensajes HTTP y transmisión por eventos, contratos separados dentro de la respuesta.
- [Localización](/packages/localization), [trabajos en segundo plano](/packages/background-jobs), [scaffolding](/packages/scaffolding) y [helpers heredados](/packages/legacy-helpers).
- Las integraciones que requieren red, credenciales o datos del host tienen guías propias en [Integraciones](/integrations).
