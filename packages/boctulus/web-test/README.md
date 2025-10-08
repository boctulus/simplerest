# web-test

Package web-test by boctulus.

## Installation

1. Add the package namespace to `composer.json` autoload section:

```json
"autoload": {
    "psr-4": {
        "Boctulus\WebTest\\": "packages/boctulus/web-test/src"
    }
}
```

2. Add the ServiceProvider to `config/config.php` providers array:

```php
'providers' => [
    Boctulus\WebTest\ServiceProvider::class,
    // ...
],
```

3. Regenerate the autoloader:

```bash
composer dumpautoload --no-ansi
```

## Usage

### Routes

Define your routes in `config/routes.php`:

```php
use Boctulus\Simplerest\Core\WebRouter;

WebRouter::get('web-test/example', 'Boctulus\WebTest\Controllers\ExampleController@index');
```

### Controllers

Create controllers in `src/Controllers/` with namespace `Boctulus\WebTest\Controllers`.

## Structure

```
web-test/
├── assets/          # CSS, JS, images
├── config/          # Configuration files (routes, etc.)
├── database/        # Migrations and seeders
├── etc/             # Additional resources
├── src/             # Source code
│   ├── Controllers/ # Controllers
│   ├── Models/      # Models
│   ├── Middlewares/ # Middlewares
│   ├── Helpers/     # Helper functions
│   ├── Libs/        # Libraries
│   ├── Interfaces/  # Interfaces
│   └── Traits/      # Traits
├── tests/           # Unit tests
├── views/           # View templates
└── composer.json    # Package metadata
```
