# Packages y Modules en SimpleREST

## Tabla de Contenidos

1. [Introducción](#introducción)
2. [Diferencias entre Packages y Modules](#diferencias-entre-packages-y-modules)
3. [Packages](#packages)
4. [Modules](#modules)
5. [Análisis Técnico Comparativo](#análisis-técnico-comparativo)
6. [Casos de Uso Óptimos](#casos-de-uso-óptimos)

---

## Introducción

SimpleREST soporta dos mecanismos para organizar y reutilizar código: **Packages** y **Modules**. Aunque comparten estructura de directorios similar, tienen propósitos y características diferentes.

### Diferencias Clave

**Packages:**
- Similares a los de Composer
- Almacenados fuera de `App/`, en la raíz del proyecto (`packages/`)
- Reutilizables y más independientes
- Pensados para distribución externa
- Versionado mediante Composer
- Menor acoplamiento con el framework

**Modules:**
- Dentro de `App/Modules/`
- Más integrados con el framework
- Autocontenidos y útiles para desarrollo rápido
- Especialmente útiles al portar tu framework a entornos como WordPress
- Alto acoplamiento con el ciclo de vida del framework
- Mayor velocidad de desarrollo

---

## Diferencias entre Packages y Modules

### 1. Concepto y Alcance

| Aspecto | Module | Package |
|---------|--------|---------|
| **Naturaleza** | Parte del sistema principal, se integra directamente en el ciclo de vida del framework. | Proyecto autónomo, gestionado como dependencia externa. |
| **Ubicación** | `App/Modules/NombreModulo/` | `packages/autor/nombre-package/` |
| **Acoplamiento** | Alto: depende del framework y sus convenciones internas. | Bajo: puede incluir sus propios namespaces, service providers, autoloaders. |
| **Portabilidad** | Pensado para moverse dentro del ecosistema del framework (p. ej. entre instancias de SimpleREST o hacia la versión WordPress). | Pensado para distribuirse a otros proyectos o frameworks (vía Composer, Packagist, Git). |
| **Velocidad de desarrollo** | Muy alta. Ideal para prototipado rápido o funcionalidades específicas. | Más lenta, requiere configuración y empaquetado. |
| **Dependencias** | Usa las del framework. | Puede declarar las suyas propias en su `composer.json`. |

### 2. Ciclo de Vida Típico

| Etapa | Module | Package |
|-------|--------|---------|
| **Creación** | Se crea directamente dentro del proyecto (comando `make module` o manual). | Se inicializa con comando `make package` que genera estructura completa. |
| **Carga** | Autocargado por el framework (por convención o registro interno). | Cargado por Composer, usando PSR-4 autoloading. |
| **Ejecución** | Participa directamente en el ciclo de petición/respuesta del framework. | Puede ejecutar código propio a través de providers o hooks. |
| **Actualización** | Se modifica directamente en el código base. | Se actualiza mediante versiones (composer update, semver). |
| **Distribución** | Manual (copiar carpeta o sincronizar). | Automatizada (repositorio o paquete publicado). |

### 3. Ventajas y Desventajas

#### Ventajas de los Modules

✅ Integración directa y ligera (sin dependencias externas)
✅ Mayor velocidad de carga y ejecución (menos overhead)
✅ Ideal para casos específicos del proyecto o desarrollo dentro del mismo ecosistema
✅ Permiten empaquetar funcionalidades completas (rutas, vistas, controladores) sin necesidad de publicación externa
✅ Reutilización simple: basta con copiar el módulo a otra instancia de tu framework (sin Composer)

#### Desventajas de los Modules

❌ Menor independencia: dependen del contexto del framework (no funcionan fuera)
❌ Dificultan la distribución pública o versionado independiente
❌ Riesgo de duplicación de código si el mismo módulo se usa en varios proyectos
❌ Difícil aplicar control de versiones fino (no tienen su propio ciclo semántico)

#### Ventajas de los Packages

✅ Independencia total del proyecto principal
✅ Versionado y distribución profesional (Composer, Git)
✅ Reutilización fácil entre proyectos diferentes (no necesariamente SimpleREST)
✅ Control de dependencias, autoloading y configuración propios
✅ Posibilidad de tests unitarios aislados, CI/CD, documentación independiente

#### Desventajas de los Packages

❌ Carga ligeramente más lenta (autoloaders adicionales, dependencias)
❌ Ciclo de desarrollo más pesado (publicación, instalación, versiones)
❌ Menos ágiles para iteración rápida
❌ No siempre pueden integrarse fácilmente en entornos "embebidos" como WordPress sin adaptadores

---

## Packages

### Creación de un Package

Los packages se crean automáticamente con el comando `make package`:

```bash
php com make package <nombre-package> <autor> [destino]
```

**Ejemplo:**

```bash
php com make package api-client boctulus
```

Esto creará la estructura completa en `packages/boctulus/api-client/`.

### Estructura Generada Automáticamente

El comando crea automáticamente:

```
packages/boctulus/api-client/
├── assets/
│   ├── css/
│   ├── img/
│   ├── js/
│   └── third_party/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── etc/
├── logs/
├── src/
│   ├── Controllers/
│   ├── Helpers/
│   ├── Interfaces/
│   ├── Libs/
│   ├── Middlewares/
│   ├── Models/
│   ├── Traits/
│   ├── ServiceProvider.php      # ← Generado automáticamente
│   └── ExampleInterface.php     # ← Generado automáticamente
├── tests/
├── views/
├── composer.json                 # ← Generado automáticamente
├── README.md                     # ← Generado automáticamente
└── LICENSE                       # ← Generado automáticamente (MIT)
```

### Archivos Generados Automáticamente

#### 1. `composer.json`

El comando genera automáticamente el `composer.json` con la configuración PSR-4:

```json
{
    "name": "boctulus/api-client",
    "description": "Package api-client by boctulus",
    "type": "library",
    "autoload": {
        "psr-4": {
            "Boctulus\\ApiClient\\": "src/"
        }
    },
    "extra": {
        "simplerest": {
            "providers": [
                "Boctulus\\ApiClient\\ServiceProvider"
            ]
        }
    },
    "require": {}
}
```

#### 2. `ServiceProvider.php`

Se genera automáticamente un ServiceProvider base en `src/ServiceProvider.php`.

#### 3. `README.md`

Se genera con instrucciones completas de instalación y uso del package.

#### 4. `LICENSE`

Se genera una licencia MIT por defecto.

### Instalación de un Package

Una vez creado el package, hay dos formas de instalarlo:

#### Opción 1: Repositorio Local (Recomendado para desarrollo)

1. **Agregar el repositorio en `composer.json` (raíz del proyecto):**

```json
"repositories": [
    {
        "type": "path",
        "url": "packages/boctulus/api-client",
        "options": {
            "symlink": true
        }
    }
],
"require": {
    "boctulus/api-client": "@dev"
}
```

2. **Actualizar Composer:**

```bash
composer clear-cache
composer update
composer dump-autoload -o
```

#### Opción 2: Agregar Manualmente el Autoload

Si prefieres no usar el sistema de repositorios de Composer:

1. **Agregar el mapeo PSR-4 en `composer.json` (raíz):**

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Boctulus\\ApiClient\\": "packages/boctulus/api-client/src/"
    }
}
```

2. **Regenerar el autoloader:**

```bash
composer dump-autoload --no-ansi
```

### Registrar el Service Provider

Agregar el ServiceProvider en `config/config.php`:

```php
/*
    Service Providers
*/

'providers' => [
    Boctulus\ApiClient\ServiceProvider::class,
    // ... otros providers
],
```

### Uso del Package

#### Service Provider

Un Service Provider debe extender la clase `ServiceProvider` e implementar los métodos `boot()` y `register()`:

```php
namespace Boctulus\ApiClient;

use Boctulus\Simplerest\Core\ServiceProvider;

class ApiClientServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Código que se ejecuta al inicio de la aplicación
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Registrar servicios, rutas, etc.
        // Ejemplo: cargar archivo de rutas
        require_once __DIR__.'/../config/routes.php';
    }
}
```

#### Definir Rutas

Las rutas del package se definen típicamente en `config/routes.php`:

```php
use Boctulus\Simplerest\Core\WebRouter;
use Boctulus\ApiClient\Controllers\ExampleController;

WebRouter::get('api-client/example', ExampleController::class . '@index');
```

#### Controladores

Los controladores se crean en `src/Controllers/` con el namespace correspondiente:

```php
namespace Boctulus\ApiClient\Controllers;

class ExampleController
{
    public function index()
    {
        return ['message' => 'Hello from ApiClient package'];
    }
}
```

#### Modelos

Los modelos se ubican en `src/Models/`:

```php
namespace Boctulus\ApiClient\Models;

use Boctulus\Simplerest\Core\Model;

class ExampleModel extends Model
{
    protected $table = 'examples';
}
```

### Configuración

Incluir archivos de configuración es tan simple como usar `require()` o `require_once()` en el ServiceProvider:

```php
public function register()
{
    $config = require __DIR__.'/../config/config.php';
    // Usar la configuración
}
```

### Migraciones

Para ejecutar migraciones dentro de un package, usa el parámetro `--dir`:

```bash
php com migrate --dir=packages/boctulus/api-client/database/migrations
```

### Notas Importantes

- Los paquetes funcionan **solamente** con rutas y **no** con el FrontController
- Actualmente el soporte cubre rutas con funciones anónimas y controladores
- Falta agregar soporte completo para migraciones, traducciones, vistas, etc. (en desarrollo)

---

## Modules

### Creación de un Module

Los modules se crean con el comando `make module`:

```bash
php com make module <nombre-module> [opciones]
```

**Opciones disponibles:**
- `-f, --force`: Sobrescribir si ya existe
- `-r, -d, --remove, --delete`: Eliminar el module

**Ejemplo:**

```bash
php com make module tax-calculator
```

Esto creará la estructura completa en `App/Modules/tax-calculator/`.

### Estructura Generada

```
App/Modules/tax-calculator/
├── assets/
│   ├── css/
│   ├── img/
│   ├── js/
│   └── third_party/
├── config/
│   └── config.php          # ← Archivo de configuración por defecto
├── database/
│   ├── migrations/
│   └── seeders/
├── etc/
├── logs/
├── src/
│   ├── Controllers/
│   ├── Helpers/
│   ├── Interfaces/
│   ├── Libs/
│   ├── Middlewares/
│   ├── Models/
│   └── Traits/
├── tests/
├── views/
└── tax-calculator.php      # ← Archivo principal del módulo
```

### Uso de Plantillas

Si existe una carpeta de plantillas en `app/templates/class_templates/Module/`, el comando copiará y parseará automáticamente esos archivos en lugar de crear archivos por defecto.

Esto permite tener templates pre-configurados para diferentes tipos de módulos.

### Eliminación de un Module

```bash
php com make module tax-calculator --remove
# o
php com make module tax-calculator -d
```

### Forzar Sobrescritura

```bash
php com make module tax-calculator --force
# o
php com make module tax-calculator -f
```

### Integración con el Framework

Los modules están más integrados con el framework que los packages:

1. **Ubicación:** Dentro de `App/Modules/`, participan del namespace principal
2. **Autoloading:** Pueden usar el autoloader principal del framework
3. **Configuración:** Acceso directo a la configuración del framework
4. **Recursos:** Pueden acceder directamente a helpers, traits y librerías del framework

### Uso Típico en WordPress

Los modules son especialmente útiles cuando se porta SimpleREST a WordPress:

```php
// En un plugin de WordPress
require_once MODULES_PATH . 'tax-calculator/tax-calculator.php';

// El módulo puede tener toda su lógica encapsulada
TaxCalculator::init();
```

### Acceso a Constantes

Los modules tienen acceso directo a constantes del framework:

```php
// Dentro de un module
require_once MODULES_PATH . 'otro-modulo/config/config.php';
Files::download($url, MODULES_PATH . "tax-calculator/assets/js");
```

---

## Análisis Técnico Comparativo

### Cuando Usar Packages

| Situación | Razón |
|-----------|-------|
| Librería general para compartir entre varios proyectos o frameworks | Independencia y portabilidad |
| Código que debe tener versiones, pruebas y distribución pública | Control de versiones con Composer |
| Funcionalidad reutilizable que puede vivir fuera del framework | Bajo acoplamiento |
| Necesitas gestionar dependencias específicas | Composer gestiona dependencias |
| Planeas publicar en Packagist o repositorio Git | Diseñado para distribución |

**Ejemplo:** Una librería de integración con APIs externas (PayPal, Stripe, etc.)

### Cuando Usar Modules

| Situación | Razón |
|-----------|-------|
| Desarrollo rápido o prototipo dentro de un proyecto existente | Alta velocidad de desarrollo |
| Plugin para WordPress basado en tu framework | Fácil de copiar entre instancias |
| Funcionalidad específica del proyecto actual | No necesita independencia |
| Integración profunda con el ciclo de vida del framework | Alto acoplamiento es ventaja |
| Desarrollo interno sin necesidad de distribución externa | Simplicidad |

**Ejemplo:** Un módulo de cálculo de impuestos específico para tu aplicación de e-commerce

---

## Casos de Uso Óptimos

### Scenario 1: E-commerce con Múltiples Instancias

**Situación:** Tienes varios sitios de e-commerce que comparten funcionalidades comunes.

**Solución:**
- **Package:** Sistema de carrito de compras (`packages/miempresa/shopping-cart`)
  - Reutilizable entre todos los proyectos
  - Versionado independiente
  - Actualizable vía Composer

- **Module:** Cálculo de impuestos específico por país (`App/Modules/tax-argentina`)
  - Lógica específica de cada instancia
  - No necesita distribución
  - Rápido de adaptar y modificar

### Scenario 2: WordPress + SimpleREST

**Situación:** Estás creando plugins de WordPress basados en SimpleREST.

**Solución:**
- **Module:** La funcionalidad principal del plugin (`App/Modules/seo-optimizer`)
  - Se integra fácilmente con WordPress
  - Se puede copiar a diferentes plugins
  - Acceso directo al framework

- **Package:** Utilidades compartidas entre plugins (`packages/miempresa/wp-helpers`)
  - Reutilizable entre múltiples plugins
  - Mantenimiento centralizado

### Scenario 3: API REST con Microservicios

**Situación:** Estás construyendo una API que se conecta a varios microservicios.

**Solución:**
- **Package:** Clientes de API para servicios externos (`packages/miempresa/payment-gateway-client`)
  - Puede usarse en otros proyectos
  - Tests unitarios aislados
  - Documentación independiente

- **Module:** Lógica de negocio específica (`App/Modules/order-processor`)
  - Lógica específica de esta API
  - Integrada con el flujo de la aplicación

---

## Resumen Ejecutivo

### Usa PACKAGES cuando:
✅ Necesites **reutilización** entre proyectos
✅ Requieras **versionado** semántico
✅ Planees **distribución** pública o privada vía Composer
✅ Necesites **independencia** del framework
✅ Quieras **tests aislados** y CI/CD independiente

### Usa MODULES cuando:
✅ Necesites **desarrollo rápido** y ágil
✅ La funcionalidad sea **específica del proyecto**
✅ Requieras **integración profunda** con el framework
✅ Trabajes en entornos **WordPress** o similares
✅ Quieras **simplicidad** sin overhead de Composer

---

## Referencias

- [How to Create a Laravel Package](https://devdojo.com/devdojo/how-to-create-a-laravel-package)
- MakeCommand.php:2667 (función `module()`)
- MakeCommand.php:2748 (función `package()`)
- config/constants.php (definición de MODULES_PATH y PACKAGES_PATH)
