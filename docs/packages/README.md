# Packages Locales — SimpleRest

El framework incluye **11 packages locales** en `packages/boctulus/`. Cada package sigue PSR-4 y puede publicarse independientemente vía Composer.

---

## Índice de Packages

| Package | README | Descripción |
|---------|--------|-------------|
| [`fine-grained-acl`](#fine-grained-acl) | ❌ | ACL granular con roles jerárquicos |
| [`basic-acl`](#basic-acl) | ❌ | ACL básico simplificado |
| [`zippy`](#zippy) | ✅ | Mapeo de categorías con LLM |
| [`cli-test`](#cli-test) | ✅ | Herramientas de testing CLI |
| [`web-test`](#web-test) | ✅ | Herramientas de testing web |
| [`llm-providers`](#llm-providers) | ✅ | Integración OpenAI, Claude, Ollama |
| [`exchange-rate`](#exchange-rate) | ❌ | API de tipo de cambio |
| [`friendlypos-web`](#friendlypos-web) | ✅ | POS web (Chile) |
| [`openfactura-sdk`](#openfactura-sdk) | ✅ | Factura electrónica Chile |
| [`shopifyconnector`](#shopifyconnector) | ❌ | Integración Shopify |
| [`dummyapi`](#dummyapi) | ✅ | API dummy para testing |

---

## Detalle por Package

### `fine-grained-acl`
Sistema ACL avanzado con:
- Roles jerárquicos (guest → registered → supervisor → admin → superadmin)
- Permisos especiales, de tabla, de fila y de atributo
- Reglas de denegación explícita con precedencia
- Overrides por usuario
- ACL por carpetas (Folder-based ACL)
- 24 comandos CLI de gestión

Ver: [`docs/ACL.md`](../ACL.md), [`docs/CHANGELOG-acl.md`](../CHANGELOG-acl.md)

### `basic-acl`
Versión simplificada de ACL. Sin herencia de roles ni permisos granulares.

### `zippy`
Sistema de mapeo de categorías de productos usando LLM (Ollama) y fuzzy matching.
Comandos: `php com zippy <namespace> <comando>`

Ver: [`docs/Zippy Commands.md`](../Zippy%20Commands.md)

### `cli-test`
Utilidades para testing de comandos CLI. Permite simular entrada/salida de consola.

### `web-test`
Utilidades para testing E2E web. Integración con Playwright/Puppeteer.

### `llm-providers`
Abstracción unificada para proveedores LLM:
- OpenAI (GPT-4, GPT-3.5)
- Anthropic Claude
- Ollama (modelos locales)
- Interfaz común independiente del proveedor

### `exchange-rate`
Consumidor de API de tipo de cambio. Tasas actualizadas vía API externa.

### `friendlypos-web`
Sistema POS web para Chile. Módulo completo con:
- Catálogo de productos
- Carrito de compras
- Generación de boletas
- Integración con OpenFactura

### `openfactura-sdk`
SDK para facturación electrónica chilena (SII):
- Generación de DTE (Documento Tributario Electrónico)
- Firma digital
- Envío al SII
- Estado de documentos

### `shopifyconnector`
Integración con Shopify API:
- Sincronización de productos
- Órdenes
- Inventario

### `dummyapi`
API dummy con datos de prueba. Útil para desarrollo y testing sin BD real.

---

## Cómo Crear un Package

```bash
php com make package <nombre> <autor>
```

Esto genera estructura completa con `composer.json`, `ServiceProvider`, tests, etc.

Ver: [`docs/Packages and Modules.md`](../Packages%20and%20Modules.md)

---

## Cómo Publicar

1. El package tiene su propio `composer.json` con PSR-4 autoload
2. Se registra en `config/config.php` via `providers` array
3. Se puede publicar en Packagist independientemente
