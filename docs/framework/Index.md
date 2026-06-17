# Índice de Documentación — SimpleRest Framework

**Framework PHP modular** | v1.0.0  
**Autor**: Pablo Bozzolo (boctulus) — Software Architect

---

## EMPEZAR AQUÍ

| Documento | Descripción |
|-----------|-------------|
| [README Principal](../README.md) | Vista general del proyecto |
| [QuickStart](./QuickStart.md) | Instalación y primer endpoint |
| [SimpleRest-philosophy.md](./SimpleRest-philosophy.md) | Filosofía de diseño |
| [release-status.md](./release-status.md) | Estado actual del release |

---

## ARQUITECTURA

| Documento | Descripción |
|-----------|-------------|
| [Framework-Architecture.md](./Framework-Architecture.md) | **NUEVO** — Estructura completa de directorios y componentes |
| [FrontController.md](./FrontController.md) | **NUEVO** — Pipeline de 6 handlers |
| [core-directives.md](./core-directives.md) | Principios y metodologías de desarrollo |
| [MIGRATION-v0.9.md](./MIGRATION-v0.9.md) | **NUEVO** — Migrar de v0.9 a v1.0 |
| [SimpleRest-Complete-Docs.md](./SimpleRest-Complete-Docs.md) | Comparativa con Laravel y Supabase |
| [CHANGELOG.md](./CHANGELOG.md) | Registro de cambios del framework |

### PSR Compliance

| Documento | Descripción |
|-----------|-------------|
| [PSR-SUMMARY.md](./PSR-SUMMARY.md) | Estado de cumplimiento PSR |
| [PSR-7.md](./PSR-7.md) | HTTP Message Interfaces (adapters) |
| [ImmutableMethods.md](./ImmutableMethods.md) | Métodos `with*()` en Request/Response |
| [CHANGELOG-PSR.md](./CHANGELOG-PSR.md) | Cambios relacionados con PSR |

---

## RUTEO Y CONTROLLERS

| Documento | Descripción |
|-----------|-------------|
| [Routing.md](./Routing.md) | Sistema completo: WebRouter + CliRouter + Handlers (1669 líneas) |
| [WebRouter.md](./WebRouter.md) | Referencia rápida de WebRouter |
| [Request.md](./Request.md) | Manejo de peticiones HTTP |
| [Response.md](./Response.md) | Manejo de respuestas HTTP |
| [Middlewares.md](./Middlewares.md) | Middlewares y filtros |
| [Output-Methods.md](./Output-Methods.md) | `StdOut::print()` vs `dd()` |

---

## BASE DE DATOS

| Documento | Descripción |
|-----------|-------------|
| [QueryBuilder.md](./QueryBuilder.md) | **Documentación completa del QB** (recomendado) |
| [Schemas.md](./Schemas.md) | **NUEVO** — Definición de esquemas de BD |
| [AutoJoins.md](./AutoJoins.md) | **NUEVO** — Joins automáticos desde schemas |
| [SubResources.md](./SubResources.md) | **NUEVO** — CRUD anidado desde relaciones |
| [Multi-Tenant.md](./Multi-Tenant.md) | **NUEVO** — Múltiples conexiones y multi-tenant |
| [ORM-STATUS.md](./ORM-STATUS.md) | **NUEVO** — Estado del ORM (no funcional, usar QB) |
| [Exceptions.md](./Exceptions.md) | Manejo de excepciones de BD |
| [SimpleRest-API-Rest.md](./SimpleRest-API-Rest.md) | API REST queries: filter, sort, paginate |
| [AutomaticEndpoints-Summary.md](./AutomaticEndpoints-Summary.md) | Endpoints REST automáticos (zero-config) |

---

## SEGURIDAD Y AUTENTICACIÓN

| Documento | Descripción |
|-----------|-------------|
| [Authentication.md](./Authentication.md) | **NUEVO** — JWT Auth: login, register, refresh, social auth |
| [ACL.md](./ACL.md) | Control de Acceso (roles jerárquicos, permisos granulares) |
| [CHANGELOG-acl.md](./CHANGELOG-acl.md) | Evolución del sistema ACL |

---

## VALIDACIÓN Y CACHÉ

