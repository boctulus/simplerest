# Framework Architecture - SimpleRest

Guía de la arquitectura y estructura de directorios de SimpleRest Framework.

---

## Estructura de Directorios

### Nivel Superior

```
simplerest/
├─ src/              # Framework Core (código del framework)
├─ app/              # Application Code (playground/dogfooding)
├─ modules/          # Módulos opcionales
├─ examples/         # Demos y ejemplos
├─ packages/         # Packages locales (composer)
├─ config/           # Configuración
├─ public/           # Assets públicos (index.php, CSS, JS)
├─ database/         # Migraciones y seeders
├─ scripts/          # Scripts de automatización
├─ tests/            # Pruebas unitarias
├─ vendor/           # Dependencias de Composer
├─ composer.json     # Configuración de Composer
└─ app.php           # Bootstrap de la aplicación
```

---

## 📁 src/ - Framework Core

**Propósito**: Código del framework puro, sin dependencias de aplicación.

**Namespace**: `Boctulus\Simplerest\Core\`

**Autoload**: Principal (type: `library`)

### Estructura

```
src/
└─ Core/
   ├─ API/                 # Controladores base API (v1, v2)
   │  └─ v1/
   │     ├─ ApiController.php
   │     ├─ AuthController.php
   │     ├─ ResourceController.php
   │     └─ ...
   │
   ├─ Controllers/         # Controladores base
   │  ├─ ConsoleController.php
   │  ├─ Controller.php
   │  └─ WebController.php
   │
   ├─ Exceptions/          # Excepciones del framework
   │  ├─ SqlException.php
   │  ├─ TableNotFoundException.php
   │  └─ ...
   │
   ├─ Handlers/            # Manejadores internos
   │  ├─ ApiHandler.php
   │  ├─ AuthHandler.php
   │  ├─ ErrorHandler.php
   │  ├─ MiddlewareHandler.php
   │  ├─ OutputHandler.php
   │  └─ RequestHandler.php
   │
   ├─ Helpers/             # Funciones helper del framework
   │  ├─ cache.php
   │  ├─ config.php
   │  ├─ db.php
   │  ├─ factories.php
   │  ├─ http.php
   │  ├─ url.php
   │  ├─ view.php
   │  └─ ...
   │
   ├─ Interfaces/          # Interfaces del framework
   │  ├─ IAuth.php
   │  ├─ ICache.php
   │  ├─ IDbAccess.php
   │  ├─ ISchema.php
   │  └─ ...
   │
   ├─ Libs/                # Bibliotecas del framework
   │  ├─ ApiClient.php
   │  ├─ Cache.php
   │  ├─ Config.php
   │  ├─ DB.php
   │  ├─ Logger.php
   │  ├─ Migration.php
   │  ├─ Schema.php
   │  ├─ Validator.php
   │  └─ ...
   │
   ├─ Psr7/                # Adaptadores PSR-7
   │  ├─ Request.php
   │  └─ Response.php
   │
   ├─ Templates/           # Plantillas de código (excluidas del autoload)
   │  ├─ Controller.php
   │  ├─ Model.php
   │  ├─ Migration.php
   │  └─ ...
   │
   ├─ Traits/              # Traits reutilizables
   │  ├─ ExceptionHandler.php
   │  ├─ QueryBuilderTrait.php
   │  ├─ Uuids.php
   │  └─ ...
   │
   ├─ Acl.php              # Control de acceso
   ├─ CliRouter.php        # Router CLI
   ├─ Constants.php        # Constantes del framework
   ├─ Container.php        # Contenedor de dependencias
   ├─ FrontController.php  # Front Controller
   ├─ Middleware.php       # Middleware base
   ├─ Model.php            # Modelo base
   ├─ Request.php          # Request (nativo + PSR-7)
   ├─ Response.php         # Response (nativo + PSR-7)
   ├─ ServiceProvider.php  # Service Provider base
   ├─ View.php             # Motor de vistas
   └─ WebRouter.php        # Router Web
