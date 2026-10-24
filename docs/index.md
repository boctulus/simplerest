# Documentación de SimpleRest

La **fuente editorial canónica** conserva hechos, intención de diseño, decisiones y contexto bajo autoridad del maintainer. La documentación pública en español es una representación derivada de esa fuente y será el idioma predeterminado del sitio. Las traducciones bajo rutas como `/en/` e `/it/` derivarán del mismo corpus canónico. Si una versión derivada difiere de la fuente canónica, prevalece la fuente canónica.

La migración es progresiva. Algunas páginas auditadas siguen en inglés y se enlazan aquí por su contenido vigente; se revisarán al incorporarlas al modelo definitivo. Las páginas antiguas no enlazadas desde este mapa son material de auditoría, no especificaciones públicas actuales.

## Empieza aquí

- [Gobernanza de la documentación](documentation-governance.md) — autoridad editorial, política de cambios localizados y relación entre fuente canónica, publicación y traducciones.
- [Arquitectura](architecture.md) — entrada de la aplicación, bootstrap y relación entre los routers y el front controller.
- [Primeros pasos](getting-started/README.md) — estado comprobado de instalación y bootstrap. Todavía no es un tutorial de instalación reproducido de principio a fin.

## Documentación por tema

| Tema | Páginas actuales | Alcance y límites |
| --- | --- | --- |
| Núcleo | [Core](core/README.md), [ciclo HTTP](core/request-lifecycle.md), [Routing HTTP](core/web-routing.md), [Request y Response](core/request-response.md), [ApiClient](core/api-client.md) | Referencias separadas para el ciclo, WebRouter, mensajes HTTP y cliente saliente; middleware mantiene límites de auditoría. ApiClient tiene anomalías de verbos y timeout registradas. |
| Base de datos | [Base de datos](database/README.md), [conexiones](database/connections.md), [Query Builder](database/query-builder.md), [Model y registros](database/model.md), [descriptores de esquema](database/schemas.md), [constructor DDL](database/schema-builder.md), [migraciones](database/migrations.md) | Referencias de código para conexiones, Query Builder, Model/ModelRecord, esquemas, DDL y ciclo de migraciones; no se ejecutaron consultas, DDL ni migraciones en una base real. |
| HTTP y API | [API HTTP](api/README.md), [resolución de recursos](api/automatic-endpoints.md), [publicación de webhooks](api/webhooks.md) | Webhooks cubre suscripciones, publicación CRUD e interna, matching por conexión/owner, condiciones y seguridad del callback; falta una prueba de entrega con base de datos y callback HTTP reales. |
| Seguridad | [Autenticación](security/authentication.md), [ACL](security/acl.md) | Alcance de código y pruebas focalizadas con límites explícitos; falta una petición de autorización respaldada por la base de datos. |
| Servicios | [Índice de servicios](services/README.md), [cache](services/cache.md), [EventBus](services/event-bus.md), [vistas](services/views.md), [i18n](services/i18n.md), [validación](services/validation.md), [HTML y formularios](services/forms.md) | Referencias separadas de código; no se ejecutaron operaciones de caché, render, formulario ni traducción. Se registran límites de TTL, dependencias de paquete y escape HTML. |
| CLI | [CLI](cli/README.md), [catálogo `php com`](cli/commands.md), [routing CLI](cli/routing.md) | CommandRegistry y grupos de ayuda se contrastaron con el checkout; no se ejecutaron subcomandos operativos. CliRouter conserva una referencia independiente. |
| Composer | [Límites de paquetes Composer](architecture/composer-packages.md), [proceso de publicación](architecture/composer-release-process.md) | Contratos y límites de los paquetes existentes; una instalación de consumidor se declara verificada solo con evidencia de la versión publicada. |
| Integraciones | [Índice de integraciones](integrations/README.md), [API Clients](integrations/api-clients.md), [Shopify Connector](integrations/shopify.md), [proveedores LLM](integrations/llm-providers.md) | Contratos de código para clientes, Shopify y providers LLM; no se reprodujeron llamadas externas ni instalación de consumidores. El bundle `simplerest-integrations` es sólo un aviso de migración. |
| Despliegue | [Guía de despliegue](deployment/README.md) | El workflow de GitHub Pages está configurado; el despliegue de una aplicación SimpleRest no tiene una receta validada en este checkout. |
| Rendimiento | [Guía de medición](performance/README.md) | Se clasificaron los scripts existentes y sus efectos; el informe numérico es histórico y no se reprodujo. |
| Referencia | [Estado de referencia](reference/README.md) | Falta construir el catálogo de configuración, variables, comandos y métodos públicos. |

## Cómo interpretar el estado

Las páginas distinguen entre evidencia de implementación, configuración, pruebas y ejecución reproducida. Que una clase o método exista no demuestra que un tutorial completo funcione. El registro interno de auditoría conserva afirmaciones rechazadas, límites y trabajo pendiente.

Una discrepancia entre código y fuente editorial canónica debe investigarse; no debe resolverse reescribiendo automáticamente la documentación para ajustarla al estado actual del código. Véase [Gobernanza de la documentación](documentation-governance.md).

## Material histórico e interno

`audit/` conserva evidencia y páginas pendientes; `_internal/legacy/` conserva índices y material histórico. Estas carpetas no son documentación publicable. La futura compilación del sitio también debe excluir `framework/` (que conserva sólo un puntero, archivos de archivo y material interno) y cualquier página legacy.
