# Pending Tasks — SimpleRest

> Consolidado desde `docs/_internal/to-do/`. Generado: 2026-05-26.

---

## 1. Core & Arquitectura

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 1.1 | Mover `providers` de `config/config.php` a `config/providers.php` | PENDING | TODO !must-do.md |
| 1.2 | Implementar ModuleProvider (similar a Service Providers de packages) con registro en `config/providers.php` + ciclo boot()/register() | PENDING | TODO !must-do.md |
| 1.3 | Reorganizar `app/Core` → `src/Core` (separar framework de app) | PENDING | SimpleRest_Plan_de_Trabajo.md |
| 1.4 | Crear carpetas `modules/` y `examples/` en raíz | PENDING | SimpleRest_Plan_de_Trabajo.md |
| 1.5 | Implementar autoloading modular dinámico sin Composer | PENDING | TODO important.md |
| 1.6 | Añadir Service Container o `App::make()` tipo Laravel | PENDING | TODO important.md |
| 1.7 | Introducir sistema de configuración centralizado (dotenv + overrides) | PENDING | TODO important.md |
| 1.8 | Convertir Config a versión sin `static` (DI container + singleton) | PENDING | TODO estructurado.txt |
| 1.9 | Crear clase `ImmutableConfig` como versión inmutable de Config | PENDING | TODO estructurado.txt, TODO Simplerest.txt |
| 1.10 | Mantener simplicidad: arrays over objects | ✅ DONE | TODO !must-do.md |
| 1.11 | Mantener Reflection mínimo y auditado | PENDING | TODO !must-do.md |

## 2. Router & FrontController

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 2.1 | Ejecutar recomendaciones de ChatGPT sobre WebRouter | PENDING | TODO estructurado.txt, TODO Simplerest.txt |
| 2.2 | Agregar wildcard support al WebRouter (catch-all routes) | PENDING | api-rest-from-frontcontroller-to-webrouter.md |
| 2.3 | Agregar `describe()` para funciones anónimas en routers CLI | PENDING | TODO estructurado.txt |
| 2.4 | Arreglar middleware cuando NO se especifica action con `@` | PENDING | TODO estructurado.txt |
| 2.5 | FrontController debe poder ejecutar controladores de distintos módulos registrados | PENDING | TODO Simplerest.txt |
| 2.6 | Parámetros adicionales en Middlewares (estilo Express.js) | PENDING | TODO estructurado.txt |
| 2.7 | Poder deshabilitar FrontController por package | ✅ DONE | fix-perfomance.md |
| 2.8 | Poder elegir front_controller_http / front_controller_console en config | PENDING | TODO Simplerest.txt |
| 2.9 | El ApiController debe poder funcionar con web_router para apagar front_controller | PENDING | fix-perfomance.md |

## 3. Query Builder

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 3.1 | Implementar `insert()` a nivel de Model con SubRecursos | PENDING | TODO estructurado.txt |
| 3.2 | Clarificar diferencias entre execution modes (`executionMode` vs `exec`) | PENDING | TODO estructurado.txt |
| 3.3 | Decidir jerarquía de clases Exception | PENDING | TODO estructurado.txt |
| 3.4 | Implementar Adapter Pattern para casos como inflector (evitar acoplamiento a Composer) | PENDING | TODO estructurado.txt |
| 3.5 | Añadir Eager Loading opcional | PENDING | TODO important.md |
| 3.6 | Crear capa ORM ligera opcional (`Model::hasOne()`, `belongsTo()`) | PENDING | TODO important.md |
| 3.7 | Soporte para soft deletes, scopes y casts automáticos | PENDING | TODO important.md |
| 3.8 | Extender integración con Redis/Memcached para caching de queries | PENDING | TODO important.md |
| 3.9 | Implementar benchmark tests para QueryBuilder | PENDING | TODO !must-do.md |

