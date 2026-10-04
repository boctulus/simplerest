# Arquitectura del Framework — SimpleRest

## Filosofía

SimpleRest es un framework PHP modular,高性能 y liviano (3-10ms bootstrap). Sigue una filosofía **"Laravel-like syntax, minimal overhead"** y **"zero-magic"**: explícito sobre implícito, arrays sobre objetos pesados.

Separación clara entre **Framework Core** (`src/framework/`) y **Application Code** (`app/`).

---

## Estructura de Directorios

```
simplerest/
│
├── index.php                    # Entry point HTTP
├── app.php                      # Bootstrap (autoload, env, helpers, providers)
├── com                          # Entry point CLI
├── composer.json                # Dependencias
├── .env                         # Variables de entorno
│
├── src/framework/               # ⬅ NÚCLEO DEL FRAMEWORK
│   ├── FrontController.php      # Dispatcher: pipeline de 6 handlers
│   ├── WebRouter.php            # Router HTTP
│   ├── CliRouter.php            # Router CLI
│   ├── Request.php              # Request singleton
│   ├── Response.php             # Response singleton
│   ├── Model.php                # Modelo base
│   ├── View.php                 # Motor de vistas
│   ├── Middleware.php           # Middleware base
│   ├── Container.php            # DI Container
│   ├── ServiceProvider.php      # Service Provider base
│   │
│   ├── Api/                     # Controladores API
│   │   ├── ApiController.php    # CRUD REST abstracto
│   │   ├── AuthController.php   # JWT Auth
│   │   ├── ResourceController.php
│   │   ├── Collections.php
│   │   ├── Download.php
│   │   ├── Files.php
│   │   ├── MySelf.php
│   │   └── TrashCan.php
│   │
│   ├── Controllers/             # Controladores base
│   │   ├── Controller.php       # Base abstracta
│   │   ├── ConsoleController.php
│   │   └── WebController.php
│   │
│   ├── Handlers/                # Pipeline de 6 handlers
│   │   ├── RequestHandler.php
│   │   ├── ApiHandler.php
│   │   ├── AuthHandler.php
│   │   ├── OutputHandler.php
│   │   ├── MiddlewareHandler.php
│   │   └── ErrorHandler.php
│   │
│   ├── Security/                # ACL completo
│   │   ├── Acl.php
│   │   ├── Contracts/
│   │   ├── Engine/
│   │   ├── Domain/
│   │   ├── Service/
│   │   ├── Explanation/
│   │   ├── Snapshot/
│   │   └── Compiler/
│   │
│   ├── Libs/                    # 101 clases de utilidad
│   │   ├── DB.php               # Query Builder
│   │   ├── Schema.php           # Schema Builder
│   │   ├── Validator.php        # Validador
│   │   ├── ApiClient.php        # Cliente HTTP
│   │   ├── Cache.php (+ DBCache, FileCache, InMemoryCache)
│   │   ├── Migration.php        # Migraciones base
│   │   ├── Paginator.php        # Paginación
│   │   ├── Mail.php             # Email
│   │   ├── EventBus.php         # Observer pattern
│   │   ├── CommandRegistry.php  # Auto-descubrimiento CLI
│   │   ├── Session.php          # Sesiones
│   │   ├── Logger.php           # Logging
│   │   └── ... (+90 más)
│   │
│   ├── Helpers/                 # 21 archivos de funciones globales
│   ├── Traits/                  # 13 traits reutilizables
│   ├── Interfaces/              # 24 interfaces
│   ├── Exceptions/              # 13 excepciones
│   ├── Psr7/                    # Adaptadores PSR-7
│   └── Templates/               # 26 stubs de generación de código
│
├── app/                         # ⬅ CÓDIGO DE APLICACIÓN
│   ├── Commands/                # 120+ comandos CLI en grupos
│   │   ├── acl/                 # 24 comandos ACL
│   │   ├── make/                # 42 generadores
│   │   ├── sql/                 # 12 comandos SQL
│   │   ├── migrations/          # 16 comandos migraciones
│   │   ├── users/               # 12 comandos usuarios
│   │   ├── system/              # 2 comandos sistema
│   │   ├── test/                # 4 comandos test
│   │   └── ...
│   ├── Controllers/Api/         # 185 controladores API auto-generados
│   ├── Controllers/             # Controladores HTTP adicionales
│   ├── Models/                  # Modelos de aplicación
│   ├── Middlewares/              # Middlewares personalizados
│   ├── Schemas/                 # Definiciones de esquemas de BD
│   ├── Modules/                 # 18 módulos de aplicación
│   ├── Libs/                    # Librerías de aplicación
│   ├── Traits/                  # Traits de aplicación
│   ├── Helpers/                 # Helpers de aplicación
│   ├── Pages/                   # Renderizadores de páginas
│   ├── Views/                   # Plantillas de vista
│   ├── Widgets/                 # Componentes widget
│   ├── Transformers/            # Transformers de datos
│   ├── DTO/                     # Data Transfer Objects
│   ├── Interfaces/              # Interfaces de aplicación
│   ├── Locale/                  # Traducciones i18n
│   └── Background/              # Tareas background
│       ├── Cronjobs/
│       └── Tasks/
│
├── packages/boctulus/           # ⬅ 11 PAQUETES LOCALES
│   ├── fine-grained-acl/        # ACL avanzado
│   ├── basic-acl/               # ACL básico
│   ├── zippy/                   # App completa
│   ├── cli-test/                # Testing CLI
│   ├── web-test/                # Testing web
│   ├── llm-providers/           # OpenAI, Claude
│   ├── exchange-rate/           # Tipo de cambio
│   ├── friendlypos-web/         # POS web
│   ├── openfactura-sdk/         # Factura electrónica Chile
│   ├── shopifyconnector/        # Shopify
│   └── dummyapi/                # API dummy
│
├── config/                      # ⬅ CONFIGURACIÓN
│   ├── config.php               # Configuración maestra
│   ├── constants.php            # Constantes de ruta
│   ├── routes.php               # Rutas HTTP
│   ├── cli_routes.php           # Rutas CLI
│   ├── databases.php            # Conexiones de base de datos
│   ├── acl.php                  # Configuración ACL
│   ├── middlewares.php          # Registro de middlewares
│   ├── cors.php                 # Configuración CORS
│   ├── messages.php             # Mensajes de error
│   ├── commands.php             # Registro de comandos
│   ├── autoload.php             # Autoload adicional
│   └── woocommerce.php          # Config WooCommerce
│
├── database/                    # Migraciones + Seeders
├── public/                      # Web root (index.php, assets)
├── storage/                     # Cache, archivos
├── logs/                        # Logs
├── tests/                       # Tests unitarios
├── scripts/                     # Scripts de automatización
├── docker/                      # Configuración Docker
├── vendor/                      # Dependencias Composer
└── docs/                        # Documentación
```

