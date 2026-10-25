<?php

declare(strict_types=1);

$legacyClassAliases = [
    \Boctulus\Simplerest\Core\Libs\CMS_Scanner\Scanner::class
        => \Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\Scanner::class,
    \Boctulus\Simplerest\Core\Libs\CMS_Scanner\CMSs\Magento::class
        => \Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\CMSs\Magento::class,
    \Boctulus\Simplerest\Core\Libs\CMS_Scanner\CMSs\PrestaShop::class
        => \Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\CMSs\PrestaShop::class,
    \Boctulus\Simplerest\Core\Libs\CMS_Scanner\CMSs\Shopify::class
        => \Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\CMSs\Shopify::class,
    \Boctulus\Simplerest\Core\Libs\CMS_Scanner\CMSs\Squarespace::class
        => \Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\CMSs\Squarespace::class,
    \Boctulus\Simplerest\Core\Libs\CMS_Scanner\CMSs\Wix::class
        => \Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\CMSs\Wix::class,
    \Boctulus\Simplerest\Core\Libs\CMS_Scanner\CMSs\WordPress::class
        => \Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\CMSs\WordPress::class,
    \Boctulus\Simplerest\Core\Libs\DomCrawler::class
        => \Boctulus\Simplerest\Integrations\Libs\DomCrawler::class,
];

foreach ($legacyClassAliases as $legacyClass => $implementationClass) {
    if (class_exists($legacyClass) || !class_exists($implementationClass)) {
        continue;
    }

    class_alias($implementationClass, $legacyClass);
}