## 4. ORM & Modelos

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 4.1 | Refactorizar `getRels()` en `RelationshipTrait` (500+ líneas, dividir en métodos pequeños) | PENDING | TODO RelationshipTrait.md |
| 4.2 | Crear tests unitarios para RelationshipTrait | PENDING | TODO RelationshipTrait.md |
| 4.3 | Reducir dependencias de funciones globales en RelationshipTrait | PENDING | TODO RelationshipTrait.md |
| 4.4 | Documentar mejor con PHPDoc RelationshipTrait | PENDING | TODO RelationshipTrait.md |
| 4.5 | Pseudo-ORM con arrays asociativos + `save()` que agrupe escrituras en bulk | PENDING | TODO ORM.md |
| 4.6 | Implementar hook `onPuttingBeforeCheck()` correctamente en TrashCan | IN PROGRESS | in-progress/api-tests-progress.md |

## 5. Migraciones

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 5.1 | `--simulate` ejecutar migraciones en transacción con ROLLBACK o tablas temporales | PENDING | TODO estructurado.txt |
| 5.2 | Crear migración desde DTO (`php com make migration --from_dto`) | PENDING | TODO estructurado.txt |
| 5.3 | Permitir combinar `--dir=` + `--to` + `--file=` | PENDING | TODO estructurado.txt |
| 5.4 | Mejorar información de errores en Exceptions (tipo, código, location) | PENDING | TODO estructurado.txt |
| 5.5 | Revisar generación de Schemas: `id` como primary key aparece como nullable | PENDING | TODO estructurado.txt |
| 5.6 | Integrar auto-generación de migraciones desde modelos | PENDING | TODO important.md |
| 5.7 | Soporte a migraciones diferidas y rollback selectivo | PENDING | TODO important.md |
| 5.8 | Mejorar `php com make` para generar seeds, modules y tests | PENDING | TODO important.md |
| 5.9 | NO está agregando PRIMARY KEY en Schema cuando se usa `->primary()` encadenado — usar `addPrimary()` como workaround | IN PROGRESS | TODO Simplerest.txt |
| 5.10 | `addUnique()` sobre varios campos no funciona | PENDING | TODO Simplerest.txt |
| 5.11 | Guardar nombre de tabla como propiedad para evitar repetir en up()/down() | PENDING | TODO Simplerest.txt |
| 5.12 | Generar schema automáticamente después de cada migración | PENDING | TODO Simplerest.txt |
| 5.13 | Bug: migraciones fallan en PHP 8+ (repiten archivos, "There is no active transaction") | PENDING | TODO Simplerest.txt |

## 6. CLI & Comandos

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 6.1 | Crear sistema de comandos similar a FriendlyPOS (fork "Adoon" luego migrar) | PENDING | TODO !must-do.md |
| 6.2 | `php com make controller {name} --module={module}` | PENDING | TODO estructurado.txt |
| 6.3 | `php com make model {name} --module={module}` | PENDING | TODO estructurado.txt |
| 6.4 | `php com make discovery` | PENDING | TODO estructurado.txt |
| 6.5 | `php com make package {name}` | ✅ DONE | packages/ existe |
| 6.6 | `php com make form-control` | PENDING | TODO estructurado.txt |
| 6.7 | `php com crud {table} --list/find/delete` | PENDING | TODO estructurado.txt |
| 6.8 | `php com db list` / `--tables` / `import` | PENDING | TODO estructurado.txt |
| 6.9 | `php com routes list` / `find` | PENDING (Router group exists but needs verification) | TODO estructurado.txt |
| 6.10 | `php com lib {lib}::{method}` (router a librerías) | PENDING | TODO Simplerest.txt |
| 6.11 | `php com core_lib Files:mkdir_or_fail` | PENDING | TODO Simplerest.txt |
| 6.12 | Modo interactivo con `--interactive` o `-I` en ICommand | PENDING | TODO Simplerest.txt |
| 6.13 | Plugins/extensiones en FileCommand | PENDING | TODO Simplerest.txt |
| 6.14 | Script `copy` para copiar core entre frameworks (`php com cp_core`, `cp_file`) | PENDING | TODO Simplerest.txt |
| 6.15 | CLI installer opcional (no Composer) | PENDING | TODO !must-do.md |

