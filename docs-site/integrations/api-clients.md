---
title: API Clients
source:
  - docs/integrations/api-clients.md
source_revision: ee8eddfddb1a40de0d021cf6d66f3300f5bfda60
status: partial
---

# API Clients

`boctulus/simplerest-api-clients` agrupa adaptadores opcionales fuera del core. Sus clientes PSR-4 usan `Boctulus\Simplerest\Integrations\Libs`. El manifiesto requiere `ext-curl`, SimpleRest y el paquete de correo; Google API Client es opcional para Google Drive. La guía describe código fuente, no contratos vigentes de las APIs remotas.

| Cliente | Uso presente en el código | Configuración y límites relevantes |
| --- | --- | --- |
| `GoogleDrive` | `getFolderInfo()`, `getInfo()`, `getUpdateDate()` y `download()` usan el SDK de Drive. | Clave `google_console_api_key` o clave explícita. El SDK es opcional. Ciertas configuraciones `local`/`debug` pueden desactivar la verificación TLS. `download()` escribe archivos de destino y progreso, y puede dormir varios minutos. |
| `HuggingFace` | `addInput()`, `setParams()` y `exec()` preparan una inferencia para uno de tres modelos codificados. | Usa una clave explícita o `huggingface_api_key`, que no viene en la configuración predeterminada. `api_version` no se usa al formar la URL. Habilita caché POST en `ApiClient`, pero no define un TTL. |
| `OneSignal` | `send()`, `addDevice()`, `app()`, `getUsers()` y `getNotifications()` usan rutas fijas de OneSignal. | Lee `app_rest_api_key`, ausente de la configuración predeterminada. `send([])` retorna `null`; el código de `getUsers()` reemplaza `limit`/`offset` por `50`/`0`. La notificación tiene como segmento predeterminado `All`. |
| `WooCommerce` | `transformProductData()` transforma datos sin red; `createProduct()` y `createVariation()` envían POST. | Requiere `base_url`, `consumer_key` y `consumer_secret`; construye firma OAuth HMAC-SHA1. La transformación aparece en un test de paquete inspeccionado, no ejecutado. |
| `WP` | `get_option()` y `set_option()` acceden a la tabla `options` de WordPress mediante la conexión SimpleRest. | No es un cliente HTTP. `get_option()` usa `unserialize()`; la llamada de actualización de `set_option()` no se verificó contra una base real. |
| `Pastebin` | `getLink()` intenta publicar en dos servicios y ofrece los métodos individuales. | Usa cURL directo y `http://`, sin `ApiClient`. El contenido se envía a servicios externos y no tiene privacidad demostrada. |
| `SendinBlue` | `send()` entrega correo transaccional con el SDK legado. | Usa `sendinblue_api_key`, el paquete de correo, Guzzle y clases del SDK que el manifiesto no declara. No se envió un correo. |

Los métodos de red pueden tener efectos externos. No se ejecutó ninguna llamada, descarga, escritura en WordPress ni envío de correo durante esta auditoría.

## Nombres de clase

La clase nueva usa el namespace PSR-4 `Boctulus\Simplerest\Integrations\Libs`; `WP`, `Pastebin` y `SendinBlue` se encuentran en el directorio classmap legado. Los tests del paquete enumeran nombres anteriores bajo `Core\Libs` para otros clientes, pero el autoload de esta instalación consumidora no se verificó. No asumas esos nombres hasta validar una instalación limpia. Revisa los [límites de paquetes](/core/microcore) antes de instalar el bundle histórico.
