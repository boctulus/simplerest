# API Clients opcionales

`boctulus/simplerest-api-clients` agrupa adaptadores externos fuera del core. Los clientes PSR-4 están bajo `Boctulus\Simplerest\Integrations\Libs`; el paquete requiere PHP `>=8.1,<8.5`, `ext-curl`, `boctulus/simplerest ^2.0 || ^3.0` y `boctulus/simplerest-mail ^1.0`. `google/apiclient` es opcional para Google Drive; `guzzlehttp/guzzle` aparece como dependencia sugerida para ciertos caminos. El README del paquete describe una instalación por VCS `dev-main`; el estado de registro/tag no se verificó en un consumidor limpio.

## Adaptadores

| Clase | Métodos y entrada observados | Dependencias, configuración y efectos |
| --- | --- | --- |
| `GoogleDrive` | `getFolderInfo()`, `getInfo()`, `getUpdateDate()` y `download()` consultan metadatos o descargan archivos mediante el SDK de Google. | Requiere `google/apiclient` y clases del servicio Drive; usa `google_console_api_key` de configuración o una clave explícita. El código puede desactivar la verificación TLS en configuración `local`/`debug` o si `ssl_cert` vale `false`. `download()` escribe al destino, genera archivos de progreso y puede dormir durante varios minutos tras ciertos conteos. No lo trates como una descarga HTTP simple ni uses esos ajustes TLS en producción. |
| `HuggingFace` | `addInput()`, `setParams()` y `exec($model)` preparan y envían una inferencia; la lista codificada contiene `distilbert-base-uncased`, `roberta-base` y `gpt-neo-2.7B`. | Toma la clave del constructor o de `huggingface_api_key`, clave que no aparece en el `config/config.php` predeterminado. `api_version` se guarda, pero no se usa para formar la URL. Habilita caché POST en `ApiClient`, pero no configura por sí mismo un TTL. El cliente hace una llamada externa; no se ejecutó. |
| `OneSignal` | `send()`, `addDevice()`, `app()`, `getUsers()` y `getNotifications()` llaman endpoints fijos usando `ApiClient`. | Lee `app_rest_api_key`, que no está en la configuración predeterminada, y recibe `app_id` en la entrada. `send()` usa el segmento `All` por defecto; `send([])` retorna `null`. `getUsers()` reasigna internamente `limit` y `offset` a `50` y `0`, por lo que ignora los argumentos recibidos. Son operaciones externas; no se enviaron notificaciones. |
| `WooCommerce` / `WooCommerceApiClient` | El mapper `transformProductData()` convierte nombres, categorías y atributos sin HTTP; `createProduct()` y `createVariation()` envían POST a rutas `/wp-json/wc/v3/...`. | Requiere `base_url`, `consumer_key` y `consumer_secret`; `setOAuth()` construye una firma HMAC-SHA1 con la URL y el verbo ya seleccionados. El test del paquete inspecciona una transformación offline; no se ejecutó ni se validó una tienda. |
| `WP` | `get_option()` consulta `options` y deserializa `option_value`; `set_option()` invoca una actualización, con conexión opcional por `conn_id`. | Usa la base de datos configurada y `unserialize()`. La llamada actual a `update()` pasa una lista posicional; no se comprobó que asigne el valor a `option_value`. No es un cliente HTTP de WordPress y no se ejecutó sobre una base real. |
| `Pastebin` | `getLink()` intenta publicar en `sprunge.us` y luego `ix.io`; también expone ambos métodos por separado. | Usa cURL directo y URLs `http://`; no pasa por `ApiClient`. Publica el texto a servicios externos sin demostrar TLS ni privacidad. No envíes secretos o información personal mediante este adaptador. |
| `SendinBlue` | `SendinBlue::send()` implementa `IMail` y entrega correo transaccional. | Lee `sendinblue_api_key` y remitente de la configuración de correo; necesita las clases del SDK SendinBlue y Guzzle, además del paquete de correo. El manifiesto no declara el paquete del SDK; la instalación que aporta esas clases no se verificó. La condición actual se registra como legado; no se envió correo. |

Los métodos de red realizan efectos externos y dependen de credenciales, límites y contratos del proveedor. Esta guía describe la implementación del repositorio, no la vigencia de las APIs remotas.

## Namespace y compatibilidad

Usa el namespace PSR-4 actual `Boctulus\Simplerest\Integrations\Libs` cuando el paquete está instalado. `WP`, `Pastebin` y `SendinBlue` siguen en el directorio classmap `LegacyCoreLibs`. Para Google Drive, Hugging Face, OneSignal y WooCommerce, los tests del paquete y el verificador de distribución enumeran también nombres `Boctulus\Simplerest\Core\Libs`, pero el manifiesto y los archivos de alias revisados no demuestran que esos nombres carguen. El estado queda registrado en el [hallazgo de autoload](../audit/README.md#integration-package-autoload-status-2026-10-24); no se ejecutaron esos tests ni una instalación consumidora.

El directorio `packages/boctulus/simplerest-integrations` sólo contiene un aviso de migración, sin código PHP ni `composer.json`. Consulta el [catálogo de integración](README.md) y las [decisiones de distribución](../architecture/composer-packages.md) antes de inferir qué paquete instalar.

## Evidencia y límites

Se inspeccionaron los manifiestos, README, adaptadores y `ApiClientsPackageTest.php`. Las pruebas cubren nombres de clase, transformación de productos, payload vacío de OneSignal, lista de modelos de Hugging Face y detección de SDK opcional; no se ejecutaron y no prueban redes de proveedores. No se hicieron llamadas HTTP, escrituras a WordPress, descargas ni envíos de correo.