## 7. PSR Standards (Fases 3-5)

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 7.1 | Fase 3: PSR-17 HTTP Factories — Instalar `psr/http-factory:^1.0` | PENDING | phase-3-psr17-factories.md |
| 7.2 | Fase 3: Implementar `ResponseFactory` | PENDING | phase-3-psr17-factories.md |
| 7.3 | Fase 3: Implementar `ServerRequestFactory` | PENDING | phase-3-psr17-factories.md |
| 7.4 | Fase 3: Implementar `StreamFactory` | PENDING | phase-3-psr17-factories.md |
| 7.5 | Fase 3: Implementar `UriFactory` | PENDING | phase-3-psr17-factories.md |
| 7.6 | Fase 3: Implementar `RequestFactory` | PENDING | phase-3-psr17-factories.md |
| 7.7 | Fase 3: Crear helpers `psr17_*()` en `app/Core/Helpers/psr17.php` | PENDING | phase-3-psr17-factories.md |
| 7.8 | Fase 3: Tests (15+ tests, 50+ assertions) | PENDING | phase-3-psr17-factories.md |
| 7.9 | Fase 4: PSR-15 Middleware — Instalar `psr/http-server-handler` y `psr/http-server-middleware` | PENDING | phase-4-psr15-middleware.md |
| 7.10 | Fase 4: Implementar `MiddlewareDispatcher` (pipeline) | PENDING | phase-4-psr15-middleware.md |
| 7.11 | Fase 4: Implementar `CallableRequestHandler` | PENDING | phase-4-psr15-middleware.md |
| 7.12 | Fase 4: Crear `LegacyMiddlewareAdapter` para middlewares existentes | PENDING | phase-4-psr15-middleware.md |
| 7.13 | Fase 4: Crear middlewares PSR-15 nativos (Auth, CORS, Logging, Validation) | PENDING | phase-4-psr15-middleware.md |
| 7.14 | Fase 4: Integrar con WebRouter | PENDING | phase-4-psr15-middleware.md |
| 7.15 | Fase 4: Tests (20+ tests, 60+ assertions) | PENDING | phase-4-psr15-middleware.md |
| 7.16 | Fase 5: StreamInterface Body en Request (reemplazar `$body` string/array por `StreamInterface`) | PENDING | phase-5-stream-interface.md |
| 7.17 | Fase 5: StreamInterface Body en Response | PENDING | phase-5-stream-interface.md |
| 7.18 | Fase 5: Helpers para streams (`stream_from_file`, `stream_from_string`, etc.) | PENDING | phase-5-stream-interface.md |
| 7.19 | Fase 5: Clases de streaming avanzado (StreamResponse, FileResponse, EventStreamResponse, ChunkedResponse) | PENDING | phase-5-stream-interface.md |
| 7.20 | Fase 5: Tests (25+ tests, 70+ assertions) | PENDING | phase-5-stream-interface.md |

## 8. Seguridad & ACL

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 8.1 | Request y Response deben usar métodos de clase para concurrencia | PENDING | TODO estructurado.txt |
| 8.2 | Revisar dependencias con problemas de seguridad en composer.json | PENDING | TODO estructurado.txt |
| 8.3 | Enviar headers de seguridad: X-Content-Type-Options, X-Frame-Options, X-XSS-Protection | PENDING | TODO estructurado.txt |
| 8.4 | Implementar invalidación de tokens (tablas `access_tokens`, `access_token_blacklist`, endpoint `/api/auth/revoke`) | PENDING | TODO estructurado.txt |
| 8.5 | Integrar JWT opcional y autenticación por API key | PENDING | TODO important.md |
| 8.6 | Añadir rate limiting, password reset y multi-auth guards | PENDING | TODO important.md |
| 8.7 | Middleware de seguridad CORS, CSRF y roles | PENDING | TODO important.md |
| 8.8 | Permitir modificar permisos de roles dinámicamente + comando `php com cache acl` | PENDING | TODO Simplerest.txt |
| 8.9 | ACL apagable por request/endpoint/pipeline | PENDING | fix-perfomance.md |
| 8.10 | Deshabilitar escaneo de módulos en producción | PENDING | fix-perfomance.md |
| 8.11 | Completar y testear `Acl::hasPermission()` | PENDING | TODO Simplerest.txt |
| 8.12 | Bug: usuario sin roles no es considerado "registrado" | PENDING | TODO Simplerest.txt |

