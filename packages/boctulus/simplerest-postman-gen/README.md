# SimpleRest Postman Generator

`boctulus/simplerest-postman-gen` provides the `PostmanGenerator` helper for
building Postman collection JSON from SimpleRest endpoints.

The existing class name remains available:

```php
use Boctulus\Simplerest\Core\Libs\PostmanGenerator;

PostmanGenerator::setCollectionName('Public API');
PostmanGenerator::addEndpoints(['users'], [PostmanGenerator::GET]);
PostmanGenerator::generate();
```

`generate()` writes a collection file to the configured destination. The host
application must supply SimpleRest core configuration and endpoint data.

## Requirements

- PHP 8.1 through 8.4
- `boctulus/simplerest` 2.x or 3.x

Composer maps the legacy class from `src/PostmanGenerator.php`.

Run the package contract tests with `composer test`.
