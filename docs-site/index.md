---
layout: home
title: Documentación en español
titleTemplate: SimpleRest
hero:
  name: SimpleRest
  text: Documentación pública
  tagline: Un recorrido curado por las capacidades documentadas del framework.
  actions:
    - theme: brand
      text: Empezar
      link: /getting-started
    - theme: alt
      text: Ver arquitectura
      link: /architecture
features:
  - title: Empieza con contexto
    details: Consulta qué partes de instalación y arranque están comprobadas antes de seguir instrucciones.
    link: /getting-started
  - title: Sigue el recorrido de una petición
    details: Revisa la entrada HTTP, el traspaso al FrontController y sus ramas condicionales.
    link: /core/request-lifecycle
  - title: Explora las guías actuales
    details: Datos, HTTP/API, seguridad, servicios y operación, con límites visibles donde falta una reproducción completa.
    link: /database
source:
  - docs/index.md
  - docs/documentation-governance.md
source_revision: 88a81da51b6c8c4c035180e3004023391205f616
status: partial
---

## Sobre esta documentación

Esta primera edición pública ofrece una selección española curada de la fuente editorial canónica. Reordena el material para lectura y navegación; no sustituye `docs/` ni crea una fuente editorial independiente.

::: warning Cobertura en desarrollo
La documentación todavía no cubre todas las capacidades del framework. Cada guía distingue lo verificado de lo pendiente; las secciones marcadas como parciales no deben tratarse como procedimientos validados para producción.
:::

## Recorrido recomendado

1. [Primeros pasos](/getting-started) explica el estado de las rutas de instalación disponibles.
2. [Arquitectura](/architecture) sitúa los componentes de entrada y despacho.
3. [Ciclo de request](/core/request-lifecycle) recorre la ruta HTTP; [Request y Response](/core/request-response) describe los mensajes y sus helpers; [WebRouter](/core/web-routing) detalla el registro HTTP.
4. [Microcore y paquetes](/core/microcore) explica qué incluye el core y qué librerías se instalan como paquetes.
5. Las guías de [Base de datos](/database), [Query Builder](/database/query-builder), [Model/ModelRecord](/database/model), [constructor DDL](/database/schema-builder), [migraciones](/database/migrations), [ApiClient](/core/api-client), [HTTP/API](/api), [endpoints automáticos](/api/automatic-endpoints), [autenticación](/security/authentication), [ACL](/security/acl) y [webhooks](/webhooks) detallan los contratos actuales y sus límites.
6. CLI tiene dos guías independientes: el [catálogo de `php com`](/cli/commands) y el [routing CLI con CliRouter](/cli/routing).

El español ocupa `/`. Los directorios `/en/`, `/it/` y `/pt/` quedan reservados para traducciones futuras; esta edición no las genera ni publica enlaces a ellas.
