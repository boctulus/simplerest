---
layout: home
title: Documentación en español
titleTemplate: SimpleRest
hero:
  name: SimpleRest
  text: Framework PHP para APIs REST basadas en datos
  tagline: Recursos REST, consultas dinámicas, esquemas, autorización y servicios backend reutilizables.
  actions:
    - theme: brand
      text: Empezar
      link: /getting-started
    - theme: alt
      text: Explorar la API
      link: /api/automatic-endpoints
features:
  - title: API REST basada en datos
    details: Generación de esquemas, modelos y controladores de recursos, con filtros, ordenamiento y agregaciones.
    link: /api/automatic-endpoints
  - title: Datos y consultas
    details: Query Builder, relaciones, migraciones, transacciones y conexiones configurables.
    link: /database
  - title: Autenticación y permisos
    details: Arquitectura ACL, autorización de operaciones y autenticación mediante componentes configurables.
    link: /security
source:
  - docs/index.md
  - docs/documentation-governance.md
source_revision: 5e6771296ae27de7352f776bbc822786ac919835
---

## Guías para desarrolladores

Comienza por [instalar SimpleRest](/getting-started) y publicar un primer endpoint. Después puedes explorar [consultas REST](/api/automatic-endpoints), [Query Builder](/database/query-builder), [migraciones](/database/migrations), [ACL](/security/acl) y [ApiClient](/core/api-client).

SimpleRest permite extender el backend mediante módulos y paquetes Composer. Consulta [microcore y paquetes](/core/microcore) para conocer la organización y las dependencias disponibles.

## Sigue explorando

Para entender el recorrido HTTP, consulta [Arquitectura](/architecture), [Ciclo de request](/core/request-lifecycle), [Request y Response](/core/request-response) y [WebRouter](/core/web-routing). [Microcore y paquetes](/core/microcore) explica qué incluye el core y el [catálogo de paquetes](/packages/catalog) enumera las extensiones disponibles.

Las guías de [Base de datos](/database), [Query Builder](/database/query-builder), [paginación](/database/pagination), [Model/ModelRecord](/database/model), [constructor DDL](/database/schema-builder) y [migraciones](/database/migrations) cubren el acceso y la estructura de datos. Para HTTP y servicios, consulta [endpoints REST automáticos](/api/automatic-endpoints), [ApiClient](/core/api-client), [webhooks](/webhooks), [autenticación](/security/authentication) y [ACL](/security/acl).

También hay guías de [Shopify Connector](/integrations/shopify), [proveedores LLM](/integrations/llm-providers), [correo](/integrations/mail), [geolocalización](/integrations/geolocation) e [inspección web](/integrations/web-inspection). El [catálogo CLI](/cli/commands), [routing CLI](/cli/routing), [despliegue](/deployment), [rendimiento](/performance) y la [referencia pública](/reference) reúnen otras tareas de desarrollo y operación.

El español es el locale raíz (`/`). Se añadirán otros locales cuando existan traducciones revisadas.
