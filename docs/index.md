# Documentación de SimpleRest

SimpleRest es un framework PHP orientado a APIs REST y aplicaciones basadas en datos. Este índice reúne sus guías de instalación, recursos HTTP, datos, seguridad, servicios y extensiones. El español es el idioma base de publicación.

Algunas guías todavía están en inglés. El [registro de gobernanza](documentation-governance.md) explica cómo se mantienen las fuentes y las traducciones.

## Empieza aquí

- [Gobernanza de la documentación](documentation-governance.md) — autoridad editorial, política de cambios localizados y relación entre fuente canónica, publicación y traducciones.
- [Arquitectura](architecture.md) — entrada de la aplicación, bootstrap y relación entre los routers y el front controller.
- [Primeros pasos](getting-started/README.md) — instalación, configuración básica y primer endpoint.

## Documentación por tema

| Tema | Páginas actuales | Alcance y límites |
| --- | --- | --- |
| Núcleo | [Core](core/README.md), [ciclo HTTP](core/request-lifecycle.md), [Routing HTTP](core/web-routing.md), [Request y Response](core/request-response.md), [middleware](core/middleware.md), [handlers](core/handlers.md), [configuración y entorno](core/configuration-environment.md), [providers](core/service-providers.md), [Container](core/dependency-container.md), [CORS](core/cors.md), [sesiones y cookies](core/sessions-cookies.md), [errores](core/error-handling.md), [logging](core/logging.md), [filesystem](core/filesystem.md), [uploads](core/file-uploads.md), [ZIP y backups](core/archives-backups.md), [HTML/XML e imágenes](core/content-conversion.md), [ApiClient](core/api-client.md) | Router HTTP, ciclo de peticiones, configuración, manejo de archivos, servicios básicos y consumo de APIs externas. |
| Base de datos | [Base de datos](database/README.md), [conexiones](database/connections.md), [Query Builder](database/query-builder.md), [paginación](database/pagination.md), [Model y registros](database/model.md), [relaciones y subrecursos](database/relations.md), [SQL raw](database/raw-queries.md), [transacciones](database/transactions.md), [compatibilidad de drivers](database/driver-compatibility.md), [descriptores de esquema](database/schemas.md), [constructor DDL](database/schema-builder.md), [migraciones](database/migrations.md) | Conexiones, modelos, Query Builder, SQL, esquemas, relaciones, transacciones y migraciones. Cada guía indica sus requisitos y límites específicos. |
| HTTP y API | [API HTTP](api/README.md), [resolución de recursos](api/automatic-endpoints.md), [controladores de recursos](api/resource-controllers.md), [Collections](api/collections.md), [TrashCan](api/trashcan.md), [endpoints de archivos](api/file-endpoints.md), [publicación de webhooks](api/webhooks.md) | Controladores REST, filtros y agregaciones, operaciones de colecciones, archivos y webhooks. |
| Seguridad | [Autenticación](security/authentication.md), [JWT](security/jwt.md), [ACL](security/acl.md), [implementaciones ACL](security/acl-implementations.md), [motor de autorización](security/authorization-engine.md) | Autenticación, JWT, motores ACL, permisos por recurso y por usuario, y paquetes opcionales de autorización. |
| Servicios | [Índice de servicios](services/README.md), [cache](services/cache.md), [EventBus](services/event-bus.md), [vistas](services/views.md), [i18n](services/i18n.md), [validación](services/validation.md), [HTML y formularios](services/forms.md) | Caché, eventos, renderizado, validación, internacionalización y generación de formularios. |
| CLI | [CLI](cli/README.md), [catálogo `php com`](cli/commands.md), [routing CLI](cli/routing.md) | Herramientas de línea de comandos, enrutamiento CLI y comandos de mantenimiento. |
| Composer y paquetes | [Catálogo de paquetes](packages/catalog.md), [paquetes extraídos del core](packages/README.md), [límites seleccionados](architecture/composer-packages.md), [proceso de publicación](architecture/composer-release-process.md) | Paquetes Composer, modularización, dependencias opcionales y proceso de distribución. |
| Integraciones | [Índice de integraciones](integrations/README.md), [API Clients](integrations/api-clients.md), [Shopify Connector](integrations/shopify.md), [proveedores LLM](integrations/llm-providers.md), [correo](integrations/mail.md), [geolocalización](integrations/geolocation.md), [inspección web](integrations/web-inspection.md) | Clientes HTTP e integraciones con servicios externos, correo, geolocalización y paquetes opcionales. |
| Despliegue | [Guía de despliegue](deployment/README.md) | Opciones y requisitos de despliegue. |
| Rendimiento | [Guía de medición](performance/README.md) | Herramientas y metodología para medir rendimiento. |
| Referencia | [Referencia pública](reference/README.md) | Configuración, helpers, clases, interfaces y componentes disponibles. |

## Cómo utilizar estas guías

Las páginas describen las APIs y los requisitos concretos de cada componente. Las diferencias entre versiones, configuraciones y paquetes se explican en sus secciones correspondientes. Consulta [Gobernanza de la documentación](documentation-governance.md) para los criterios de actualización editorial.

## Referencias internas

Las auditorías y el material histórico se mantienen separados de estas guías en `docs/audit/` y `docs/_internal/`.