## 9. EventBus & Hooks

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 9.1 | Mejorar EventBus: Single Event Persistence (solo último valor) | PENDING | TODO estructurado.txt |
| 9.2 | Mejorar EventBus: Synchronous Notification (bloquea ejecución) | PENDING | TODO estructurado.txt |
| 9.3 | Mejorar EventBus: No hay ack/retry mechanism | PENDING | TODO estructurado.txt |
| 9.4 | Mejorar EventBus: Falta queuing mechanism | PENDING | TODO estructurado.txt |
| 9.5 | Mejorar sistema de hooks/events para modularidad avanzada | PENDING | TODO important.md |

## 10. Jobs & Queues

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 10.1 | Mejorar persistencia y monitoreo de jobs fallidos | PENDING | TODO important.md |
| 10.2 | Añadir soporte para colas distribuidas (Redis/RabbitMQ) | PENDING | TODO important.md |
| 10.3 | Integrar workers paralelos para alta concurrencia | PENDING | TODO important.md |

## 11. Frontend & UI

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 11.1 | Implementar componentes de UI como en FriendlyPOS (Node.js) | PENDING | TODO !must-do.md |
| 11.2 | Terminar generador de front | PENDING | TODO estructurado.txt |
| 11.3 | Incorporar switches del plugin "Import quoter cl" | PENDING | TODO estructurado.txt |
| 11.4 | Agregar Dropdown sub-menu | PENDING | TODO estructurado.txt |
| 11.5 | Construir formularios responsivos desde arrays | PENDING | TODO estructurado.txt |
| 11.6 | Implementar campos: Dropdown, Radio, Checkboxes, TinyMCE, File upload, Image upload, Gallery, Date/Time/DateTime picker | PENDING | TODO estructurado.txt |
| 11.7 | DataTables: centrar celdas con cabeceras Tabulator | PENDING | TODO estructurado.txt |
| 11.8 | DataTables: checkbox "seleccionar todos" | PENDING | TODO estructurado.txt |
| 11.9 | DataTables: ocultar "Delete" hasta seleccionar registros | PENDING | TODO estructurado.txt |
| 11.10 | DataTables: reemplazar 3 botones por "..." desplegable | PENDING | TODO estructurado.txt |
| 11.11 | DataTables: deprecated edición inline por defecto | PENDING | TODO estructurado.txt |
| 11.12 | DataTables: modo "view" al click en row | PENDING | TODO estructurado.txt |
| 11.13 | Permitir assets en módulos/packages con build script + deploy en `./public/assets` | PENDING | TODO estructurado.txt |
| 11.14 | Pestaña para ver registros borrados (soft-delete) con botón restore | PENDING | TODO Simplerest.txt |
| 11.15 | Implementar UNDO stack para operaciones de borrado | PENDING | TODO Simplerest.txt |
| 11.16 | UI Controllers acoplados a vistas concretas (Fragment-style como Android) | PENDING | TODO Simplerest.txt |
| 11.17 | ViewModels para almacenar estado de vistas | PENDING | TODO Simplerest.txt |
| 11.18 | Motor de plantillas con {grid}/{row}/{col} | PENDING | TODO Simplerest.txt |
| 11.19 | Implementar herencia de plantillas | PENDING | TODO Simplerest.txt |
| 11.20 | Considerar DaisyUI en vez de Bootstrap | PENDING | TODO Simplerest.txt |
| 11.21 | Implementar modularización via "plugins" (convertibles a packages) | PENDING | TODO Simplerest.txt |
| 11.22 | Minificar CSS/JS y usar /dist en producción | PENDING | TODO Simplerest.txt |

## 12. Documentación

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 12.1 | Documentar funciones con @param y @return (PHPdoc) vía IA + inserción quirúrgica | PENDING | TODO estructurado.txt |
| 12.2 | Enfoque híbrido Starlight + Doxygen para documentación | PENDING | TODO estructurado.txt |
| 12.3 | Generar documentación con Doxygen | PENDING | TODO estructurado.txt |
| 12.4 | Complementar con documentación existente en .txt | PENDING | TODO estructurado.txt |
| 12.5 | Documentar progreso de pruebas unitarias | PENDING | TODO estructurado.txt |
| 12.6 | Expandir `/docs` con ejemplos CRUD, packages/modules, ciclo de vida request | PENDING | TODO important.md |
| 12.7 | Crear sitio web estático simplerest.dev para docs | PENDING | TODO important.md |
| 12.8 | Publicar SimpleRest-philosophy.md públicamente | ✅ DONE | TODO !must-do.md |
| 12.9 | Documentar el generador automático de endpoints y autojoin como killer feature | PENDING | TODO !must-do.md |
| 12.10 | Mantener documentación de routing actualizada | PENDING | TODO !must-do.md |

