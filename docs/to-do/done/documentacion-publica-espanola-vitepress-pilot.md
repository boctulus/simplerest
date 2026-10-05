---
title: Piloto de documentación pública española con VitePress
current_step: Piloto compilado y gobernanza verificada
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: false
tags: [docs, vitepress, español, derivados]
---
## Pasos planificados

1. **Relevar tooling, corpus autorizado y límites del piloto** — confirmar package manager y lockfiles actuales, revisar el manifest y elegir una versión estable de VitePress.
2. **Montar estructura aislada de VitePress** — crear `docs-site/` con configuración española en `/`, navegación, scripts propios y dependencias sin modificar el tooling raíz.
3. **Curar las ocho páginas públicas con trazabilidad canónica** — home, Getting Started, arquitectura, ciclo de request, Database, HTTP/API, Security y Webhooks; cada página declara `source`, `source_revision` y `status` cuando aplique.
4. **Registrar trabajo técnico posterior** — mantener los gaps de Getting Started, CRUD, Database, CLI y Deployment como pendientes en las fuentes canónicas antes de propagarlos al derivado.
5. **Actualizar inventario y validar gobernanza** — clasificar el nuevo documento bajo `docs/`, comprobar exclusiones y ejecutar el guard documental.
6. **Compilar y revisar el piloto** — ejecutar el build de VitePress y resolver errores de enlaces o rutas del sitio.

## Trabajo posterior fuera del piloto

Los gaps de Getting Started, CRUD, Database, CLI y Deployment quedaron en la [tarea pendiente de documentación canónica](../gaps-tecnicos-documentacion-canonica.md). Para cada uno, la fuente canónica se corrige primero con evidencia y luego se actualiza la página pública derivada; mientras falte evidencia, el derivado permanece parcial.
