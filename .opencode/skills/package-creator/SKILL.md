---
name: package-creator
description: Complete guide for creating, configuring, and distributing packages in SimpleRest, including ServiceProviders, Composer setup, routes, and migrations.
---

# Package Creator Skill

## Overview

SimpleRest supports two mechanisms for code organization:
- **Packages** (`packages/author/name/`) — reusable, versioned, distributable via Composer
- **Modules** (`app/Modules/name/`) — project-specific, tightly integrated, faster dev

This SKILL covers **Packages**.

## Step 1: Create the Package

```bash
php com make package api-client boctulus
```

This generates:

```
packages/boctulus/api-client/
├── assets/
│   ├── css/
│   ├── img/
│   ├── js/
│   └── third_party/
├── config/
│   └── config.php
├── database/
│   ├── migrations/
│   └── seeders/
├── src/
│   ├── Controllers/
│   ├── Helpers/
│   ├── Interfaces/
│   ├── Libs/
│   ├── Middlewares/
│   ├── Models/
│   ├── Traits/
│   ├── ServiceProvider.php      ← auto-generated
│   └── ExampleInterface.php     ← auto-generated
├── tests/
├── views/
├── composer.json                 ← auto-generated
├── README.md                     ← auto-generated
└── LICENSE                       ← MIT, auto-generated
```

## Step 2: Configure Autoloading

### Option A: Composer Repository (recommended)

Add to root `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "packages/boctulus/api-client",
            "options": { "symlink": true }
        }
    ],
    "require": {
        "boctulus/api-client": "@dev"
    }
}
```

Then:

```bash
composer clear-cache
composer update
composer dump-autoload -o
```

### Option B: Manual PSR-4 (simpler)

Add to root `composer.json`:

```json
{
    "autoload": {
        "psr-4": {
            "Boctulus\\ApiClient\\": "packages/boctulus/api-client/src/"
        }
    }
}
```

```bash
composer dump-autoload --no-ansi
```

## Step 3: Register the ServiceProvider

In `config/config.php`:

```php
'providers' => [
    Boctulus\ApiClient\ServiceProvider::class,
    // ...
],
```

### ServiceProvider Lifecycle

```php
namespace Boctulus\ApiClient;

use Boctulus\Simplerest\Core\ServiceProvider;

class ApiClientServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Runs at app startup — register routes, load configs
    }

    public function register()
    {
        // Register services, bindings
        require_once __DIR__ . '/../config/routes.php';
    }
}
```

## Step 4: Define Routes

In `config/routes.php`:

```php
use Boctulus\Simplerest\Core\WebRouter;
use Boctulus\ApiClient\Controllers\ExampleController;

WebRouter::get('api-client/example', ExampleController::class . '@index');
WebRouter::post('api-client/webhook', 'WebhookController@handle');
```

## Step 5: Create Controllers

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

## Step 6: Create Models

```php
namespace Boctulus\ApiClient\Models;

use Boctulus\Simplerest\Core\Model;

class ExampleModel extends Model
{
    protected $table = 'examples';
}
```

### Models in Custom Locations

Set model namespace for a specific connection:

```php
DB::getConnection('laravel_pos');
set_model_namespace('laravel_pos', 'Boctulus\FriendlyposWeb');
// Now DB::table('unidad_medida') looks in Boctulus\FriendlyposWeb\Models\
```

## Step 7: Migrations

### Create

```bash
php com make migrations:package boctulus/api-client create_api_logs_table --create
```

### Run

```bash
php com migrate --dir=packages/boctulus/api-client/database/migrations
php com migrate --dir=packages/boctulus/api-client/database/migrations --to=logs_db
```

### Option: Auto-register in ServiceProvider

```php
public function boot()
{
    // Check and run pending migrations
    $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
}
```

## Step 8: Configuration

Package-specific config in `config/config.php`:

```php
return [
    'api_key' => env('API_CLIENT_KEY'),
    'timeout' => 30,
];
```

Load in ServiceProvider:

```php
public function register()
{
    $config = require __DIR__ . '/../config/config.php';
}
```

## Distribution Checklist

- [ ] `composer.json` has correct `name`, `description`, `type: library`
- [ ] PSR-4 autoload maps correctly
- [ ] `extra.simplerest.providers` array lists ServiceProvider classes
- [ ] README with install instructions
- [ ] LICENSE file
- [ ] `.gitignore` excludes vendor, logs, etc.
- [ ] Tests in `tests/` directory

### composer.json Structure

```json
{
    "name": "boctulus/api-client",
    "description": "API Client package for SimpleRest",
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

## Packages vs Modules Decision

| Use Packages when... | Use Modules when... |
|---------------------|-------------------|
| Reuse across projects | Project-specific logic |
| Need semantic versioning | Rapid prototyping |
| Public/private distribution | Deep framework integration |
| Independent tests/CI | WordPress portability |
| Composer dependency management | Simplicity without overhead |

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Class not found | Run `composer dump-autoload` |
| Routes not working | Verify ServiceProvider is registered in `config/providers` (or `config/config.php`) |
| Package not installed | Check `composer.json` repositories section |
| Migration not found | Ensure `--dir` path is correct and absolute from project root |
| Namespace mismatch | Verify PSR-4 mapping in package's `composer.json` |
| Schema not detected | Set model namespace with `set_model_namespace()` |

## Best Practices

1. **Namespace pattern**: `{Author}\{PackageName}` (e.g., `Boctulus\ApiClient`)
2. **Keep packages focused** — single responsibility
3. **Use `extra.simplerest.providers`** for auto-discovery
4. **Version with semver** from the start
5. **Include tests** in `tests/` with PHPUnit
6. **Document routes** provided by the package
7. **Don't hardcode** connection names — use config or ServiceProvider
