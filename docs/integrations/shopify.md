# Shopify Connector

`boctulus/simplerest-shopify-connector` contiene `Boctulus\ShopifyConnector\Shopify` y `ShopifyWooCommerceAdaptorTrait`. Su manifiesto requiere PHP `>=8.1,<8.5` y `boctulus/simplerest ^2.0`; el proceso de distribución lo clasifica como paquete privado. La fuente está auditada, pero no hay tests de paquete ni una petición a Shopify ejecutada.

## Cliente `Shopify`

El constructor recibe `$shop`, `$api_token`, `$api_key`, `$api_secret` y `$api_ver`. Cuando todos están presentes, escribe datos bajo `Config::set('shopify', ...)`; las propiedades de instancia guardan tienda, key, secret y versión, pero no guardan `api_token`.

| Método | Comportamiento fuente |
| --- | --- |
| `getProducts($max_rows = null)` | Llama al endpoint REST `/admin/api/{version}/products.json` en páginas de hasta 100 y combina los productos. Llama a `set_time_limit(0)`. Si `max_rows` se alcanza, el límite se revisa entre páginas y puede devolver más filas que las solicitadas. El header `X-Shopify-Access-Token` recibe la propiedad `api_key`, no el argumento `api_token` del constructor. |
| `getCollectionsByProductId(...)` | Consulta `collects` y luego cada colección personalizada. Las credenciales aparecen embebidas en la URL que construye el método. |
| `getWebHook(...)` / `webHookExists(...)` | Busca topics por entidad y operación. La lista se mantiene en una variable `static` compartida y no está indexada por tienda; llamadas de varias tiendas en el mismo proceso pueden reutilizar datos previos. |
| `createWebhook(...)` | Comprueba que la base de callback local use HTTPS, crea un topic remoto y arma una URL callback fija bajo `/index.php/wp-json/connector/v1/shopify/webhooks/`. Puede crear un recurso externo; no lo ejecutes como lectura. |
| `deleteAllWebhooks()` | Intenta borrar únicamente los topics `products/create`, `products/update` y `products/delete`; no expone un resultado de cada eliminación. |

Las rutas usan una versión API recibida por el constructor; no se validó contra la versión admitida actualmente por Shopify. Los caminos que construyen URLs con usuario/clave pueden filtrar secretos a registros del transporte. No uses estos métodos como receta de producción sin revisar autenticación, destino y respuesta.

## Conversión a WooCommerce

`ShopifyWooCommerceAdaptorTrait::adaptToWooCommerce()` convierte campos de producto, tags, categorías, variantes y atributos al formato esperado por el adaptador WooCommerce. Consulta el [cliente WooCommerce](api-clients.md) para la transformación pura y sus llamadas externas.

Los conversores de estado se llaman `convertProductStatusFromShopifyToWooCommerce()` y `convertProductStatusFromWooCommerceToShopify()`. En el código actual, ambos comprueban `in_array($status, $arr)` sobre los valores, aunque las claves son los estados de origen; los valores conocidos pueden retornar sin conversión. Este comportamiento queda documentado como discrepancia de fuente y no como una conversión funcional comprobada.

## Evidencia y límites

Se inspeccionaron las dos clases y el manifiesto Composer. No hay suite focalizada bajo el paquete y no se ejecutaron peticiones, creaciones/eliminaciones de webhooks, sincronizaciones ni conversiones con fixtures. El README histórico del bundle `boctulus/simplerest-integrations` no instala este conector; revisa la [clasificación de paquetes](../architecture/composer-packages.md).
