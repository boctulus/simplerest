---
title: Documentación pública en inglés
current_step: 1
next_step: 1
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: true
next_step_complexity: null
tags: [docs, i18n, vitepress]
---
## Pasos planificados

1. **Habilitar el locale inglés** — Añadir `/en/`, navegación, sidebar, búsqueda y controles de interfaz en `docs-site/.vitepress/config.mts`.
2. **Crear la portada inglesa** — Traducir `docs-site/index.md` a `docs-site/en/index.md`, preservando la referencia editorial y el alcance parcial.
3. **Traducir API, lote 1** — Crear `en/api.md`, `en/api/automatic-endpoints.md` y `en/api/collections.md`.
4. **Traducir API, lote 2** — Crear `en/api/file-endpoints.md`, `en/api/resource-controllers.md` y `en/api/trashcan.md`.
5. **Traducir arquitectura y CLI** — Crear `en/architecture.md`, `en/cli/commands.md` y `en/cli/routing.md`.
6. **Traducir core, lote 1** — Crear `en/core/api-client.md`, `en/core/archives-backups.md` y `en/core/configuration-environment.md`.
7. **Traducir core, lote 2** — Crear `en/core/content-conversion.md`, `en/core/cors.md` y `en/core/dependency-container.md`.
8. **Traducir core, lote 3** — Crear `en/core/error-handling.md`, `en/core/file-uploads.md` y `en/core/filesystem.md`.
9. **Traducir core, lote 4** — Crear `en/core/handlers.md`, `en/core/logging.md` y `en/core/microcore.md`.
10. **Traducir core, lote 5** — Crear `en/core/middleware.md`, `en/core/request-lifecycle.md` y `en/core/request-response.md`.
11. **Traducir core, lote 6** — Crear `en/core/service-providers.md`, `en/core/sessions-cookies.md` y `en/core/web-routing.md`.
12. **Traducir base de datos, lote 1** — Crear `en/database.md`, `en/database/driver-compatibility.md` y `en/database/migrations.md`.
13. **Traducir base de datos, lote 2** — Crear `en/database/model.md`, `en/database/pagination.md` y `en/database/query-builder.md`.
14. **Traducir base de datos, lote 3** — Crear `en/database/raw-queries.md`, `en/database/relations.md` y `en/database/schema-builder.md`.
15. **Traducir base de datos, lote 4** — Crear `en/database/transactions.md`, `en/deployment.md` y `en/getting-started.md`.
16. **Traducir integraciones, lote 1** — Crear `en/integrations.md`, `en/integrations/api-clients.md` y `en/integrations/geolocation.md`.
17. **Traducir integraciones, lote 2** — Crear `en/integrations/llm-providers.md`, `en/integrations/mail.md` y `en/integrations/shopify.md`.
18. **Traducir integraciones y paquetes, lote 3** — Crear `en/integrations/web-inspection.md`, `en/packages/background-jobs.md` y `en/packages/catalog.md`.
19. **Traducir paquetes, lote 1** — Crear `en/packages/html-content.md`, `en/packages/http-adapters.md` y `en/packages/legacy-helpers.md`.
20. **Traducir paquetes, lote 2** — Crear `en/packages/localization.md`, `en/packages/media-output.md` y `en/packages/scaffolding.md`.
21. **Traducir paquetes y operación** — Crear `en/packages/test-data-seeder.md`, `en/performance.md` y `en/reference.md`.
22. **Traducir seguridad, lote 1** — Crear `en/security.md`, `en/security/acl-implementations.md` y `en/security/acl.md`.
23. **Traducir seguridad, lote 2** — Crear `en/security/authentication.md`, `en/security/authorization-engine.md` y `en/security/jwt.md`.
24. **Traducir servicios, lote 1** — Crear `en/services.md`, `en/services/cache.md` y `en/services/event-bus.md`.
25. **Traducir servicios, lote 2** — Crear `en/services/forms.md`, `en/services/i18n.md` y `en/services/validation.md`.
26. **Traducir servicios y webhooks** — Crear `en/services/views.md` y `en/webhooks.md`.
27. **Validar el sitio bilingüe** — Construir VitePress y comprobar cobertura de rutas, enlaces internos y metadatos `source`/`source_revision`.
28. **Actualizar inventario y gobernanza** — Registrar la tarjeta en `docs/audit/documentation-inventory.json` y ejecutar el guard documental.
29. **Publicar y comprobar** — Confirmar solo los cambios documentales previstos en `master`, publicar y verificar que `/en/` responde con la versión inglesa.