| Documento | Descripción |
|-----------|-------------|
| [Validation.md](./Validation.md) | **NUEVO** — Validador de formularios y reglas |
| [Caching.md](./Caching.md) | **NUEVO** — FileCache, DBCache, InMemoryCache |

---

## CLI Y COMANDOS

| Documento | Descripción |
|-----------|-------------|
| [CommandLine.md](./CommandLine.md) | Sistema de comandos CLI |
| [commands/PackCommand.md](./commands/PackCommand.md) | Comando `php com pack` |
| [commands/AclCommands.md](./commands/AclCommands.md) | Referencia completa de 23 comandos ACL |

### Grupos de Comandos

```
php com acl/         → 23 comandos ACL
php com make/        → 42 generadores de código
php com sql/         → 11 comandos SQL
php com migrations/  → 15 comandos de migraciones
php com users/       → 11 comandos de usuarios
php com system/      → clear, opcache-clear
php com test/        → echo, greet, calc, info
php com file/        → list
php com doc/         → from-json
php com module/      → to-package
php com mysql-log/   → on, off, dump, start
php com router/      → list
php com pack/        → build
```

---

## FRONTEND Y VISTAS

| Documento | Descripción |
|-----------|-------------|
| [ViewEngine.md](./ViewEngine.md) | **NUEVO** — Motor de vistas con layouts |
| [HTML-Form-builder.md](./HTML-Form-builder.md) | Constructor programático de formularios HTML |
| [HTML-Form-builder.AdminLTE.md](./HTML-Form-builder.AdminLTE.md) | Formularios con tema AdminLTE |

---

## EXTENSIBILIDAD (PACKAGES Y MÓDULOS)

| Documento | Descripción |
|-----------|-------------|
| [Packages and Modules.md](./Packages%20and%20Modules.md) | Comparativa y guía completa |
| [Pacakges.md](./Pacakges.md) | Creación de packages (**⚠️ typo en nombre**) |
| [ModuleProvider.md](./ModuleProvider.md) | Creación de módulos con ModuleProvider |
| [packages/README.md](./packages/README.md) | **NUEVO** — Catálogo de 11 packages locales |

---

## INTEGRACIONES Y SERVICIOS

| Documento | Descripción |
|-----------|-------------|
| [ApiClient.md](./ApiClient.md) | Cliente HTTP para consumir APIs externas |
| [Ollama-Models.md](./Ollama-Models.md) | Integración con modelos LLM locales |

---

## EVENTOS Y HOOKS

| Documento | Descripción |
|-----------|-------------|
| [EventBus.md](./EventBus.md) | **NUEVO** — Sistema de eventos Observer |
| (Model hooks) | `boot()`, `onReading()`, `onCreating()`, etc. |

---

## HERRAMIENTAS DE DESARROLLO

| Documento | Descripción |
|-----------|-------------|
| [Helpers.md](./Helpers.md) | **NUEVO** — Referencia de 21 helpers globales |
| [TestingGuide.md](./TestingGuide.md) | **NUEVO** — PHPUnit + Playwright |
| [unit-tests-pruebas-unitarias.md](./unit-tests-pruebas-unitarias.md) | **NUEVO** — Guía de tests unitarios |

---

## INTERNACIONALIZACIÓN

| Documento | Descripción |
|-----------|-------------|
| [i18n.md](./i18n.md) | **NUEVO** — Sistema de traducciones gettext |

---

## DEPLOYMENT Y PERFORMANCE

| Documento | Descripción |
|-----------|-------------|
| [Deployment.md](./Deployment.md) | **NUEVO** — Nginx, Apache, Docker |
| [Performance.md](./Performance.md) | **NUEVO** — Optimización, OPCache, Swoole |

---

## DOCUMENTOS ORIGINALES (respaldo)

| Documento | Nota |
|-----------|------|
| [DOC-Simplerest.txt](./_archive/DOC-Simplerest.txt) | **ARCHIVADO** — Documentación original obsoleta (pre-v1.0) |

---

## ESTRUCTURA DEL DIRECTORIO docs/

