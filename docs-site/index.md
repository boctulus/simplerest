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
    details: Revisa los puntos de entrada, los routers y las ramas condicionales del front controller.
    link: /core/request-lifecycle
  - title: Explora las guías actuales
    details: Base de datos, HTTP/API, seguridad y webhooks, con límites visibles donde falta una reproducción completa.
    link: /database
source:
  - docs/index.md
  - docs/documentation-governance.md
source_revision: 1dff834829126b0c1c5aa60bd3ff13a75e7859f4
status: partial
---

## Sobre esta documentación

Este piloto publica una selección española curada de la fuente editorial canónica. Reordena el material para lectura y navegación; no sustituye `docs/` ni crea una fuente editorial independiente.

::: warning Estado del piloto
No es una publicación completa del framework. Cada guía indica qué está trazado y qué no se ha comprobado; una página parcial no debe leerse como un procedimiento listo para producción.
:::

## Recorrido recomendado

1. [Primeros pasos](/getting-started) explica el estado de las rutas de instalación disponibles.
2. [Arquitectura](/architecture) sitúa los componentes de entrada y despacho.
3. [Ciclo de request](/core/request-lifecycle) recorre las ramas HTTP y CLI que están trazadas.
4. Las guías de [Base de datos](/database), [HTTP/API](/api), [seguridad](/security) y [webhooks](/webhooks) detallan los contratos actuales y sus límites.

El español ocupa `/`. Los directorios `/en/`, `/it/` y `/pt/` quedan reservados para traducciones futuras; este piloto no las genera ni publica enlaces a ellas.
