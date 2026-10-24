---
title: Integraciones
source:
  - docs/integrations/README.md
source_revision: a052e20aa20c0ec1f4289cf7f6edf8856125b889
status: partial
---

# Integraciones

Estas guías describen el código que hay en este checkout. La presencia de un paquete no confirma su publicación, compatibilidad vigente con un proveedor ni una llamada externa exitosa. Para los límites de distribución del core y sus extensiones, consulta [Microcore y paquetes](/core/microcore).

## Referencias auditadas

- [API Clients](/integrations/api-clients): Google Drive, WooCommerce, OneSignal, Hugging Face y adaptadores heredados de WordPress, Pastebin y SendinBlue. Se inspeccionó la fuente; no se llamaron proveedores.
- [Proveedores LLM](/integrations/llm-providers): OpenAI, Anthropic, xAI y Ollama; configuración y dispatch contrastados con el código, sin llamadas externas.
- [Shopify Connector](/integrations/shopify): lectura de productos, webhooks y conversión a WooCommerce. La guía expone límites de autenticación y de las operaciones remotas.

El antiguo `boctulus/simplerest-integrations` es un aviso de migración del bundle histórico; no contiene un paquete Composer instalable en este árbol. Sus sucesores tienen APIs y dependencias separadas.

Otros paquetes de geolocalización, correo e inspección web siguen en auditoría por separado.
