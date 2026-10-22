# friendlypos_web

Package friendlypos_web by --author:boctulus.

## Motivation

Este package provee servicios varios a un POS WEB. Es complementario a "openfactura-sdk" para facturacion electronica.

El código actual del paquete y sus pruebas no importan ni utilizan las copias de QR Code o XLSX que estaban en `third_party/`; esas copias fueron retiradas. Esas capacidades no forman parte de la funcionalidad actual.

| Copia retirada | Origen y alternativa Composer | Compatibilidad y decisión |
| --- | --- | --- |
| `chillerlan/` | [chillerlan/php-qrcode](https://packagist.org/packages/chillerlan/php-qrcode), upstream [chillerlan/php-qrcode](https://github.com/chillerlan/php-qrcode) | Si se vuelve a usar, limitar a `^5.0` para mantener soporte PHP 8.1; la rama 6 requiere PHP 8.2. No se declara hoy porque no hay consumidores en el paquete. |
| `phpqrcode/` | Copia LGPL-3.0 del proyecto [PHP QR Code en SourceForge](https://sourceforge.net/projects/phpqrcode/) | No sustituirla por `phpqrcode/phpqrcode` sin revisión: esa entrada de Packagist apunta a otro repositorio, solo publica `dev-master` y no declara licencia. No se declara porque no hay consumidores. |
| `shuchkin/SimpleXLSX.php` y `SimpleXLSXEx.php` | [shuchkin/simplexlsx](https://packagist.org/packages/shuchkin/simplexlsx), upstream [shuchkin/simplexlsx](https://github.com/shuchkin/simplexlsx) | La publicación Composer soporta el rango PHP del framework y declara `ext-libxml`, `ext-mbstring`, `ext-simplexml` y `ext-zlib`; Packagist mostraba dos avisos de seguridad al revisar. No se declara porque no hay consumidores. |

Si se incorpora una de estas funciones, `friendlypos-web/composer.json` debe declarar la dependencia real en `require`; Composer la resolverá en el proyecto consumidor. No se deben volver a copiar distribuciones en `third_party/`.

Este paquete contiene integración de FriendlyPOS y no está preparado para publicación pública independiente. Su dependencia `boctulus/openfactura-sdk` todavía carece de un artefacto Composer estable e instalable desde Packagist.


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

## OpenFactura API - Funcionalidad de Override

El controlador `OpenFacturaController` ahora permite sobrescribir la API Key y el modo sandbox a través de headers o parámetros en el body de la solicitud, en lugar de depender únicamente de los valores definidos en el archivo `.env`.

### Configuración en .env

Valores predeterminados definidos en el archivo `.env`:
```
OPENFACTURA_SANDBOX=true
OPENFACTURA_API_KEY_DEV="your_dev_api_key"
OPENFACTURA_API_KEY_PROD="your_prod_api_key"
```

### Uso de Headers para Override

Puedes proporcionar parámetros personalizados usando headers HTTP:

- `X-Openfactura-Api-Key`: Sobrescribe la API Key predeterminada
- `X-Openfactura-Sandbox`: Sobrescribe el modo sandbox (true/false)

Ejemplo de solicitud con headers:
```bash
curl -X POST http://tu-servidor/api/v1/openfactura/dte/emit \
  -H "X-Openfactura-Api-Key: mi_api_key_personalizada" \
  -H "X-Openfactura-Sandbox: true" \
  -H "Content-Type: application/json" \
  -d '{
    "dteData": { ... },
    "responseOptions": ["PDF", "FOLIO", "TIMBRE"]
  }'
```

### Uso de Parámetros en el Body para Override

Alternativamente, puedes incluir los parámetros en el body de la solicitud:

- `api_key`: Sobrescribe la API Key predeterminada
- `sandbox`: Sobrescribe el modo sandbox (true/false)

Ejemplo de solicitud con parámetros en el body:
```bash
curl -X POST http://tu-servidor/api/v1/openfactura/dte/emit \
  -H "Content-Type: application/json" \
  -d '{
    "api_key": "mi_api_key_personalizada",
    "sandbox": true,
    "dteData": { ... },
    "responseOptions": ["PDF", "FOLIO", "TIMBRE"]
  }'
```

> **Nota**: Los headers tienen precedencia sobre los parámetros del body si se proporcionan ambos.

### Precedencia de Valores

1. **Headers HTTP**: `X-Openfactura-Api-Key` y `X-Openfactura-Sandbox` (máxima precedencia)
2. **Body de la solicitud**: `api_key` y `sandbox`
3. **Archivo .env**: Valores predeterminados si no se proporciona ninguno

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
