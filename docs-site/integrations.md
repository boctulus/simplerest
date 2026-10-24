---
title: Integraciones
source:
  - docs/integrations/README.md
source_revision: ee8eddfddb1a40de0d021cf6d66f3300f5bfda60
status: partial
---

# Integraciones

Estas guías describen el código que hay en este checkout. La presencia de un paquete no confirma su publicación, compatibilidad vigente con un proveedor ni una llamada externa exitosa. Para los límites de distribución del core y sus extensiones, consulta [Microcore y paquetes](/core/microcore).

## Referencias auditadas

- [API Clients](/integrations/api-clients): Google Drive, WooCommerce, OneSignal, Hugging Face y adaptadores heredados de WordPress, Pastebin y SendinBlue. Se inspeccionó la fuente; no se llamaron proveedores.
- [Shopify Connector](/integrations/shopify): lectura de productos, webhooks y conversión a WooCommerce. La guía expone límites de autenticación y de las operaciones remotas.

El antiguo `boctulus/simplerest-integrations` es un aviso de migración del bundle histórico; no contiene un paquete Composer instalable en este árbol. Sus sucesores tienen APIs y dependencias separadas.

Otros paquetes de proveedores LLM, geolocalización, correo e inspección web siguen en auditoría por separado.
