# Servicios de aplicación

Estas referencias separan los servicios del framework que operan sobre datos, eventos, HTML y locales. Cada página describe la API presente en el checkout y declara si se inspeccionó fuente, tests o ejecución.

## Referencias

- [Cache, transitorios y memoización](cache.md) — drivers disponibles, TTL, helpers globales y diferencias entre almacenamiento en archivo, base de datos y memoria.
- [EventBus y observadores](event-bus.md) — registro síncrono, payload, persistencia de la última carga y diferencia respecto de los hooks de `Model`.
- [Vistas y layouts PHP](views.md) — inclusión de vistas, layouts, assets, caché por ruta y límites de escape.
- [Traducciones y locales](i18n.md) — paquete gettext, dominios, archivos `.po`, fallback, locale y detección heurística.
- [Validación de datos en el servidor](validation.md) — `Validator`, `ValidationRules`, reglas por campo y alcance de base de datos.
- [HTML y formularios](forms.md) — builders de HTML/Bootstrap y límites de validación, assets y distribución Composer.

## Separación de responsabilidades

El generador HTML produce marcado y no enlaza estado de formulario ni validación. `Validator` comprueba datos en PHP; sus mensajes pasan por el servicio i18n. El `EventBus` tampoco emite automáticamente los hooks de ciclo de vida de `Model`, que se describen en la [referencia de Model/ModelRecord](../database/model.md).

La evidencia de una API en fuente no acredita una instalación standalone, una conexión de base de datos ni un recorrido de navegador. Las páginas conservan esos límites para cada servicio.
