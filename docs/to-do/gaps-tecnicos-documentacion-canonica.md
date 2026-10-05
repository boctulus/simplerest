---
title: Cerrar gaps técnicos de la documentación canónica
current_step: "Getting Started: create-project v1.0.3 verificado; checkout actual bloqueado y documentado"
next_step: Cerrar el flujo CRUD automático con una petición HTTP completa
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: false
next_step_complexity: high
tags: [docs, canonical, getting-started, crud, database, cli, deployment]
---
## Pasos planificados

1. **Getting Started** — reproducir instalación limpia, bootstrap de la aplicación y primer request; corregir el procedimiento primero en su fuente canónica.
2. **CRUD** — auditar asignación de parámetros, serialización, validación y flujo HTTP CRUD automático; actualizar su fuente canónica con contratos reproducidos.
3. **Database** — verificar escrituras, transacciones, modos de ejecución, consultas raw, paginación, agregados, relaciones, DDL, migraciones, aislamiento y compatibilidad por driver.
4. **CLI** — descubrir los comandos públicos, verificar su comportamiento y reproducir los ejemplos en la fuente canónica.
5. **Deployment** — verificar un procedimiento de despliegue reproducible y documentarlo en la fuente canónica.

## Regla de propagación

Usar sólo fuentes autorizadas por `docs/canonical-manifest.json`. Al cerrar cada gap, actualizar primero la fuente canónica con evidencia; después propagar el cambio a la página correspondiente de `docs-site/` y renovar `source_revision`. Mantener `status: partial` mientras falte evidencia.

## Avance Getting Started (2026-10-05)

- `composer create-project boctulus/simplerest ... 1.0.3` completó instalación y bootstrap en una carpeta temporal. `GET /system/health` devolvió HTTP 200 con `{"ok":true}` usando PHP 8.3.15 y Composer 2.8.5.
- El archivo limpio del checkout actual instaló dependencias con `composer install`, pero no creó `.env`. El helper Composer lo creó al invocarlo; el request se detuvo porque el archivo rastreado no contiene `DumbController`, requerido por `config/routes.php`.
- Los hallazgos quedaron primero en `docs/getting-started/README.md` y luego en `docs-site/getting-started.md`. No se trabajó en CRUD ni se cambió la navegación del sitio.
