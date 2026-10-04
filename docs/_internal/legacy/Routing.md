# Routing en SimpleRest Framework

## Tabla de Contenidos

- [Introducción](#introducción)
- [Controladores](#controladores)
- [WebRouter](#webrouter)
  - [Definición de Rutas](#definición-de-rutas)
  - [Rutas con Parámetros](#rutas-con-parámetros)
  - [Grupos de Rutas](#grupos-de-rutas)
  - [Funciones Anónimas](#funciones-anónimas)
  - [Ordenamiento Automático](#ordenamiento-automático)
- [CliRouter](#clirouter)
  - [Uso Básico](#uso-básico)
  - [Comandos con Funciones Anónimas](#comandos-con-funciones-anónimas)
  - [Comandos Multi-palabra](#comandos-multi-palabra)
  - [Soporte de Métodos Mágicos (__call)](#soporte-de-métodos-mágicos-__call)
  - [Grupos de Comandos](#grupos-de-comandos)
- [Routing en Packages](#routing-en-packages)
  - [Configuración de Rutas Web](#configuración-de-rutas-web)
  - [Configuración de Rutas CLI](#configuración-de-rutas-cli)
  - [Configuración de Package](#configuración-de-package)
- [Routing en Modules](#routing-en-modules)
  - [Diferencias entre Modules y Packages](#diferencias-entre-modules-y-packages)
  - [Estructura de un Module](#estructura-de-un-module)
  - [Configuración de Rutas Web en Modules](#configuración-de-rutas-web-en-modules)
  - [ModuleProvider](#moduleprovider)
  - [Registro del Module](#registro-del-module)
  - [Autoloading de Modules](#autoloading-de-modules)
  - [Ventajas de los Modules](#ventajas-de-los-modules)
  - [Cuándo usar Modules vs Packages](#cuándo-usar-modules-vs-packages)
- [Front Controller](#front-controller)
- [Arquitectura de Handlers](#arquitectura-de-handlers)
  - [Concepto](#concepto)
  - [Beneficios](#beneficios)
  - [Los 6 Handlers](#los-6-handlers)
  - [Configuración](#configuración)
  - [Flujo de Ejecución](#flujo-de-ejecución)
  - [Crear un Handler Personalizado](#crear-un-handler-personalizado)
  - [Handler vs Traits](#handler-vs-traits)
  - [Casos de Uso Avanzados](#casos-de-uso-avanzados)
  - [Testing de Handlers](#testing-de-handlers)
  - [Migración desde FrontController Antiguo](#migración-desde-frontcontroller-antiguo)
  - [Soporte de __call() en FrontController](#soporte-de-__call-en-frontcontroller)
- [Errores Comunes](#errores-comunes)
  - [Comando no encontrado](#comando-no-encontrado)
  - [Método mágico __call() no funciona](#método-mágico-__call-no-funciona)
  - [Handler personalizado no se carga](#handler-personalizado-no-se-carga)
  - [Ruta web no responde](#ruta-web-no-responde)
  - [Argumentos incorrectos](#argumentos-incorrectos)
  - [Conflicto de rutas](#conflicto-de-rutas)
- [Mejores Prácticas](#mejores-prácticas)
- [Referencias](#referencias)
- [Changelog - Mejoras Recientes](#changelog---mejoras-recientes)
- [Comparación con Laravel Routing](#comparacion-con-laravel-routing)

---

## Introducción

SimpleRest Framework ofrece un sistema de routing flexible que soporta tanto rutas web (HTTP) como rutas de consola (CLI). El sistema permite definir rutas mediante controladores o funciones anónimas.

### Componentes principales:

- **WebRouter**: Maneja rutas HTTP con soporte para verbos GET, POST, PUT, PATCH, DELETE, OPTIONS y QUERY
- **CliRouter**: Maneja comandos de consola con soporte para comandos simples y multi-palabra
- **FrontController**: Sistema simplificado principalmente para uso en terminal

---

## Controladores

Los controladores son clases cuyos métodos ejecutan acciones al ser invocados desde el FrontController o el Router.

### Ejemplo básico:

```php
<?php

namespace Boctulus\Simplerest\Controllers;

class DumbController extends Controller
{
    function add($a, $b)
    {
        $res = (int) $a + (int) $b;
        return "$a + $b = " . $res;
    }
}
```

### Ejecución desde consola:

```bash
php com dumb add 1 6
```

**Nota**: Hay soporte para controladores en sub-directorios (ej: `Controllers/Admin/UserController.php`)

---

## WebRouter

### Configuración

El WebRouter se habilita desde `config/config.php` y se configura en `config/routes.php`.

```php
'web_router' => true,
```

### Definición de Rutas

#### Métodos estándar

```php
// Rutas básicas
WebRouter::get('/usuario/{id}', 'UserController@show');
WebRouter::post('/producto', 'ProductController@store');
WebRouter::put('/producto/{id}', 'ProductController@update');
WebRouter::delete('/producto/{id}', 'ProductController@destroy');
WebRouter::query('/producto/search', 'ProductController@search');
```

#### Usando `fromArray()`

Permite definir múltiples rutas en un solo llamado:

```php
WebRouter::fromArray([
    'GET:/speed_check' => 'SpeedCheckController@index',
    'POST:/producto' => 'ProductController@store',
    'QUERY:/producto/search' => 'ProductController@search',
    '/ping' => 'SystemController@ping' // Todos los verbos
]);
```

### Rutas con Parámetros

```php
// Ruta: /user/123
WebRouter::get('user/{id}', 'UserController@show')
    ->where(['id' => '[0-9]+']);

// Múltiples parámetros
WebRouter::get('calc/sum/{a}/{b}', function($a, $b) {
    return "La suma de $a y $b es " . ($a + $b);
})->where(['a' => '[0-9]+', 'b' => '[0-9]+']);
```

### Grupos de Rutas

```php
WebRouter::group('admin', function() {
    WebRouter::get('dashboard', 'AdminController@dashboard');
    WebRouter::get('settings', 'AdminController@settings');
    WebRouter::get('users', 'AdminController@users');
});

// Las rutas resultantes:
// /admin/dashboard
// /admin/settings
// /admin/users
```

### Funciones Anónimas

```php
WebRouter::get('system/info', function() {
    return [
        'php_version' => PHP_VERSION,
        'framework' => 'SimpleRest v1.0'
    ];
});

WebRouter::delete('cache/{key}', function($key) {
    return "Deleting cache key: $key";
});
```

### Ordenamiento Automático

**✨ Nuevo**: El WebRouter ordena automáticamente las rutas de más específica a más general.

No es necesario preocuparse por el orden al definir rutas:

```php
// Antes (orden manual requerido):
WebRouter::get('admin/users/active', 'AdminController@activeUsers');
WebRouter::get('admin/users/{id}', 'AdminController@showUser');
WebRouter::get('admin/users', 'AdminController@listUsers');

// Ahora (orden automático):
WebRouter::get('admin/users', 'AdminController@listUsers');
WebRouter::get('admin/users/{id}', 'AdminController@showUser');
WebRouter::get('admin/users/active', 'AdminController@activeUsers');
// ✅ El router las ordena automáticamente para evitar conflictos
```

**Criterios de ordenamiento:**

1. Mayor número de literales (segmentos sin placeholders)
2. Mayor número de segmentos totales
3. Menor número de parámetros dinámicos `{param}`

---

## CliRouter

### Configuración

El CliRouter se habilita desde `config/config.php` y se configura en `config/cli_routes.php`.

```php
'console_router' => true,
```

### Uso Básico

```bash
php com {comando} [argumentos]
```

#### Definir comandos en `config/cli_routes.php`:

```php
// Comando con controlador
CliRouter::command('dbdriver', 'Boctulus\Simplerest\Controllers\DumbController@db_driver');

// Comando con función anónima
CliRouter::command('version', function() {
    return 'SimpleRest Framework v1.0.0';
});
```

#### Ejecución:

```bash
php com version
# Salida: SimpleRest Framework v1.0.0

php com dbdriver
# Ejecuta el método db_driver del DumbController
```

### Comandos con Funciones Anónimas

```php
// Sin parámetros
CliRouter::command('version', function() {
    return 'SimpleRest Framework v1.0.0';
});

// Con parámetros
CliRouter::command('pow', function($num, $exp) {
    return pow($num, $exp);
});

// Con validación
CliRouter::command('user:create', function($name, $email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Error: Email inválido";
    }
    return "Usuario $name creado con email $email";
});
```

#### Ejecución:

```bash
php com pow 2 8
# Salida: 256

php com user:create Juan juan@example.com
# Salida: Usuario Juan creado con email juan@example.com
```

### Comandos Multi-palabra

**✨ Nuevo**: Soporte para comandos con múltiples palabras separadas por espacios.

```php
CliRouter::command('cache:clear', 'CacheController@clear');
CliRouter::command('db:migrate', 'MigrationController@migrate');
CliRouter::command('db:seed', 'MigrationController@seed');
```

#### Ejecución:

```bash
php com cache:clear
php com db:migrate
php com db:seed

# También funciona con espacios:
php com cache clear
php com db migrate
php com db seed
```

**Nota**: Internamente los espacios se convierten a `:` para el matching.

### Soporte de Métodos Mágicos (__call)

**✨ Nuevo**: CliRouter ahora soporta el método mágico `__call()` en controladores.

Cuando un controlador tiene el método `__call()`, el CliRouter permite invocar métodos que no existen físicamente en la clase:

#### Ejemplo: WhatsappController

```php
<?php

namespace Boctulus\Simplerest\Controllers;

class WhatsappController extends Controller
{
    function __call($name, $arguments)
    {
        // Permitir números de teléfono como métodos
        if (is_numeric($name)) {
            return $this->createLink($name, ...$arguments);
        }

        // Permitir alias=valor
        if (Strings::contains("=", $name)) {
            $alias = Strings::after($name, "=");
            return $this->index($alias);
        }
    }

    private function createLink($phone, $message = null)
    {
        if ($message) {
            return "https://api.whatsapp.com/send?phone=$phone&text=$message";
        }
        return "https://wa.me/$phone";
    }
}
```

#### Uso:

```bash
# Método "mágico" con número como nombre
php com whatsapp 333333333 'Hola!'
# Resultado: https://api.whatsapp.com/send?phone=333333333&text=Hola!

# Método "mágico" con formato especial
php com whatsapp alias=ph
# Resultado: https://wa.me/639620738513

# Método real existente
php com whatsapp getPhone es
# Resultado: +34 644 149161
```

**Ventajas**:
- ✅ Permite APIs más flexibles en CLI
- ✅ Acepta parámetros que no son nombres de métodos válidos en PHP
- ✅ Ideal para DSLs (Domain Specific Languages) en consola
- ✅ Compatible con el fallback del CliRouter

**Cómo funciona**:

El CliRouter verifica si el controlador tiene `__call()` antes de decidir si un parámetro es un método o un argumento:

```php
// En CliRouter.php (líneas 203-218)
if (isset($params[1])) {
    if (method_exists($controller, $params[1])) {
        // Método real existe
        $action = $params[1];
        $actionParams = array_slice($params, 2);
    } elseif (method_exists($className, '__call')) {
        // Tiene __call(), tratar segundo parámetro como método
        $action = $params[1];
        $actionParams = array_slice($params, 2);
    } else {
        // No existe ni método ni __call(), es argumento de index()
        $action = 'index';
        $actionParams = array_slice($params, 1);
    }
}
```

### Grupos de Comandos

```php
CliRouter::group('db', function() {
    CliRouter::command('migrate', 'MigrationController@migrate');
    CliRouter::command('rollback', 'MigrationController@rollback');
    CliRouter::command('seed', 'SeederController@run');
});

// Genera los comandos:
// php com db:migrate
// php com db:rollback
// php com db:seed
```

#### Grupos anidados:

```php
CliRouter::group('cache', function() {
    CliRouter::group('redis', function() {
        CliRouter::command('clear', 'CacheController@clearRedis');
        CliRouter::command('info', 'CacheController@redisInfo');
    });
});

// Ejecutar:
// php com cache:redis:clear
// php com cache redis clear  (también funciona)
```

---

## Routing en Packages

Los packages pueden definir sus propias rutas web y CLI, que se cargan automáticamente cuando el package está habilitado.

### Estructura de un Package

```
packages/vendor/package-name/
├── config/
│   ├── routes.php          # Rutas web (WebRouter)
│   └── cli_routes.php      # Rutas CLI (CliRouter)
├── src/
│   ├── Controllers/
│   ├── ServiceProvider.php
│   └── ...
└── composer.json
```

### Configuración de Rutas Web

Archivo: `packages/vendor/package-name/config/routes.php`

```php
<?php

use Boctulus\Simplerest\Core\WebRouter;

// Rutas web del package
WebRouter::get('mypackage/dashboard', 'Vendor\PackageName\Controllers\DashboardController@index');
WebRouter::post('mypackage/save', 'Vendor\PackageName\Controllers\DataController@save');

// Usando grupos
WebRouter::group('mypackage', function() {
    WebRouter::get('users', 'Vendor\PackageName\Controllers\UserController@index');
    WebRouter::get('users/{id}', 'Vendor\PackageName\Controllers\UserController@show');
    WebRouter::post('users', 'Vendor\PackageName\Controllers\UserController@store');
});
```

### Configuración de Rutas CLI

Archivo: `packages/vendor/package-name/config/cli_routes.php`

```php
<?php

use Boctulus\Simplerest\Core\CliRouter;

// Comandos CLI del package
CliRouter::group('mypackage', function() {

    // Comandos simples
    CliRouter::command('install', 'Vendor\PackageName\Controllers\SetupController@install');
    CliRouter::command('status', 'Vendor\PackageName\Controllers\SetupController@status');

    // Comandos agrupados
    CliRouter::group('db', function() {
        CliRouter::command('migrate', 'Vendor\PackageName\Controllers\DbController@migrate');
        CliRouter::command('seed', 'Vendor\PackageName\Controllers\DbController@seed');
    });

    // Con funciones anónimas
    CliRouter::command('version', function() {
        return 'MyPackage v1.0.0';
    });
});
```

#### Ejecución de comandos del package:

```bash
# Comandos simples
php com mypackage install
php com mypackage status
php com mypackage version

# Comandos agrupados
php com mypackage db migrate
php com mypackage db seed

# También funciona con ':'
php com mypackage:db:migrate
```

### ServiceProvider del Package

El ServiceProvider debe cargar ambos archivos de rutas:

```php
<?php

namespace Vendor\PackageName;

use Boctulus\Simplerest\Core\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        // Load package web routes
        $routesFile = __DIR__ . '/../config/routes.php';
        if (file_exists($routesFile)) {
            include $routesFile;
        }

        // Load package CLI routes
        $cliRoutesFile = __DIR__ . '/../config/cli_routes.php';
        if (file_exists($cliRoutesFile)) {
            include $cliRoutesFile;
        }
    }

    public function register()
    {
        // Register services
    }
}
```

### Configuración de Package

Cada package puede tener su propio archivo de configuración que permite controlar diferentes aspectos del comportamiento del framework para ese package específico.

#### Archivo: `packages/vendor/package-name/config/config.php`

```php
<?php

/*
    Package Configuration

    Este archivo de configuración es específico del package y sobrescribe
    la configuración global cuando se ejecuta un controlador de este package.

    Opciones disponibles:
    - front_controller: Habilita/deshabilita FrontController para este package (default: true)
    - web_router: Habilita/deshabilita WebRouter para este package (default: true)
    - console_router: Habilita/deshabilita CliRouter para este package (default: true)
    - base_url: Prefijo de URL base para las rutas del package (default: '')
*/

return [
    // Habilitar o deshabilitar FrontController para este package
    'front_controller' => true,

    // Habilitar o deshabilitar WebRouter para este package
    'web_router' => true,

    // Habilitar o deshabilitar CliRouter para este package
    'console_router' => true,

    // Configuración personalizada del package
    // Agrega tu configuración personalizada aquí
];
```

#### ¿Cómo funciona?

El ServiceProvider debe cargar la configuración del package en el método `boot()`:

```php
<?php

namespace Vendor\PackageName;

use Boctulus\Simplerest\Core\ServiceProvider as BaseServiceProvider;
use Boctulus\Simplerest\Core\Libs\Config;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        // Cargar configuración del package
        $configFile = __DIR__ . '/../config/config.php';
        if (file_exists($configFile)) {
            $packageConfig = include $configFile;

            // Extraer vendor y package del namespace
            // Formato esperado: Vendor\Package
            $namespace = __NAMESPACE__;
            $parts = explode('\\', $namespace);

            if (count($parts) >= 2) {
                $vendor = strtolower($parts[0]);
                $package = strtolower($parts[1]);

                Config::loadPackageConfig($vendor, $package, $packageConfig);
            }
        }

        // Cargar rutas web del package
        $routesFile = __DIR__ . '/../config/routes.php';
        if (file_exists($routesFile)) {
            include $routesFile;
        }

        // Cargar rutas CLI del package
        $cliRoutesFile = __DIR__ . '/../config/cli_routes.php';
        if (file_exists($cliRoutesFile)) {
            include $cliRoutesFile;
        }
    }

    public function register()
    {
        // Registrar servicios
    }
}
```

#### Deshabilitando FrontController

Si deseas que tu package solo use WebRouter o CliRouter y no el FrontController:

```php
// config/config.php
return [
    'front_controller' => false,  // Deshabilitar FrontController
    'web_router' => true,
    'console_router' => true,
];
```

**Importante:** Cuando se deshabilita `front_controller`, el package solo responderá a las rutas definidas explícitamente en `routes.php` y `cli_routes.php`. No podrás acceder a controladores directamente mediante la URL o comandos CLI sin definir las rutas correspondientes.

#### Ejemplo de Uso

Para un package que debe ser accedido solo mediante rutas explícitas:

```php
// config/config.php
return [
    'front_controller' => false,
    'web_router' => true,
    'console_router' => true,
];

// config/routes.php
use Boctulus\Simplerest\Core\WebRouter;

WebRouter::group('secure-api', function() {
    WebRouter::post('process', 'Vendor\SecureApi\Controllers\ApiController@process');
    WebRouter::get('status', 'Vendor\SecureApi\Controllers\ApiController@status');
});

// config/cli_routes.php
use Boctulus\Simplerest\Core\CliRouter;

CliRouter::group('secure-api', function() {
    CliRouter::command('init', 'Vendor\SecureApi\Controllers\SetupController@init');
    CliRouter::command('reset', 'Vendor\SecureApi\Controllers\SetupController@reset');
});
```

Con esta configuración:
- ✅ Funciona: `POST /secure-api/process` (ruta definida)
- ✅ Funciona: `php com secure-api init` (comando definido)
- ❌ No funciona: `GET /secure-api/api-controller/process` (FrontController deshabilitado)

### Ejemplo Completo: Package Zippy

#### Rutas Web (`packages/boctulus/zippy/config/routes.php`):

```php
<?php

use Boctulus\Simplerest\Core\WebRouter;

WebRouter::group('zippy', function() {
    WebRouter::get('csv/comercio', 'Boctulus\Zippy\Controllers\ZippyController@read_csv_comercio');
    WebRouter::get('csv/products', 'Boctulus\Zippy\Controllers\ZippyController@read_csv_products');

    WebRouter::get('importer', 'Boctulus\Zippy\Controllers\ProductImportController@index');
    WebRouter::get('importer/import', 'Boctulus\Zippy\Controllers\ProductImportController@import_zippy_csv');
});
```

#### Rutas CLI (`packages/boctulus/zippy/config/cli_routes.php`):

```php
<?php

use Boctulus\Simplerest\Core\CliRouter;

CliRouter::group('zippy', function() {

    CliRouter::group('importer', function() {
        CliRouter::command('import', 'Boctulus\Zippy\Controllers\ProductImportController@import_zippy_csv');
        CliRouter::command('check-dupes', 'Boctulus\Zippy\Controllers\ProductImportController@check_dupes');
    });

    CliRouter::group('csv', function() {
        CliRouter::command('comercio', 'Boctulus\Zippy\Controllers\ZippyController@read_csv_comercio');
        CliRouter::command('products', 'Boctulus\Zippy\Controllers\ZippyController@read_csv_products');
    });
});
```

#### Uso:

```bash
# Rutas web
GET /zippy/csv/comercio
GET /zippy/importer/import

# Rutas CLI
php com zippy importer import
php com zippy importer check-dupes
php com zippy csv comercio
```

### Ejemplo Completo: Package LLM-Providers

#### Rutas CLI (`packages/boctulus/llm-providers/config/cli_routes.php`):

```php
<?php

use Boctulus\Simplerest\Core\CliRouter;

CliRouter::group('llm', function() {
    // Ollama
    CliRouter::command('ollama:prompt', 'Boctulus\LLMProviders\Controllers\LlmController@ollama_prompt');
    CliRouter::command('ollama:list', 'Boctulus\LLMProviders\Controllers\LlmController@ollama_list');
});
```

#### Uso:

```bash
# Listar modelos de Ollama
php com llm ollama:list

# Enviar un prompt a Ollama con el modelo por defecto
php com llm ollama:prompt "¿Cuál es la capital de Francia?"

# Enviar un prompt a Ollama especificando un modelo
php com llm ollama:prompt "Escribe un poema sobre la IA" "mistral"
```


---

## Routing en Modules

Los módulos son componentes autocontenidos que viven dentro de `app/Modules/` y pueden definir sus propias rutas web y CLI de manera similar a los packages, pero con una diferencia fundamental: **no requieren configuración en composer.json**.

### Diferencias entre Modules y Packages

| Aspecto | Modules | Packages |
|---------|---------|----------|
| **Ubicación** | `app/Modules/{ModuleName}/` | `packages/{vendor}/{package}/` |
| **Autoloading** | ✅ Autoloader personalizado del framework | Composer PSR-4 |
| **composer.json** | ❌ **NO requerido** | ✅ Requerido |
| **Distribución** | Copiar carpeta directamente | Via Composer/Packagist |
| **Configuración** | Solo registro en `config/config.php` | composer.json + config.php |
| **Portabilidad** | Alta (copiar y pegar) | Media (requiere Composer) |

### Estructura de un Module

```
app/
└── Modules/
    └── ModuleName/
        ├── assets/
        ├── config/
        │   └── routes.php          # Rutas web (WebRouter)
        ├── database/
        ├── docs/
        ├── src/
        │   ├── Controllers/
        │   ├── ModuleProvider.php   # ← Punto de entrada
        │   └── ...
        ├── tests/
        └── views/
```

### Configuración de Rutas Web en Modules

Archivo: `app/Modules/Xeni/config/routes.php`

```php
<?php

use Boctulus\Simplerest\Core\WebRouter;

// Rutas web del módulo
WebRouter::get('xeni/v1/test', 'Boctulus\Simplerest\Modules\Xeni\Controllers\V1TestController@index');
WebRouter::get('xeni/test', 'Boctulus\Simplerest\Modules\Xeni\Controllers\TestController@index');

// Usando grupos
WebRouter::group('xeni', function() {
    WebRouter::get('dashboard', 'Boctulus\Simplerest\Modules\Xeni\Controllers\DashboardController@index');
    WebRouter::get('users', 'Boctulus\Simplerest\Modules\Xeni\Controllers\UserController@list');
    WebRouter::get('users/{id}', 'Boctulus\Simplerest\Modules\Xeni\Controllers\UserController@show');
});
```

### ModuleProvider

El ModuleProvider es similar al ServiceProvider de los packages, pero específico para módulos:

```php
<?php

namespace Boctulus\Simplerest\Modules\Xeni;

use Boctulus\Simplerest\Core\ServiceProvider;

class ModuleProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Cargar rutas del módulo
        $routesPath = __DIR__ . '/../config/routes.php';
        if (file_exists($routesPath)) {
            include $routesPath;
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Registrar servicios del módulo si es necesario
    }
}
```

### Registro del Module

**Opción 1: Registro Manual** (Recomendado)

Agregar el ModuleProvider al array `providers` en `config/config.php`:

```php
'providers' => [
    // ... otros providers
    Boctulus\Simplerest\Modules\Xeni\ModuleProvider::class,
    // ... otros providers
],
```

**¡Eso es todo!** El módulo ya está funcionando. No es necesario modificar `composer.json`.

**Opción 2: Auto-Discovery** (Experimental)

El framework incluye soporte para auto-discovery de módulos, permitiendo que los ModuleProviders se registren automáticamente sin necesidad de agregarlos manualmente al `config.php`.

⚠️ **Nota**: Esta feature está disponible pero requiere verificar si está habilitada en tu instalación. El registro manual es más explícito y recomendado para producción.

Si el auto-discovery está habilitado, el framework escaneará automáticamente `app/Modules/*/src/ModuleProvider.php` y los registrará. Sin embargo, el registro manual es preferible porque:

- ✅ Más explícito y fácil de rastrear
- ✅ Mejor control sobre qué módulos están activos
- ✅ Evita overhead de escaneo de archivos
- ✅ Mejor para entornos de producción

### Autoloading de Modules

El framework incluye un autoloader personalizado que carga automáticamente las clases de módulos siguiendo la convención:

**Namespace**: `Boctulus\Simplerest\Modules\{ModuleName}\...`
**Ubicación**: `app/Modules/{ModuleName}/src/...`

**Ejemplo**:
- Clase: `Boctulus\Simplerest\Modules\Xeni\Controllers\TestController`
- Archivo: `app/Modules/Xeni/src/Controllers/TestController.php`

El autoloader se registra automáticamente en `app.php` después del autoloader de Composer, proporcionando carga de clases transparente sin necesidad de `composer.json`.

### Uso desde HTTP

```bash
# Rutas definidas en el ModuleProvider
GET /xeni/v1/test
GET /xeni/test
GET /xeni/dashboard
GET /xeni/users
GET /xeni/users/123
```

### Ventajas de los Modules

✅ **Sin composer.json**: No necesitas agregar nada al autoload de Composer
✅ **Autoloading automático**: El framework carga las clases transparentemente
✅ **Portabilidad**: Copia el módulo a otro proyecto y registra el provider
✅ **Simplicidad**: Solo un paso de configuración (registro en config.php)
✅ **Ideal para WordPress**: Perfecto para portar funcionalidad a plugins WP
✅ **Rápido desarrollo**: Menos overhead que packages completos

### Cuándo usar Modules vs Packages

**Usa Modules cuando:**
- Desarrolles funcionalidad específica del proyecto actual
- Necesites portabilidad rápida sin dependencias de Composer
- Estés creando plugins WordPress basados en SimpleRest
- No requieras distribución pública vía Packagist
- Quieras desarrollo ágil sin configuración de composer.json

**Usa Packages cuando:**
- La funcionalidad sea reutilizable entre múltiples proyectos
- Necesites versionado semántico con Composer
- Planees distribución pública o privada vía Packagist
- Requieras gestión de dependencias propias
- Necesites tests aislados e integración continua

### Ejemplo Completo: Module ComponentTest

Este módulo de ejemplo muestra componentes UI:

**Estructura**:
```
app/Modules/ComponentTest/
├── config/
│   └── routes.php
├── src/
│   ├── Controllers/
│   │   └── ComponentsController.php
│   └── ModuleProvider.php
└── views/
    └── components.php
```

**Rutas** (`config/routes.php`):
```php
<?php

use Boctulus\Simplerest\Core\WebRouter;

WebRouter::group('components', function() {
    WebRouter::get('test', 'Boctulus\Simplerest\Modules\ComponentTest\Controllers\ComponentsController@index');
    WebRouter::get('demo', 'Boctulus\Simplerest\Modules\ComponentTest\Controllers\ComponentsController@demo');
});
```

**Registro** (`config/config.php`):
```php
'providers' => [
    // ...
    Boctulus\Simplerest\Modules\ComponentTest\ModuleProvider::class,
    // ...
],
```

**Uso**:
```bash
GET /components/test
GET /components/demo
```

---

## Front Controller

Es posible configurar el uso del Front Controller y/o del Router. El primero es más sencillo pero se aconseja casi exclusivamente para utilizar los controllers desde la terminal.

### Diferencia con CliRouter

Mientras tanto **CliRouter** como **FrontController** pueden ejecutar:

```bash
php com folder\calc inc 7
```

Solo **FrontController** puede hacerlo así:

```bash
php com folder calc inc 7
```

El FrontController no necesita la `\` para separar carpetas.

---

## Arquitectura de Handlers

El FrontController utiliza un sistema de **Handlers** modulares que separan las responsabilidades del routing en clases independientes y configurables.

### Concepto

En lugar de tener un FrontController monolítico de 400+ líneas, la arquitectura de handlers delega cada aspecto del routing a clases especializadas:

```
Request → RequestHandler → ApiHandler/AuthHandler → Controller
                              ↓
                         OutputHandler → MiddlewareHandler → Response
```

### Beneficios

✅ **Modularidad**: Cada handler tiene una responsabilidad única
✅ **Testabilidad**: Los handlers son fáciles de testear aisladamente
✅ **Configurabilidad**: Puedes reemplazar cualquier handler con tu propia implementación
✅ **Mantenibilidad**: FrontController reducido de 317 a 99 líneas (68% menos código)
✅ **Extensibilidad**: Agregar nuevos tipos de rutas sin tocar el core

### Los 6 Handlers

#### 1. RequestHandler

**Responsabilidad**: Parsea requests HTTP/CLI y resuelve controladores regulares.

**Métodos**:
- `parse(string $env): array` - Detecta entorno y extrae parámetros
- `resolveController(array $params): array` - Resuelve clase, método y argumentos

**Ubicación**: `app/Core/Handlers/RequestHandler.php`

#### 2. ApiHandler

**Responsabilidad**: Maneja rutas `/api/*` con validación de versión.

**Métodos**:
- `resolve(array $params): array` - Resuelve rutas API con versionado

**Características**:
- Valida formato de versión (v1, v2, etc.)
- Soporte para `remove_api_slug` config
- Determina método HTTP automáticamente

**Ubicación**: `app/Core/Handlers/ApiHandler.php`

#### 3. AuthHandler

**Responsabilidad**: Procesa rutas `/auth` de autenticación.

**Métodos**:
- `resolve(array $params): array` - Resuelve rutas de autenticación

**Características**:
- Maneja MyAuthController
- Valida versión de API
- Extrae action y parámetros

**Ubicación**: `app/Core/Handlers/AuthHandler.php`

#### 4. OutputHandler

**Responsabilidad**: Formatea respuestas según contexto (JSON, HTML, CLI).

**Métodos**:
- `format($controller, $data): string` - Determina y aplica formato de salida

**Formatos soportados**:
- `json` - Para ApiController
- `pretty_json` - Para Postman/Insomnia
- `dd` - Para ConsoleController y navegadores

**Ubicación**: `app/Core/Handlers/OutputHandler.php`

#### 5. MiddlewareHandler

**Responsabilidad**: Ejecuta middlewares configurados para clase/método.

**Métodos**:
- `run(string $class, string $method): void` - Ejecuta middlewares aplicables

**Características**:
- Carga `config/middlewares.php`
- Soporta middleware específico por método
- Soporta middleware global con `__all__`

**Ubicación**: `app/Core/Handlers/MiddlewareHandler.php`

#### 6. ErrorHandler

**Responsabilidad**: Manejo centralizado de errores y excepciones.

**Métodos**:
- `handle(\Throwable $e): void` - Procesa errores y envía respuesta

**Características**:
- Logging automático de errores
- Respuestas JSON estructuradas
- Captura todas las excepciones

**Ubicación**: `app/Core/Handlers/ErrorHandler.php`

### Configuración

Los handlers se configuran en `config/config.php`:

```php
'front_behaviors' => [
    'request'    => Boctulus\Simplerest\Core\Handlers\RequestHandler::class,
    'api'        => Boctulus\Simplerest\Core\Handlers\ApiHandler::class,
    'auth'       => Boctulus\Simplerest\Core\Handlers\AuthHandler::class,
    'output'     => Boctulus\Simplerest\Core\Handlers\OutputHandler::class,
    'middleware' => Boctulus\Simplerest\Core\Handlers\MiddlewareHandler::class,
    'error'      => Boctulus\Simplerest\Core\Handlers\ErrorHandler::class,
],
```

### Flujo de Ejecución

```php
// FrontController::resolve()

1. Instanciar handlers desde config
2. Determinar entorno (CLI/HTTP)
3. Parse request → RequestHandler::parse()
4. Resolver ruta:
   - Si /auth  → AuthHandler::resolve()
   - Si /api   → ApiHandler::resolve()
   - Sino     → RequestHandler::resolveController()
5. Validar clase y método existen
6. Ejecutar método del controlador
7. Formatear salida → OutputHandler::format()
8. Ejecutar middlewares → MiddlewareHandler::run()
9. Enviar respuesta
```

### Crear un Handler Personalizado

#### Ejemplo: CustomApiHandler

```php
<?php

namespace MyApp\Handlers;

use Boctulus\Simplerest\Core\Handlers\ApiHandler;

class CustomApiHandler extends ApiHandler
{
    public function resolve(array $params): array
    {
        // Lógica personalizada para versionado diferente
        if (isset($params[1]) && $params[1] === 'v2') {
            return $this->resolveV2($params);
        }

        // Delegar al comportamiento original
        return parent::resolve($params);
    }

    private function resolveV2(array $params): array
    {
        // Implementación específica para v2
        $controller = $params[2] ?? null;
        $namespace = namespace_url() . '\\Controllers\\api\\v2\\';
        $class_name = $namespace . ucfirst($controller) . 'Controller';

        // ... lógica custom

        return [$class_name, $method, $args, 'v2'];
    }
}
```

#### Registrar el Handler Personalizado

En `config/config.php`:

```php
'front_behaviors' => [
    'request'    => Boctulus\Simplerest\Core\Handlers\RequestHandler::class,
    'api'        => MyApp\Handlers\CustomApiHandler::class, // ← Personalizado
    'auth'       => Boctulus\Simplerest\Core\Handlers\AuthHandler::class,
    'output'     => Boctulus\Simplerest\Core\Handlers\OutputHandler::class,
    'middleware' => Boctulus\Simplerest\Core\Handlers\MiddlewareHandler::class,
    'error'      => Boctulus\Simplerest\Core\Handlers\ErrorHandler::class,
],
```

### Handler vs Traits

¿Por qué handlers y no traits?

| Aspecto | Handlers (Clases) | Traits |
|---------|------------------|--------|
| **Instanciabilidad** | ✅ Sí, con estado propio | ❌ No |
| **Configurabilidad** | ✅ Desde config.php | ❌ Fijo en código |
| **Reemplazabilidad** | ✅ Total | ⚠️ Parcial |
| **Testabilidad** | ✅ Alta (unit tests) | ⚠️ Media |
| **Inyección de dependencias** | ✅ Sí | ❌ No |
| **Polimorfismo** | ✅ Sí | ❌ No |

### Casos de Uso Avanzados

#### 1. Handler de Autenticación Personalizada

```php
class OAuth2AuthHandler extends AuthHandler
{
    public function resolve(array $params): array
    {
        // Validar token OAuth2
        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? null;

        if (!$this->validateOAuth2Token($token)) {
            Response::getInstance()->error('Unauthorized', 401);
        }

        return parent::resolve($params);
    }
}
```

#### 2. Handler de Output con Cache

```php
class CachedOutputHandler extends OutputHandler
{
    public function format($controller, $data): string
    {
        $cacheKey = $this->getCacheKey($controller, $data);

        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        $output = parent::format($controller, $data);
        Cache::set($cacheKey, $output, 3600);

        return $output;
    }
}
```

#### 3. Handler de Errores con Logging Avanzado

```php
class SentryErrorHandler extends ErrorHandler
{
    public function handle(\Throwable $e): void
    {
        // Enviar a Sentry
        \Sentry\captureException($e);

        // Log local
        parent::handle($e);
    }
}
```

### Testing de Handlers

Los handlers son fáciles de testear:

```php
class RequestHandlerTest extends TestCase
{
    public function testParseHttpRequest()
    {
        $_SERVER['REQUEST_URI'] = '/users/123';

        $handler = new RequestHandler();
        [$params, $is_auth, $is_api] = $handler->parse('http');

        $this->assertEquals(['users', '123'], $params);
        $this->assertFalse($is_auth);
        $this->assertFalse($is_api);
    }

    public function testResolveController()
    {
        $handler = new RequestHandler();
        [$class, $method, $args] = $handler->resolveController(['users', 'show', '123']);

        $this->assertEquals('Boctulus\Simplerest\Controllers\UsersController', $class);
        $this->assertEquals('show', $method);
        $this->assertEquals(['123'], $args);
    }
}
```

### Migración desde FrontController Antiguo

Si tienes código legacy que depende del FrontController antiguo, la migración es transparente:

✅ **Compatibilidad 100%**: Todos los controladores existentes funcionan sin cambios
✅ **API idéntica**: El comportamiento externo es el mismo
✅ **Sin breaking changes**: No hay que modificar rutas o controladores

La única diferencia es interna: el código ahora está mejor organizado en handlers separados.

### Soporte de __call() en FrontController

El FrontController también soporta el método mágico `__call()` tanto para HTTP como para CLI:

```php
class ApiController extends Controller
{
    function __call($name, $arguments)
    {
        // Manejar rutas dinámicas como /api/v1/users/getByEmail
        if (Strings::startsWith('getBy', $name)) {
            $field = lcfirst(Strings::after($name, 'getBy'));
            return $this->findBy($field, ...$arguments);
        }
    }

    private function findBy($field, $value)
    {
        return User::where($field, $value)->first();
    }
}
```

**Uso HTTP**:
```bash
GET /api/v1/users/getByEmail/user@example.com
GET /api/v1/users/getById/123
```

**Uso CLI**:
```bash
php com api v1 users getByEmail user@example.com
php com api v1 users getById 123
```

El FrontController detecta automáticamente si la clase tiene `__call()` y permite la ejecución de métodos que no existen físicamente.

---

## Errores Comunes

### Comando no encontrado

**Problema**: El comando CLI no se ejecuta.

**Solución**:
- Verifica que el archivo `config/cli_routes.php` existe
- Asegúrate de que `console_router` está habilitado en `config/config.php`
- Confirma que el namespace del controlador es correcto
- Ejecuta `composer dumpautoload --no-ansi` después de agregar nuevos controladores

### Método mágico __call() no funciona

**Problema**: Los métodos dinámicos con `__call()` no se ejecutan.

**Solución**:
- **CLI**: Verifica que estás usando CliRouter o FrontController (ambos soportan `__call()`)
- **HTTP**: Verifica que el FrontController está habilitado
- Confirma que el método `__call()` está definido correctamente en el controlador
- Revisa que el método no exista físicamente (PHP prioriza métodos reales sobre `__call()`)

**Ejemplo de debug**:

```php
function __call($name, $arguments)
{
    // Debug temporal
    error_log("__call invoked with: $name");
    error_log("Arguments: " . var_export($arguments, true));

    // Tu lógica...
}
```

### Handler personalizado no se carga

**Problema**: El handler personalizado no se está usando.

**Solución**:
- Verifica que el namespace es correcto en `config/config.php`
- Confirma que el handler extiende la clase base correspondiente
- Ejecuta `composer dumpautoload`
- Revisa que el método requerido está implementado
- Verifica que el array `front_behaviors` tiene la clave correcta

**Ejemplo correcto**:

```php
// config/config.php
'front_behaviors' => [
    'request'    => Boctulus\Simplerest\Core\Handlers\RequestHandler::class,
    'api'        => MyApp\Handlers\CustomApiHandler::class, // ✅ Namespace completo
    // ...
],
```

### Ruta web no responde

**Problema**: La ruta web retorna 404.

**Solución**:
- Verifica que `web_router` está habilitado en `config/config.php`
- Confirma que la ruta está definida en `config/routes.php` o en el package
- Revisa que el controlador y método existen
- Verifica que el ServiceProvider del package está registrado

### Argumentos incorrectos

**Problema**: Error de argumentos en comandos CLI.

**Solución**:
- Revisa la cantidad de parámetros esperados por el método
- Usa comillas si el argumento contiene espacios: `php com test "hello world"`
- Verifica el tipo de datos esperado (string, int, etc.)

### Conflicto de rutas

**Problema**: Una ruta captura requests de otra más específica.

**Solución**:
- **WebRouter**: Ya no es necesario ordenar manualmente, el router lo hace automáticamente
- **CliRouter**: Define comandos más específicos dentro de grupos para evitar ambigüedad
- Usa el método `where()` para validar parámetros y hacer rutas más específicas

---

## Mejores Prácticas

### Para WebRouter:

1. **Usa grupos** para organizar rutas relacionadas
2. **Valida parámetros** con `where()` para mayor seguridad
3. **Prefija rutas de packages** para evitar conflictos (ej: `mypackage/*`)
4. **Documenta rutas complejas** con comentarios en el archivo de rutas

### Para CliRouter:

1. **Usa grupos** para comandos relacionados (`db:migrate`, `db:seed`)
2. **Nombres descriptivos** que indiquen claramente la acción
3. **Maneja errores** dentro de los controladores/callbacks
4. **Documenta comandos** en el README del package

### Para Packages:

1. **Siempre prefijar rutas** con el nombre del package
2. **Cargar rutas en ServiceProvider** dentro del método `boot()`
3. **Documentar comandos CLI** disponibles en el README
4. **Incluir ejemplos** de uso en los archivos de rutas

---

## Referencias

- Configuración principal: `config/config.php`
- Rutas web globales: `config/routes.php`
- Rutas CLI globales: `config/cli_routes.php`
- WebRouter: `app/Core/WebRouter.php`
- CliRouter: `app/Core/CliRouter.php`
- FrontController: `app/Core/FrontController.php`
- Handlers: `app/Core/Handlers/*.php`

---

## Changelog - Mejoras Recientes

### v0.8.13 - Configuración Específica de Packages

#### ✨ Package Configuration System

**Sistema de configuración por package**:
- ✅ Cada package puede tener su propio `config/config.php`
- ✅ Configuración específica sobrescribe la global
- ✅ Control granular de FrontController, WebRouter y CliRouter por package
- ✅ Namespace aislado: `packages.{vendor}.{package}.*`
- ✅ Fallback automático a configuración global

**Nuevos métodos en Config.php**:
1. **Config::loadPackageConfig()** - Carga configuración del package
2. **Config::getPackageConfig()** - Obtiene config con fallback a global
3. **Config::getPackageFromClass()** - Extrae vendor/package del namespace

**Ubicación**: `app/Core/Libs/Config.php` (líneas 148-229)

#### ✨ FrontController Package-aware

**FrontController verifica configuración de packages**:
- ✅ Detecta automáticamente si un controlador pertenece a un package
- ✅ Respeta la configuración `front_controller` del package
- ✅ Si está deshabilitado, el package solo responde a rutas explícitas
- ✅ Compatible con packages existentes (default: habilitado)

**Ubicación**: `app/Core/FrontController.php` (líneas 50-66)

#### ✨ Templates Actualizados

**ServiceProvider.php template**:
- ✅ Carga automática de `config/config.php` en `boot()`
- ✅ Detección automática de vendor/package desde namespace
- ✅ Integración con `Config::loadPackageConfig()`

**Ubicación**: `app/Core/Templates/ServiceProvider.php`

**PackageConfig.php template** (nuevo):
- ✅ Template con opciones por defecto
- ✅ Documentación inline de todas las opciones
- ✅ Generado automáticamente al crear packages con `php com make package`

**Ubicación**: `app/Core/Templates/PackageConfig.php`

#### ✨ Generación Automática

**MakeCommand actualizado**:
- ✅ `php com make package vendor/name` genera `config/config.php` automáticamente
- ✅ Usa template PackageConfig.php
- ✅ Listo para usar sin configuración adicional

**Ubicación**: `app/Commands/MakeCommand.php` (líneas 3010-3018)

#### 📚 Documentación

**Routing.md actualizado con**:
- ✅ Nueva sección "Configuración de Package" (140+ líneas)
- ✅ Ejemplos de configuración por package
- ✅ Ejemplo práctico de package con FrontController deshabilitado
- ✅ Explicación de ✅/❌ con FrontController habilitado/deshabilitado
- ✅ Documentación de cómo funciona el sistema de carga de config

**Ubicación**: `docs/Routing.md` (líneas 526-665)

#### 🎯 Casos de Uso

**Package con rutas explícitas solamente**:
```php
// Deshabilitar FrontController para forzar rutas explícitas
return [
    'front_controller' => false,
    'web_router' => true,
    'console_router' => true,
];
```

**Package aislado con configuración personalizada**:
```php
return [
    'front_controller' => true,
    'web_router' => true,
    'console_router' => false,  // Sin comandos CLI
    'base_url' => '/custom-prefix',

    // Configuración personalizada
    'api_key' => 'secret',
    'cache_enabled' => true,
];
```

**Acceder a configuración del package**:
```php
// Desde cualquier lugar del código
$value = Config::getPackageConfig('vendor', 'package', 'api_key', 'default');
```

#### 🔧 Breaking Changes

**Ninguno**: Esta feature es completamente backward compatible. Packages existentes sin `config/config.php` usan los valores por defecto (todos habilitados).

---

### v0.8.12 - Refactoring de Handlers y Soporte __call()

#### ✨ Nueva Arquitectura de Handlers

**Refactoring completo del FrontController**:
- ✅ Reducción de 317 a 99 líneas (68% menos código)
- ✅ 6 handlers modulares e intercambiables
- ✅ Configuración centralizada en `config/config.php`
- ✅ 100% compatible con código existente (sin breaking changes)

**Handlers implementados**:
1. **RequestHandler** - Parsing HTTP/CLI y resolución de controladores
2. **ApiHandler** - Manejo de rutas `/api/*` con versionado
3. **AuthHandler** - Procesamiento de rutas `/auth`
4. **OutputHandler** - Formateo de respuestas (JSON, HTML, CLI)
5. **MiddlewareHandler** - Ejecución de middlewares
6. **ErrorHandler** - Manejo centralizado de errores

**Ubicación**: `app/Core/Handlers/`

#### ✨ Soporte de Métodos Mágicos (__call)

**CliRouter**: Ahora detecta y ejecuta correctamente métodos mágicos `__call()`:
- ✅ Permite números y caracteres especiales como nombres de método
- ✅ Ideal para DSLs (Domain Specific Languages)
- ✅ Fallback inteligente: primero busca método real, luego `__call()`

**Ejemplo**:
```bash
php com whatsapp 333333333 'Hola'
php com whatsapp alias=ph
```

**FrontController**: Soporte completo de `__call()` tanto HTTP como CLI:
- ✅ Detecta automáticamente si la clase tiene `__call()`
- ✅ Funciona con rutas web y comandos de consola
- ✅ Validación inteligente: `method_exists()` antes de `__call()`

**Fix aplicado**: `app/Core/CliRouter.php` líneas 207-210

#### 🐛 Correcciones

**PHP 8 Compatibility**:
- ✅ Agregado `#[\ReturnTypeWillChange]` a métodos ArrayAccess en Request.php
- ✅ Eliminados warnings de deprecación en PHP 8.x

**Request Parameters**:
- ✅ Parámetros de ruta ahora accesibles vía Request ArrayAccess
- ✅ `$req->setParams()` llamado automáticamente en FrontController

#### 📚 Documentación

**Routing.md actualizado con**:
- ✅ Sección completa "Arquitectura de Handlers" (300+ líneas)
- ✅ Ejemplos de handlers personalizados
- ✅ Testing de handlers
- ✅ Casos de uso avanzados (OAuth2, Cache, Sentry)
- ✅ Sección "Soporte de Métodos Mágicos"
- ✅ Errores comunes actualizados
- ✅ Tabla de contenidos expandida

#### 🎯 Beneficios

**Para Desarrolladores**:
- Código más limpio y mantenible
- Fácil de extender sin tocar el core
- Testeable a nivel unitario
- Flexibilidad total para customización

**Para el Framework**:
- Arquitectura moderna y escalable
- Separación clara de responsabilidades (SOLID)
- Preparado para features futuras
- Mejor debuggeabilidad

---

# Comparacion con Laravel routing

SimpleRest está en ~70% de funcionalidad respecto a Laravel en routing:

  Fortalezas:
  - ✅ Tiene las features fundamentales bien implementadas
  - ✅ Ordenamiento automático es superior a Laravel
  - ✅ Sintaxis más simple y consistente CLI/Web
  - ✅ Multi-word commands más flexibles

  Debilidades:
  - ❌ Falta Model Binding (feature MUY usada)
  - ❌ Falta Resource Controllers (ahorra MUCHO código)
  - ❌ Alias/Name implementados pero no funcionan
  - ❌ No hay route caching (importante para performance)

  Para producción seria, SimpleRest necesitaría:
  1. Arreglar alias() y name()
  2. Implementar Model Binding
  3. Implementar Resource Controllers
  4. Agregar Route Caching

  Con esos 4 features, SimpleRest estaría al 85-90% de Laravel en routing y sería completamente viable para producción.