---

## Pipeline de Petición HTTP

```
index.php
  → app.php (bootstrap)
  → WebRouter::compile()           # Registra rutas
  → WebRouter::resolve()           # Resuelve URL → Controlador
  → CliRouter::compile() + resolve()
  → FrontController::resolve()     # Pipeline de 6 handlers:
       1. RequestHandler    → Parsea request
       2. ApiHandler        → Resuelve rutas /api/*
       3. AuthHandler       → Resuelve rutas /auth
       4. OutputHandler     → Formatea respuesta
       5. MiddlewareHandler → Ejecuta middlewares
       6. ErrorHandler      → Maneja errores
```

## Pipeline CLI

```
com {grupo} {comando} [args] [--opts]
  → app.php (bootstrap)
  → CliRouter::resolve()
  → CommandRegistry::discover()   # Auto-descubre app/Commands/{grupo}/*.php
  → Ejecuta handle()
```

---

## Principios Arquitectónicos

| Principio | Descripción |
|-----------|-------------|
| **Front Controller** | Single entry point (`index.php`) → dispatcher pipeline |
| **MVC-lite** | Model → arrays (no objetos pesados), Controller, View minimalista |
| **Service Provider** | Paquetes se registran via `IServiceProvider` |
| **Command Pattern** | CLI commands auto-descubiertos |
| **Observer (EventBus)** | Eventos y hooks de ciclo de vida |
| **Template Method** | Model hooks (`boot()`, `onReading()`, `onCreating()`) |
| **Singleton** | Request, Response, WebRouter, CliRouter |
| **Strategy** | Handlers plugueables en FrontController |

---

## DB Engines Soportados

MySQL, PostgreSQL, SQLite, SQL Server (MSSQL), Oracle, Firebird, DB2, Informix, Sybase

---

## Autor

Pablo Bozzolo (boctulus) — Software Architect
