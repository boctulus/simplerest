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
| Núcleo | [Core](core/README.md), [ciclo HTTP](core/request-lifecycle.md), [Routing HTTP](core/web-routing.md), [Request y Response](core/request-response.md), [middleware](core/middleware.md), [handlers](core/handlers.md), [configuración y entorno](core/configuration-environment.md), [providers](core/service-providers.md), [Container](core/dependency-container.md), [CORS](core/cors.md), [sesiones y cookies](core/sessions-cookies.md), [errores](core/error-handling.md), [logging](core/logging.md), [filesystem](core/filesystem.md), [uploads](core/file-uploads.md), [ZIP y backups](core/archives-backups.md), [HTML/XML e imágenes](core/content-conversion.md), [ApiClient](core/api-client.md) | El orden global de resolvers queda en Arquitectura; el ciclo HTTP no describe CliRouter. Runtime, filesystem y conversión están trazados a fuente y no reproducidos. Se explicitan rutas no confinadas, helper base64 incompleto y límites de TLS/archivo. ApiClient mantiene anomalías de verbos y timeout registradas. |
| Base de datos | [Base de datos](database/README.md), [conexiones](database/connections.md), [Query Builder](database/query-builder.md), [paginación](database/pagination.md), [Model y registros](database/model.md), [relaciones y subrecursos](database/relations.md), [SQL raw](database/raw-queries.md), [transacciones](database/transactions.md), [compatibilidad de drivers](database/driver-compatibility.md), [descriptores de esquema](database/schemas.md), [constructor DDL](database/schema-builder.md), [migraciones](database/migrations.md) | Referencias separadas y trazadas en fuente; no se ejecutaron consultas, escrituras, transacciones, DDL, migraciones, joins ni llamadas por driver. La matriz de compatibilidad sigue incompleta; paginación no se probó con base de datos. |
| HTTP y API | [API HTTP](api/README.md), [resolución de recursos](api/automatic-endpoints.md), [controladores de recursos](api/resource-controllers.md), [Collections](api/collections.md), [TrashCan](api/trashcan.md), [endpoints de archivos](api/file-endpoints.md), [publicación de webhooks](api/webhooks.md) | Resolución, controladores, recursos especiales, archivos y webhooks tienen referencias por responsabilidad. Son trazas de fuente; no se ejecutaron flujos CRUD ni operaciones de archivos, y los límites de autorización quedan registrados. |
| Seguridad | [Autenticación](security/authentication.md), [JWT](security/jwt.md), [ACL](security/acl.md), [implementaciones ACL](security/acl-implementations.md), [motor de autorización](security/authorization-engine.md) | Core, paquetes opcionales y motor se documentan por separado; persisten límites de implementación y no se ejecutó una petición de autorización respaldada por base de datos. |
| Servicios | [Índice de servicios](services/README.md), [cache](services/cache.md), [EventBus](services/event-bus.md), [vistas](services/views.md), [i18n](services/i18n.md), [validación](services/validation.md), [HTML y formularios](services/forms.md) | Referencias separadas de código; no se ejecutaron operaciones de caché, render, formulario ni traducción. Se registran límites de TTL, dependencias de paquete y escape HTML. |
| CLI | [CLI](cli/README.md), [catálogo `php com`](cli/commands.md), [routing CLI](cli/routing.md) | CommandRegistry y grupos de ayuda se contrastaron con el checkout; no se ejecutaron subcomandos operativos. CliRouter conserva una referencia independiente. |
| Composer y paquetes | [Catálogo de paquetes](packages/catalog.md), [paquetes extraídos del core](packages/README.md), [límites seleccionados](architecture/composer-packages.md), [proceso de publicación](architecture/composer-release-process.md) | El catálogo refleja los manifiestos del checkout; no acredita tags, registro en Packagist, instalación en consumidor ni soporte. Los resultados de release fechados son históricos. |
| Integraciones | [Índice de integraciones](integrations/README.md), [API Clients](integrations/api-clients.md), [Shopify Connector](integrations/shopify.md), [proveedores LLM](integrations/llm-providers.md), [correo](integrations/mail.md), [geolocalización](integrations/geolocation.md), [inspección web](integrations/web-inspection.md) | Contratos de código; no se reprodujeron llamadas externas, envío de correo, instalación de consumidores ni compatibilidad actual con proveedores. El bundle `simplerest-integrations` es sólo un aviso de migración. |
| Despliegue | [Guía de despliegue](deployment/README.md) | El workflow de GitHub Pages está configurado; el despliegue de una aplicación SimpleRest no tiene una receta validada en este checkout. |
| Rendimiento | [Guía de medición](performance/README.md) | Se clasificaron los scripts existentes y sus efectos; el informe numérico es histórico y no se reprodujo. |
| Referencia | [Referencia pública](reference/README.md) | Catálogo de configuración y entorno, helpers globales, entradas CLI, contratos y familias de clases; distingue carga de aplicación, Composer y componentes internos. |

## Cómo interpretar el estado

Las páginas distinguen entre evidencia de implementación, configuración, pruebas y ejecución reproducida. Que una clase o método exista no demuestra que un tutorial completo funcione. El registro interno de auditoría conserva afirmaciones rechazadas, límites y trabajo pendiente.

Una discrepancia entre código y fuente editorial canónica debe investigarse; no debe resolverse reescribiendo automáticamente la documentación para ajustarla al estado actual del código. Véase [Gobernanza de la documentación](documentation-governance.md).

## Material histórico e interno

`audit/` conserva evidencia y páginas pendientes; `_internal/legacy/` conserva índices y material histórico. Estas carpetas no son documentación publicable. La futura compilación del sitio también debe excluir `framework/` (que conserva sólo un puntero, archivos de archivo y material interno) y cualquier página legacy.
