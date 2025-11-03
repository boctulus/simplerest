# Packages

## Creación de un Package

Los packages se crean automáticamente con el comando `make package`:

```bash
php com make package <nombre-package> <autor> [destino]
```

**Ejemplo:**

```bash
php com make package api-client boctulus
```

Esto creará la estructura completa en `packages/boctulus/api-client/`.

## Estructura Generada Automáticamente

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

## Archivos Generados Automáticamente

### 1. `composer.json`

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

### 2. `ServiceProvider.php`

Se genera automáticamente un ServiceProvider base en `src/ServiceProvider.php`.

### 3. `README.md`

Se genera con instrucciones completas de instalación y uso del package.

### 4. `LICENSE`

Se genera una licencia MIT por defecto.

## Instalación de un Package

Una vez creado el package, hay dos formas de instalarlo:

### Opción 1: Repositorio Local (Recomendado para desarrollo)

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

### Opción 2: Agregar Manualmente el Autoload

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

## Registrar el Service Provider

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

## Uso del Package

### Service Provider

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

### Configuración

Incluir archivos de configuración es tan simple como usar `require()` o `require_once()` en el ServiceProvider:

```php
public function register()
{
    $config = require __DIR__.'/../config/config.php';
    // Usar la configuración
}
```

### Definir Rutas

Las rutas del package se definen típicamente en `config/routes.php`:

```php
use Boctulus\Simplerest\Core\WebRouter;
use Boctulus\ApiClient\Controllers\ExampleController;

WebRouter::get('api-client/example', ExampleController::class . '@index');
```

### Controladores

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

### Modelos

Los modelos se ubican en `src/Models/`:

```php
namespace Boctulus\ApiClient\Models;

use Boctulus\Simplerest\Core\Model;

class ExampleModel extends Model
{
    protected $table = 'examples';
}
```

## Modelos y schemas en ubicacion arbitraria

Es perfectamente posible cambiar la ubicacion de schemas y modelos dentro de un package.

Pasos:

1.- Mover schemas y modelos
2.- Ajustar namespaces de los schemas (edicion dentro del archivo .php del modelo)
3.- Ajustar namespace de los modelos de la conexion (recordar que el framework es multitenant) que es utilizado por DB::table()

La funcion set_model_namespace() concatena al namespace pasado como parametro '\\Models\\'

Ej:
```
    /*
        Conexion a la base de datos: 'laravel_pos'
        Namespace del package: 'Boctulus\FriendlyposWeb'
    */

    DB::getConnection('laravel_pos');

    set_model_namespace('laravel_pos', 'Boctulus\FriendlyposWeb');

    $res = dd(
        DB::table('unidad_medida')->get()
    );

    DB::closeConnection('laravel_pos');
```

## Migraciones

Para ejecutar migraciones dentro de un package, usa el parámetro `--dir`:

```bash
php com migrate --dir=packages/boctulus/api-client/database/migrations
```

## Notas Importantes

- Los paquetes funcionan **solamente** con rutas y **no** con el FrontController
- Actualmente el soporte cubre rutas con funciones anónimas y controladores
- Falta agregar soporte completo para migraciones, traducciones, vistas, etc. (en desarrollo)