```

---

## 📁 app/ - Application Code

**Propósito**: Código de aplicación, playground, dogfooding.

**Namespace**: `Boctulus\Simplerest\*`

**Autoload**: Dev (autoload-dev)

### Estructura

```
app/
├─ Background/          # Background jobs y cron jobs
│  ├─ Cronjobs/
│  └─ Tasks/
│
├─ Commands/            # Comandos CLI personalizados
│  ├─ MakeCommand.php
│  ├─ MigrationsCommand.php
│  └─ ...
│
├─ Controllers/         # Controladores de aplicación
│  ├─ Demos/
│  └─ ...
│
├─ DTO/                 # Data Transfer Objects
│
├─ Helpers/             # Helpers específicos de aplicación
│
├─ Interfaces/          # Interfaces de aplicación
│
├─ Libs/                # Librerías de aplicación
│
├─ Locale/              # Traducciones (i18n)
│
├─ Middlewares/         # Middlewares personalizados
│
├─ Models/              # Modelos de datos
│  ├─ main/             # DB principal
│  ├─ MyModel.php       # Modelo base personalizado
│  └─ ...
│
├─ Modules/             # Módulos de aplicación (a migrar)
│  ├─ AndroidEngine/
│  ├─ FriendlyPOS/
│  ├─ Xeni/
│  └─ ...
│
├─ Pages/               # Páginas (Page Controllers)
│  └─ admin/
│
├─ Schemas/             # Esquemas de base de datos
│  └─ main/
│
├─ Traits/              # Traits de aplicación
│
├─ Transformers/        # Transformadores de datos
│
└─ Views/               # Vistas de la aplicación
```

---

## 📁 modules/ - Módulos Opcionales

**Propósito**: Funcionalidades modulares y opcionales que extienden el framework.

**Estado**: Planeado (futuro)

**Estructura esperada**:
```
modules/
├─ Xeni/
├─ FriendlyPOS/
└─ AndroidEngine/
```

---

## 📁 examples/ - Demos y Ejemplos

**Propósito**: Ejemplos de uso y demostraciones.

**Estado**: Planeado (futuro)

**Estructura esperada**:
```
examples/
├─ Countdown/
├─ ProgressBar/
└─ StarRating/
```

---

## 📁 packages/ - Packages Locales

**Propósito**: Packages desarrollados localmente que pueden publicarse independientemente.

**Estructura actual**:
```
packages/
└─ boctulus/
   ├─ basic-acl/
   ├─ cli-test/
   ├─ exchange-rate/
   ├─ fine-grained-acl/
   ├─ friendlypos-web/
   ├─ llm-providers/
   ├─ openfactura-sdk/
   ├─ web-test/
   └─ zippy/
```

**Características**:
- ✅ Cada package tiene su propio `composer.json`
- ✅ Pueden publicarse independientemente
- ✅ Siguen PSR-4
- ✅ Reutilizables entre proyectos

---

## 📁 config/ - Configuración

Archivos de configuración del framework y aplicación:

```
config/
├─ config.php           # Configuración principal
├─ constants.php        # Constantes globales
├─ autoload.php         # Archivos a cargar automáticamente
└─ ...
```

---

## 📁 public/ - Assets Públicos

**Document Root** del servidor web:

```
public/
├─ index.php            # Entry point
├─ css/
├─ js/
├─ images/
├─ components/          # Componentes frontend
└─ ...
```

---

## 📁 database/ - Migraciones

```
database/
├─ migrations/          # Migraciones de BD
└─ seeders/            # Seeders
```

---

## 📁 scripts/ - Scripts de Automatización

Scripts para tareas administrativas y automatización:

```
scripts/
├─ init/               # Scripts de inicialización
├─ tmp/                # Scripts temporales
└─ ...
```

---

## 📁 tests/ - Pruebas Unitarias

Pruebas del framework y aplicación:

```
tests/
├─ unit-tests/         # Pruebas unitarias específicas
├─ DB_TransactionTest.php
├─ ModelTest.php
├─ ValidatorTest.php
├─ WebRouterTest.php
└─ ...
```

---

## Composer Autoloading

### Configuración Actual

```json
{
  "type": "library",
  "autoload": {
    "psr-4": {
      "Boctulus\\Simplerest\\": "src/",
      "Boctulus\\FineGrainedACL\\": "packages/boctulus/fine-grained-acl/src",
      "Boctulus\\BasicACL\\": "packages/boctulus/basic-acl/src",
      "Boctulus\\Zippy\\": "packages/boctulus/zippy/src",
      ...
    },
    "exclude-from-classmap": [
      "src/Core/Templates/"
    ]
  },
  "autoload-dev": {
    "psr-4": {
      "Boctulus\\Simplerest\\Tests\\": "tests/",
      "Boctulus\\Simplerest\\": "app/"
    }
  }
}
```

### Reglas de Autoloading

1. **Framework Core** (`src/`):
   - Namespace: `Boctulus\Simplerest\Core\`
   - Cargado siempre (autoload principal)
   - Type: `library`

2. **Application Code** (`app/`):
   - Namespace: `Boctulus\Simplerest\*`
   - Cargado solo en desarrollo (autoload-dev)
   - Puede coincidir con namespaces del Core (por compatibilidad)

3. **Packages** (`packages/`):
   - Cada uno con su propio namespace
   - Registrados individualmente en autoload

4. **Tests** (`tests/`):
   - Namespace: `Boctulus\Simplerest\Tests\`
   - Solo en desarrollo (autoload-dev)

---

## Principios de Arquitectura

### 1. Separation of Concerns

```
src/   → Framework puro, genérico, reutilizable
app/   → Aplicación específica, dogfooding
app/packages/ → Extensiones modulares
app/modules/ → Extensiones modulares
```

### 2. Dependency Rule

```
Framework (src/) ← NO DEBE depender de → Application (app/)
Application (app/) → PUEDE usar → Framework (src/)
```

### 3. Single Responsibility

Cada directorio tiene una responsabilidad clara y única.

### 4. Open/Closed Principle

El framework está abierto a extensión (via packages, modules) pero cerrado a modificación.

---


**Autor**: Pablo Bozzolo (boctulus)
**Software Architect**
