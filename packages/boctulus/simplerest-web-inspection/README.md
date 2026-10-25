# SimpleRest Web Inspection

`boctulus/simplerest-web-inspection` identifies common CMS and web technology
markers and offers Symfony based HTML selector helpers.

It contains `CMS_Scanner` and its Magento, PrestaShop, Shopify, Squarespace,
Wix, and WordPress detectors, plus `DomCrawler` for querying links, text,
attributes, and HTML fragments. The scanner may fetch a public page; its
request failures are reported as exceptions and do not terminate the host
application.

The package is not registered on Packagist yet. Install it from GitHub for
now:

```bash
composer config repositories.simplerest-web-inspection vcs https://github.com/boctulus/simplerest-web-inspection
composer require boctulus/simplerest-web-inspection:dev-main
```

Implementation classes live under `Boctulus\Simplerest\Integrations\Libs`.
`src/legacy_aliases.php` preserves their previous `Boctulus\Simplerest\Core\Libs`
class names when those names are not already provided by the installed core.
This package does not include API clients or HTML presentation builders.

```php
use Boctulus\Simplerest\Integrations\Libs\DomCrawler;

$crawler = new DomCrawler('<h1>Catalog</h1><a href="/items">Items</a>');
$title = $crawler->getText('h1');
$links = $crawler->getLinks();
```

Run the package contract tests with `composer test`.
