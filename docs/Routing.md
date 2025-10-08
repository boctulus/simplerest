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
  - [Grupos de Comandos](#grupos-de-comandos)
- [Routing en Packages](#routing-en-packages)
  - [Configuración de Rutas Web](#configuración-de-rutas-web)
  - [Configuración de Rutas CLI](#configuración-de-rutas-cli)
- [Front Controller](#front-controller)
- [Errores Comunes](#errores-comunes)

---

## Introducción

SimpleRest Framework ofrece un sistema de routing flexible que soporta tanto rutas web (HTTP) como rutas de consola (CLI). El sistema permite definir rutas mediante controladores o funciones anónimas.

### Componentes principales:

- **WebRouter**: Maneja rutas HTTP con soporte para verbos GET, POST, PUT, PATCH, DELETE, OPTIONS
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
```

#### Usando `fromArray()`

Permite definir múltiples rutas en un solo llamado:

```php
WebRouter::fromArray([
    'GET:/speed_check' => 'SpeedCheckController@index',
    'POST:/producto' => 'ProductController@store',
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

## Errores Comunes

### Comando no encontrado

**Problema**: El comando CLI no se ejecuta.

**Solución**:
- Verifica que el archivo `config/cli_routes.php` existe
- Asegúrate de que `console_router` está habilitado en `config/config.php`
- Confirma que el namespace del controlador es correcto
- Ejecuta `composer dumpautoload --no-ansi` después de agregar nuevos controladores

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