## 13. API & Modelado

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 13.1 | Generar documentación en RAML | PENDING | TODO estructurado.txt |
| 13.2 | Implementar Swagger-UI | PENDING | TODO estructurado.txt |
| 13.3 | Usar readme.com para documentación de APIs | PENDING | TODO estructurado.txt |
| 13.4 | Resolver problemas con PSR-4/PSR-0 (warnings de autoloading, clases con nombres incorrectos) | PENDING | TODO estructurado.txt |
| 13.5 | Generar Constants para rutas compatibles con Service Workers | PENDING | TODO estructurado.txt |
| 13.6 | En cada API para terceros, incluir forma sencilla de probar (token + JSON request) | PENDING | TODO Simplerest.txt |
| 13.7 | Documentar HTTP codes y códigos de error en cada API | PENDING | TODO Simplerest.txt |

## 14. Testing

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 14.1 | Implementar benchmark tests para cada core component (Router, QueryBuilder, ORM-lite, Caching) | PENDING | TODO !must-do.md |
| 14.2 | Automatizar performance testing post-commit | PENDING | TODO !must-do.md |
| 14.3 | Build CLI tool para medir boot time, request handling, DB query latency | PENDING | TODO !must-do.md |
| 14.4 | Benchmark contra Laravel y WordPress usando endpoints equivalentes | PENDING | TODO !must-do.md |
| 14.5 | Actualizar y mantener pruebas unitarias existentes | PENDING | TODO estructurado.txt |
| 14.6 | Tests: Event hooks (modelos, API) | PENDING | TODO estructurado.txt |
| 14.7 | Tests: Comparar impersonate vs login | PENDING | TODO estructurado.txt |
| 14.8 | Tests: Comparar con Laravel Query Builder | PENDING | TODO estructurado.txt |
| 14.9 | Añadir `tests/` con PHPUnit + cobertura de CLI y DB | PENDING | TODO important.md |
| 14.10 | Incluir comandos `php com test` y `php com lint` | PENDING | TODO important.md |
| 14.11 | ApiCollectionsTest.php — corregir y pasar 4/4 tests | IN PROGRESS | in-progress/api-tests-progress.md |
| 14.12 | ApiTrashCanTest.php — Bug: testSoftDeleteAndTrashCan falla (producto no aparece en trash_can) | IN PROGRESS | in-progress/api-tests-progress.md |
| 14.13 | ApiTest.php — ejecutar y corregir tests del grupo `refactor` | IN PROGRESS | in-progress/api-tests-progress.md |
| 14.14 | Stress testing con JMeter, RESTful Stress, telegraf + influxdb + grafana | PENDING | TODO estructurado.txt |

## 15. Cache & Optimización

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 15.1 | Testear en Nginx, LiteSpeed y Apache | PENDING | TODO estructurado.txt |
| 15.2 | Zipear respuestas HTTP | PENDING | TODO estructurado.txt |
| 15.3 | Usar PHP-FPM (opcode caching) | PENDING | TODO estructurado.txt |
| 15.4 | Implementar OPCache | PENDING | TODO estructurado.txt |
| 15.5 | Memcached / Redis | PENDING | TODO estructurado.txt |
| 15.6 | Cachear views de componentes | PENDING | TODO estructurado.txt |
| 15.7 | Implementar build simple con GULP (minificar, compilar preprocesadores, optimizar imágenes) | PENDING | TODO estructurado.txt |
| 15.8 | Añadir caching estratégico (QueryBuilder, autoloading, endpoints) | PENDING | TODO !must-do.md |
| 15.9 | Implementar Swoole | PENDING | TODO estructurado.txt |
| 15.10 | HTTP 2.0 y 3.0 | PENDING | TODO estructurado.txt |
| 15.11 | Paginación eficiente (seek method) | PENDING | TODO estructurado.txt |
| 15.12 | CDN (Cloudflare) | PENDING | TODO estructurado.txt |

