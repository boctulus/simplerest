# SimpleRest API Clients

`boctulus/simplerest-api-clients` groups optional clients for external APIs:

- `GoogleDrive` uses `google/apiclient` when Google Drive methods are called.
- `WooCommerce` and `WooCommerceApiClient` provide WooCommerce REST helpers.
- `OneSignal` provides notification API helpers.
- `HuggingFace` calls selected Hugging Face model endpoints.
- `Pastebin` provides legacy Pastebin helpers.
- `SendinBlue` sends transactional email through the legacy SendinBlue SDK.

The package is not registered on Packagist yet. Install it from GitHub for
now:

```bash
composer config repositories.simplerest-api-clients vcs https://github.com/boctulus/simplerest-api-clients
composer require boctulus/simplerest-api-clients:dev-main
```
Google's SDK remains an optional dependency; Composer does not copy third-party
source into this package.

`Pastebin` and `SendinBlue` keep their historical
`Boctulus\Simplerest\Core\Libs` class names and are registered by classmap.
The SendinBlue adapter also expects the host's SimpleRest mail services and
SendinBlue SDK classes to be available.

Existing PHP class names remain under
`Boctulus\Simplerest\Integrations\Libs` for compatibility. Install this
package only when an application uses one of these clients. TLS verification
uses the SimpleRest HTTP client's normal configuration.

Run the package contract tests with `composer test`.

The WooCommerce data mapper can be used without sending a request:

```php
$woocommerce = new WooCommerce([
    'base_url' => 'https://store.example',
    'consumer_key' => getenv('WC_CONSUMER_KEY'),
    'consumer_secret' => getenv('WC_CONSUMER_SECRET'),
]);

$payload = $woocommerce->transformProductData([
    'type' => 'simple',
    'name' => 'Blue T-shirt',
    'sku' => 'TSHIRT-BLUE',
    'description' => 'Cotton T-shirt',
    'categories' => ['Clothing'],
    'attributes' => ['Color' => ['Blue']],
]);
```
