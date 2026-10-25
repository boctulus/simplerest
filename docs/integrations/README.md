# Integraciones y clientes externos

Esta sección separa los paquetes opcionales del core y describe lo que implementa este checkout. La presencia de una carpeta no acredita disponibilidad en Packagist, compatibilidad actual con un proveedor ni una petición externa exitosa. Revisa la decisión de distribución en [Límites de paquetes Composer](../architecture/composer-packages.md) y el estado de release en [Proceso de publicación](../architecture/composer-release-process.md).

## Referencias auditadas

| Integración | Propietario | Alcance de esta referencia |
| --- | --- | --- |
| [API Clients](api-clients.md) | `boctulus/simplerest-api-clients` | Google Drive, WooCommerce, OneSignal, Hugging Face y adaptadores heredados de Pastebin y SendinBlue; fuente y tests inspeccionados, sin llamadas externas. |
| [Proveedores LLM](llm-providers.md) | `boctulus/simplerest-llm-providers` | OpenAI, Anthropic, xAI y Ollama; configuración, factory y dispatch contrastados con fuente, sin tests ejecutados ni llamadas externas. |
| [Shopify Connector](shopify.md) | `boctulus/simplerest-shopify-connector` | Lectura de productos, operaciones de webhooks y conversión hacia WooCommerce; fuente inspeccionada, sin test ni petición al proveedor. |
| [Geolocalización](geolocation.md) | `boctulus/simplerest-geolocation` | Google Maps remoto y búsqueda local en tablas GeoLite2; no se usaron credenciales, red ni base de datos. |
| [Correo](mail.md) | `boctulus/simplerest-mail` | SMTP, entrega por WordPress remoto y auditoría JSON local; no se envió correo ni se escribieron archivos. |
| [Inspección web](web-inspection.md) | `boctulus/simplerest-web-inspection` | Detección de CMS/tecnologías y consulta DOM; no se solicitó ninguna URL externa. |

## Otros paquetes locales

PSR-7, SSE, generadores HTML, procesamiento de contenido, medios, i18n, trabajos, scaffolding, seeder y helpers de host tienen sus referencias en el [catálogo de paquetes](../packages/catalog.md). Ese inventario describe manifiestos y límites; no implica instalación o prueba de los paquetes.

`boctulus/simplerest-integrations` es un aviso de migración para el bundle histórico, no un paquete Composer instalable en este árbol. Sus sucesores tienen manifiestos y APIs independientes.