## 16. Base de Datos & Multi-tenant

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 16.1 | Options: `get_option()` y `set_option()` por scope de DB | PENDING | TODO estructurado.txt |
| 16.2 | Securitizar tenant_id (hash sobre conexión) | PENDING | TODO estructurado.txt |
| 16.3 | Asociar tenants a dominios/subdominios + puerto | PENDING | TODO estructurado.txt |
| 16.4 | Soporte JSON completo | PENDING | TODO estructurado.txt |
| 16.5 | Transients (DB, archivos, REDIS, Memcached) | PENDING | TODO estructurado.txt |
| 16.6 | CSV a migraciones | PENDING | TODO estructurado.txt |
| 16.7 | Seeders | PENDING | TODO estructurado.txt |
| 16.8 | Store Procedures | PENDING | TODO estructurado.txt |
| 16.9 | Cursors | PENDING | TODO estructurado.txt |
| 16.10 | Views (read-only) | PENDING | TODO estructurado.txt |
| 16.11 | PostgreSQL completo | PENDING | TODO estructurado.txt |
| 16.12 | SQLite optimizado | PENDING | TODO estructurado.txt |

## 17. Paquetes & Módulos

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 17.1 | Convertir core libraries a packages: SendinBlue, FacebookConnect, GoogleConnect, Obfuscator, laravelGenerator, PostmanGenerator, GoogleMaps | PENDING | TODO estructurado.txt |
| 17.2 | Collections (Laravel wrapper) como package | PENDING | TODO estructurado.txt |
| 17.3 | Carbon (DateTime extension) como package | PENDING | TODO estructurado.txt |
| 17.4 | Framework como package privado de Composer | PENDING | TODO estructurado.txt |
| 17.5 | Service Providers con autodiscovery | PENDING | TODO estructurado.txt |
| 17.6 | Vendor:publish para configuración | PENDING | TODO estructurado.txt |
| 17.7 | Conversión bidireccional modules ↔ packages | PENDING | TODO Simplerest.txt |
| 17.8 | Permitir que módulos/packages definan sus propios comandos CLI via ServiceProvider | PENDING | comparativa_simplerest_vs_laravel.md |

## 18. Docker & DevOps

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 18.1 | docker-compose para SimpleRest + phpmyadmin | PENDING | TODO estructurado.txt |
| 18.2 | Implementar "Laravel Sail" equivalente | PENDING | TODO estructurado.txt |
| 18.3 | Nginx container | PENDING | TODO estructurado.txt |
| 18.4 | GitHub Actions para migraciones automáticas, tests, deploy automático | ✅ DONE (.github/workflows/tests.yml) | TODO estructurado.txt |
| 18.5 | Dockerfile de desarrollo rápido con php-fpm + nginx | PENDING | TODO important.md |
| 18.6 | Implementar logging avanzado | PENDING | TODO estructurado.txt |
| 18.7 | Implementar Laravel Telescope equivalente | PENDING | TODO estructurado.txt |
| 18.8 | Implementar CI/CD básico con GitHub Actions | ✅ DONE | TODO important.md |

## 19. Integraciones

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 19.1 | Depurar SDK Firebase (errores silenciosos) | PENDING | TODO important.md |
| 19.2 | Extender LLM Providers con Anthropic, Mistral, Gemini | PENDING | TODO important.md |
| 19.3 | Soporte a WebSockets y eventos en tiempo real | PENDING | TODO important.md |
| 19.4 | Incluir helpers para integración React/Vue | PENDING | TODO important.md |
| 19.5 | Añadir renderer HTML5 nativo con plantillas dinámicas | PENDING | TODO important.md |

## 20. Bugs Conocidos

