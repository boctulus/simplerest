---
title: Integraciones
source:
  - docs/integrations/README.md
source_revision: 379f2383edbe4978e934195d6a6ed4102651d154
status: partial
---

# Integraciones

Estas guías describen el código que hay en este checkout. La presencia de un paquete no confirma su publicación, compatibilidad vigente con un proveedor ni una llamada externa exitosa. Para los límites de distribución del core y sus extensiones, consulta [Microcore y paquetes](/core/microcore).

## Referencias auditadas

- [Proveedores LLM](/integrations/llm-providers): OpenAI, Anthropic, xAI y Ollama; configuración y dispatch contrastados con el código, sin llamadas externas.
- [Shopify Connector](/integrations/shopify): lectura de productos, webhooks y conversión a WooCommerce. La guía expone límites de autenticación y de las operaciones remotas.
- [Geolocalización](/integrations/geolocation): cliente Google Maps y consulta local a tablas GeoLite2. No se usaron credenciales, red ni base de datos.
- [Correo](/integrations/mail): SMTP, entrega por WordPress remoto y auditoría JSON. No se envió correo ni se escribieron archivos.
- [Inspección web](/integrations/web-inspection): detector de tecnologías con redirecciones y selectores DOM locales. No se solicitaron URLs externas.

El antiguo `boctulus/simplerest-integrations` es un aviso de migración del bundle histórico; no contiene un paquete Composer instalable en este árbol. Sus sucesores tienen APIs y dependencias separadas.

El [catálogo de paquetes](/packages/catalog) cubre además los paquetes locales de HTTP, generación de contenido, medios, tareas, localización y desarrollo. El manifiesto describe código y dependencias, no publicación o instalación en consumidores.
