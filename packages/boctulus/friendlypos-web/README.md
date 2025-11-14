# friendlypos_web

Package friendlypos_web by --author:boctulus.

## Motivation

Este package provee servicios varios a un POS WEB. Es complementario a "openfactura-sdk" para facturacion electronica.

- Utilizar "openfactura-sdk" y sobre el construir una API REST con autenticacion y persistir en base de datos lo necesario. 

- Utilizar "phpqrcode" para generar codigos QR desde `third_party\phpqrcode`

https://phpqrcode.sourceforge.net/

Otra opcion que aparece incluida en el proyecto original es "php-qrcode" basado en proyecto de "Kazuhiko Arase"

https://github.com/chillerlan/php-qrcode

- Utilizar "fpdf" para generar PDFs dado que el proyecto original utiliza "LaravelFpdf"

- Utilizar "SimpleXLSX" desde  `third_party\phpqrcode\shuchkin` para procesar archivos XLS / XLSX

- Utilizar "html5-qrcode.min.js", una cross-platform HTML5 QR code reader si se necesitara poder escanear codigos QR desde el POS WEB

https://github.com/mebjas/html5-qrcodea cross-platform HTML5 QR code reade


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
