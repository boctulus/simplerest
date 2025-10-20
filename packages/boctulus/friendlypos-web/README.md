# friendlypos_web

Package friendlypos_web by --author:boctulus.

## Installation

1. Add the package namespace to `composer.json` autoload section:

```json
"autoload": {
    "psr-4": {
        "Boctulus\FriendlyposWeb\\": "packages/boctulus/friendlypos-web/src"
    }
}
```

2. Add the ServiceProvider to `config/config.php` providers array:

```php
'providers' => [
    Boctulus\FriendlyposWeb\ServiceProvider::class,
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

WebRouter::get('friendlypos-web/example', 'Boctulus\FriendlyposWeb\Controllers\ExampleController@index');
```

### Controllers

Create controllers in `src/Controllers/` with namespace `Boctulus\FriendlyposWeb\Controllers`.

## Structure

```
friendlypos-web/
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
