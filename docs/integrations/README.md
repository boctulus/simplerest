# Integraciones y clientes externos

Esta sección separa los paquetes opcionales del core y describe lo que implementa este checkout. La presencia de una carpeta no acredita disponibilidad en Packagist, compatibilidad actual con un proveedor ni una petición externa exitosa. Revisa la decisión de distribución en [Límites de paquetes Composer](../architecture/composer-packages.md) y el estado de release en [Proceso de publicación](../architecture/composer-release-process.md).

## Referencias auditadas

| Integración | Propietario | Alcance de esta referencia |
| --- | --- | --- |
| [API Clients](api-clients.md) | `boctulus/simplerest-api-clients` | Google Drive, WooCommerce, OneSignal, Hugging Face y adaptadores heredados de WordPress, Pastebin y SendinBlue; fuente y tests inspeccionados, sin llamadas externas. |
| [Shopify Connector](shopify.md) | `boctulus/simplerest-shopify-connector` | Lectura de productos, operaciones de webhooks y conversión hacia WooCommerce; fuente inspeccionada, sin test ni petición al proveedor. |

## Paquetes por revisar

El checkout también contiene paquetes de proveedores LLM, geolocalización, correo, inspección web, PSR-7 y trabajos programados. Sus contratos, dependencias y estado de distribución se incorporarán cuando se auditen por separado; no se infieren del nombre del paquete.

`boctulus/simplerest-integrations` es un aviso de migración para el bundle histórico, no un paquete Composer instalable en este árbol. Sus sucesores tienen manifiestos y APIs independientes.
