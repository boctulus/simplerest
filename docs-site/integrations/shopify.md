---
title: Shopify Connector
source:
  - docs/integrations/shopify.md
source_revision: ee8eddfddb1a40de0d021cf6d66f3300f5bfda60
status: partial
---

# Shopify Connector

El paquete contiene `Boctulus\ShopifyConnector\Shopify` y `ShopifyWooCommerceAdaptorTrait`. La versión API se pasa al constructor; no se comprobó contra las versiones vigentes de Shopify. La matriz de distribución del checkout lo trata como extensión privada.

## Cliente `Shopify`

| Método | Comportamiento fuente y límite |
| --- | --- |
| `getProducts($max_rows)` | Lee páginas de hasta 100 productos y desactiva el timeout de PHP con `set_time_limit(0)`. El límite de filas se revisa entre páginas. El header de acceso recibe la propiedad `api_key`, aunque el constructor recibe también `$api_token`. |
| `getCollectionsByProductId(...)` | Consulta las colecciones de un producto; las credenciales aparecen dentro de la URL construida. |
| `getWebHook()` y `webHookExists()` | Buscan un topic. La caché estática de webhooks no está separada por tienda. |
| `createWebhook()` | Crea un webhook remoto y requiere que la URL base de callback use HTTPS. El callback usa una ruta fija bajo `/index.php/wp-json/connector/v1/shopify/webhooks/`. |
| `deleteAllWebhooks()` | Recorre sólo `products/create`, `products/update` y `products/delete`; no devuelve el resultado de cada operación. |

Crear o eliminar webhooks modifica la tienda externa. No se ejecutó ninguna operación remota.

## Conversión a WooCommerce

`adaptToWooCommerce()` transforma campos, etiquetas, categorías, variantes y atributos. Los conversores de estado comparan el estado de entrada contra los valores del mapa en vez de sus claves; por eso la conversión de estados conocidos no está demostrada como funcional.

No hay tests de paquete ni conversiones con fixtures ejecutadas en esta revisión. Revisa los [límites de distribución Composer](/core/microcore) antes de asumir que el paquete está disponible como dependencia pública.