```
docs/
├── Index.md                            ← Este archivo
│
├── START HERE/
│   ├── README.md (root)
│   ├── QuickStart.md
│   └── SimpleRest-philosophy.md
│
├── ARCHITECTURE/
│   ├── Framework-Architecture.md       ← NUEVO
│   ├── FrontController.md              ← NUEVO
│   ├── core-directives.md
│   ├── MIGRATION-v0.9.md               ← NUEVO
│   ├── SimpleRest-Complete-Docs.md
│   ├── release-status.md
│   ├── CHANGELOG.md
│   ├── CHANGELOG-acl.md
│   ├── CHANGELOG-PSR.md
│   ├── PSR-SUMMARY.md
│   ├── PSR-7.md
│   └── ImmutableMethods.md
│
├── CORE/
│   ├── Routing.md
│   ├── WebRouter.md
│   ├── Request.md
│   ├── Response.md
│   ├── Middlewares.md
│   ├── Exceptions.md
│   ├── Validation.md                   ← NUEVO
│   ├── Authentication.md               ← NUEVO
│   └── Output-Methods.md
│
├── DATABASE/
│   ├── QueryBuilder.md
│   ├── Schemas.md                      ← NUEVO
│   ├── AutoJoins.md                    ← NUEVO
│   ├── SubResources.md                 ← NUEVO
│   ├── Multi-Tenant.md                 ← NUEVO
│   ├── ORM-STATUS.md                   ← NUEVO
│   ├── SimpleRest-API-Rest.md
│   └── AutomaticEndpoints-Summary.md
│
├── SECURITY/
│   ├── ACL.md
│   └── Authentication.md
│
├── CACHING/
│   └── Caching.md                      ← NUEVO
│
├── CLI/
│   ├── CommandLine.md
│   └── commands/
│       ├── PackCommand.md
│       └── AclCommands.md              ← NUEVO
│
├── FRONTEND/
│   ├── ViewEngine.md                   ← NUEVO
│   ├── HTML-Form-builder.md
│   └── HTML-Form-builder.AdminLTE.md
│
├── MODULARITY/
│   ├── Packages and Modules.md
│   ├── Pacakges.md                     (typo, mantenido por ref)
│   ├── ModuleProvider.md
│   └── packages/README.md              ← NUEVO
│
├── INTEGRATIONS/
│   ├── ApiClient.md
│   ├── Ollama-Models.md
│   └── i18n.md                         ← NUEVO
│
├── EVENTS/
│   └── EventBus.md                     ← NUEVO
│
├── DEV TOOLS/
│   ├── Helpers.md                      ← NUEVO
│   ├── TestingGuide.md                 ← NUEVO
│   └── unit-tests-pruebas-unitarias.md ← NUEVO
│
├── OPS/
│   ├── Deployment.md                   ← NUEVO
│   └── Performance.md                  ← NUEVO
│
├── _archive/                           ← NUEVO
│   ├── DOC-Simplerest.txt              (archivado)
│   └── extras/                         (archivado)
│
├── _internal/                          (planes, issues, TODO)
│   ├── etc/
│   ├── issues/
│   └── to-do/
│       ├── acl-*.md
│       ├── phase-*.md
│       ├── TODO *.md
│       ├── orm/
│       └── sections/
│
└── commands/
    └── PackCommand.md
```

---

## LEYENDA

| Símbolo | Significado |
|---------|-------------|
| ← NUEVO | Documento creado en esta reorganización (Mayo 2026) |
| (archivado) | Documento obsoleto movido a `_archive/` |
| (typo) | Archivo con error ortográfico en el nombre, mantenido por referencias externas |

---

## DOCUMENTACIÓN PENDIENTE

Estos temas están identificados como faltantes para documentación futura:

- **Mail System** — `Mail.php`, SendinBlue
- **Security** — SimpleCrypt, XSS, CSRF
- **Traits Reference** — los 13 traits del framework
- **Interfaces Reference** — las 24 interfaces
- **Exceptions Reference** — las 13 excepciones
- **Libs Catalog** — catálogo completo de las 101 clases Libs
- **Background Jobs** — JobQueue, Task, Cronjobs
- **Transformers / DTO** — patrones de transformación
- **Widgets / Pages** — componentes de UI
- **Files / Download** — API de archivos
- **TrashCan** — soft-delete management
- **Collections** — manejo de colecciones
- **MySelf** — API del usuario actual
- **PSR Fase 3-5** — PSR-17 (factories), PSR-15 (middleware), StreamInterface

---

**Última actualización**: 2026-05-20  
**Versión del Framework**: 1.0.0
