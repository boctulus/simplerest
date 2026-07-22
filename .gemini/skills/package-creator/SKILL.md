---
name: package-creator
description: Complete guide for creating, configuring, and distributing packages in SimpleRest, including ServiceProviders, Composer setup, routes, and migrations.
---

# Package Creator Skill

## Create Package

```bash
php com make package api-client boctulus
```

Creates: `packages/boctulus/api-client/` with Controllers, Models, ServiceProvider, composer.json, etc.

## Autoload Setup

### Option A: Composer Repository (recommended)

```json
// root composer.json
{
    "repositories": [{
        "type": "path",
        "url": "packages/boctulus/api-client",
        "options": { "symlink": true }
    }],
    "require": { "boctulus/api-client": "@dev" }
}
```

```bash
composer clear-cache && composer update && composer dump-autoload -o
```

### Option B: Manual PSR-4

```json
{
    "autoload": {
        "psr-4": { "Boctulus\\ApiClient\\": "packages/boctulus/api-client/src/" }
    }
}
```

```bash
composer dump-autoload --no-ansi
```

## Register ServiceProvider

```php
// config/config.php
'providers' => [
    Boctulus\ApiClient\ServiceProvider::class,
],
```

### ServiceProvider

```php
namespace Boctulus\ApiClient;
use Boctulus\Simplerest\Core\ServiceProvider;

class ApiClientServiceProvider extends ServiceProvider {
    public function boot() { }
    public function register() {
        require_once __DIR__ . '/../config/routes.php';
    }
}
```

## Routes & Controllers

```php
// config/routes.php
WebRouter::get('api-client/example', 'ExampleController@index');
```

```php
namespace Boctulus\ApiClient\Controllers;
class ExampleController {
    public function index() { return ['message' => 'OK']; }
}
```

## Models

```php
namespace Boctulus\ApiClient\Models;
use Boctulus\Simplerest\Core\Model;
class ExampleModel extends Model { protected $table = 'examples'; }
```

Custom namespace:

```php
DB::getConnection('laravel_pos');
set_model_namespace('laravel_pos', 'Boctulus\FriendlyposWeb');
```

## Migrations

```bash
php com make migrations:package boctulus/api-client create_logs_table --create
php com migrate --dir=packages/boctulus/api-client/database/migrations
php com migrate --dir=.../migrations --to=logs_db
```

## composer.json Structure

```json
{
    "name": "boctulus/api-client",
    "type": "library",
    "autoload": { "psr-4": { "Boctulus\\ApiClient\\": "src/" } },
    "extra": {
        "simplerest": { "providers": ["Boctulus\\ApiClient\\ServiceProvider"] }
    }
}
```

## Package vs Module

| Package | Module |
|---------|--------|
| `packages/author/name/` | `app/Modules/name/` |
| Reusable across projects | Project-specific |
| Semantic versioning | Rapid prototyping |
| Composer distribution | Copy between instances |
| Low coupling | Deep framework integration |

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Class not found | `composer dump-autoload` |
| Routes not working | Verify ServiceProvider in config/providers |
| Migration not found | Check `--dir` path |
| Namespace mismatch | Check PSR-4 mapping in composer.json |
