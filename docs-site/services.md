---
title: Servicios del framework
source:
  - docs/services/README.md
source_revision: 88a81da51b6c8c4c035180e3004023391205f616
status: partial
---

# Servicios del framework

Estas guías describen los servicios disponibles en este checkout y separan sus responsabilidades. La existencia de una API en el código no demuestra una instalación consumidora, una conexión de base de datos ni un recorrido completo en navegador.

## Referencias

- [Cache, transitorios y memoización](/services/cache): drivers, TTL, helpers y límites de almacenamiento.
- [EventBus y observadores](/services/event-bus): despacho síncrono, payload y última carga guardada.
- [Vistas y layouts PHP](/services/views): inclusión, layouts, assets, caché y escape de salida.
- [Traducciones y locales](/services/i18n): gettext, dominios, catálogos, fallback y detección de idioma.
- [Validación del servidor](/services/validation): `Validator`, reglas, errores y comprobaciones con base de datos.
- [HTML y formularios](/services/forms): builders de HTML y Bootstrap, sin enlace automático a validación.

El generador HTML produce marcado; `Validator` comprueba datos en PHP; los mensajes de validación usan el servicio i18n. Los hooks de `Model` tampoco son emisiones del [EventBus](/services/event-bus): son caminos de extensión separados del [ciclo de Model/ModelRecord](/database/model).