| # | Tarea | Estado | Fuente |
|---|-------|--------|--------|
| 20.1 | UPDATE no actualiza con bindings estilo `UPDATE star_rating SET gender='?', author='?'` | PENDING | TODO Simplerest.txt |
| 20.2 | `render()` no envía JS y CSS a templates (pero `view()` sí) | PENDING | TODO Simplerest.txt |
| 20.3 | Falta llamar a `auth()->setPermissions()` al loguear usuario — afecta `hasSpecialPermission()` | PENDING | TODO Simplerest.txt |
| 20.4 | Vista puede renderizar algo antes de mostrar error (después de cambios en Response/FrontController) | PENDING | TODO Simplerest.txt |
| 20.5 | Migraciones fallan en PHP 8+ (repite archivos, "There is no active transaction") | PENDING | TODO Simplerest.txt |
| 20.6 | `id` es Primary Key pero aparece como nullable en schemas generados | PENDING | TODO Simplerest.txt |
| 20.7 | `addUnique()` sobre varios campos no funciona en migraciones | PENDING | TODO Simplerest.txt |
| 20.8 | Bug: permite actualizar `belongs_to`, `updated_by` y `updated_at` sin permisos especiales | PENDING | TODO Simplerest.txt |
| 20.9 | PRIMARY KEY no se agrega cuando se usa `->primary()` encadenado en Schema | IN PROGRESS | TODO Simplerest.txt |
| 20.10 | testSoftDeleteAndTrashCan — producto no aparece en trash_can después de soft delete | IN PROGRESS | in-progress/api-tests-progress.md |

---

## 21. Evaluación para Lanzamiento Público

### ¿Falta mucho?

El framework **ya es funcional**: tiene router, query builder con joins/subconsultas, migrations con tracking, CLI, auth por tokens, endpoints API automáticos, jobs/queues, packages. Muchas aplicaciones reales podrían construirse hoy. Pero para presentarlo al público como un framework "serio" en 2026, hay carencias importantes.

### Crítico — Bloqueante para público

| # | Carencia | Impacto |
|---|----------|---------|
| 1 | **Suite de tests inexistente** — `tests/` raíz vacío, sin PHPUnit configurado. | Ningún dev confía en un framework sin tests. Es la carencia #1. |
| 2 | **Seguridad incompleta** — Token invalidation no implementado, `Acl::hasPermission()` sin terminar, usuarios sin roles no son "registrados", faltan security headers, hay bugs activos de auth. | Vulnerabilidades reales + mala impresión. |
| 3 | **PHP 8+ compatibility rota** — Migraciones fallan en PHP 8: repite archivos y lanza "There is no active transaction". Bloquea adopción en entornos modernos. |
| 4 | **Documentación insuficiente** — Hay docs parciales pero falta una guía cohesiva, ejemplos CRUD completos, explicación del ciclo de vida del request, tutorial de inicio rápido. |

### Muy serio — Duele pero no mata

| # | Carencia | Nota |
|---|----------|------|
| 5 | **Sin instalación vía Composer** — No existe `composer require boctulus/simplerest`. En 2026 es casi inaceptable. |
| 6 | **Concurrencia** — Request/Response como singletons static. Va a fallar bajo carga concurrente real. Deuda arquitectónica. |
| 7 | **PSR-7/15/17 no implementados** — Si bien es filosófico, la interoperabilidad con el ecosistema PHP moderno lo exige. |
| 8 | **CLI roto** — `php com Acl help` explota (controller no encontrado). Mala primera impresión. |

### Veredicto

El core es sólido pero necesita **3-4 meses de trabajo enfocado** en:

1. **Testing** — Suite base + CI整合ado
2. **Seguridad** — Token validation, ACL completo, PHP 8 compat
3. **Documentación** — Guía cohesiva + ejemplos
4. **Composer package** — Instalación vía `composer create-project`

Lo no-crítico (frontend, PSR completo, streaming, Redis, multi-tenant avanzado) puede llegar en versiones posteriores sin afectar el lanzamiento.

---

*Tasks extracted from: `docs/_internal/to-do/TODO !must-do.md`, `TODO estructurado.txt`, `TODO important.md`, `TODO ORM.md`, `TODO RelationshipTrait.md`, `TODO Simplerest.txt`, `SimpleRest_Plan_de_Trabajo.md`, `api-rest-from-frontcontroller-to-webrouter.md`, `comparativa_simplerest_vs_laravel.md`, `fix-perfomance.md`, `phase-3-psr17-factories.md`, `phase-4-psr15-middleware.md`, `phase-5-stream-interface.md`, `in-progress/api-tests-progress.md`, `sections/Modules.md`, `sections/Packages.md`, `orm/ORM.md`, `orm/ORM-Laravel-like.md`*
