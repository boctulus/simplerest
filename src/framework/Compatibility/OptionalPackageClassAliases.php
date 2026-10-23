<?php

declare(strict_types=1);

/**
 * Preserve legacy class names when Composer's authoritative class map is enabled.
 *
 * The per-class compatibility files remain useful with ordinary PSR-4 loading,
 * but Composer cannot discover their conditional class_alias() calls while
 * building an optimized class map. This loader handles those names after the
 * Composer loader declines them.
 */
$optionalClassAliases = [
    'Boctulus\\Simplerest\\Core\\Libs\\HtmlBuilder\\Bt5Form' => [
        'Boctulus\\Simplerest\\HtmlBuilders\\Bt5Form',
        'boctulus/simplerest-bootstrap-html',
    ],
    'Boctulus\\Simplerest\\Core\\Libs\\HtmlBuilder\\AdminLte' => [
        'Boctulus\\Simplerest\\HtmlBuilders\\AdminLte',
        'boctulus/simplerest-bootstrap-html',
    ],
    'Boctulus\\Simplerest\\Core\\Libs\\BootstrapPaginator' => [
        'Boctulus\\Simplerest\\Integrations\\Libs\\BootstrapPaginator',
        'boctulus/simplerest-bootstrap-html',
    ],
    'Boctulus\\Simplerest\\Core\\Libs\\DomCrawler' => [
        'Boctulus\\Simplerest\\Integrations\\Libs\\DomCrawler',
        'boctulus/simplerest-web-inspection',
    ],
    'Boctulus\\Simplerest\\Core\\Libs\\GoogleDrive' => [
        'Boctulus\\Simplerest\\Integrations\\Libs\\GoogleDrive',
        'boctulus/simplerest-api-clients',
    ],
    'Boctulus\\Simplerest\\Core\\Libs\\HuggingFace' => [
        'Boctulus\\Simplerest\\Integrations\\Libs\\HuggingFace',
        'boctulus/simplerest-api-clients',
    ],
    'Boctulus\\Simplerest\\Core\\Libs\\OneSignal' => [
        'Boctulus\\Simplerest\\Integrations\\Libs\\OneSignal',
        'boctulus/simplerest-api-clients',
    ],
    'Boctulus\\Simplerest\\Core\\Libs\\WooCommerce' => [
        'Boctulus\\Simplerest\\Integrations\\Libs\\WooCommerce',
        'boctulus/simplerest-api-clients',
    ],
    'Boctulus\\Simplerest\\Core\\Libs\\WooCommerceApiClient' => [
        'Boctulus\\Simplerest\\Integrations\\Libs\\WooCommerceApiClient',
        'boctulus/simplerest-api-clients',
    ],
    'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\Scanner' => [
        'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\Scanner',
        'boctulus/simplerest-web-inspection',
    ],
    'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\Magento' => [
        'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\Magento',
        'boctulus/simplerest-web-inspection',
    ],
    'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\PrestaShop' => [
        'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\PrestaShop',
        'boctulus/simplerest-web-inspection',
    ],
    'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\Shopify' => [
        'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\Shopify',
        'boctulus/simplerest-web-inspection',
    ],
    'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\Squarespace' => [
        'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\Squarespace',
        'boctulus/simplerest-web-inspection',
    ],
    'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\Wix' => [
        'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\Wix',
        'boctulus/simplerest-web-inspection',
    ],
    'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\WordPress' => [
        'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\WordPress',
        'boctulus/simplerest-web-inspection',
    ],
    'Boctulus\\Simplerest\\Core\\Libs\\code_cleaner\\AngularCleaner' => [
        'Boctulus\\Simplerest\\CodeTools\\Cleaners\\AngularCleaner',
        'boctulus/simplerest-scaffolding',
    ],
    'Boctulus\\Simplerest\\Core\\Libs\\code_cleaner\\BootstrapCleaner' => [
        'Boctulus\\Simplerest\\CodeTools\\Cleaners\\BootstrapCleaner',
        'boctulus/simplerest-scaffolding',
    ],
];

spl_autoload_register(static function (string $class) use ($optionalClassAliases): void {
    if (!isset($optionalClassAliases[$class])) {
        return;
    }

    [$implementation, $package] = $optionalClassAliases[$class];
    if (!class_exists($implementation)) {
        throw new LogicException(sprintf(
            '%s is optional. Install %s through Composer before using it.',
            $class,
            $package
        ));
    }

    class_alias($implementation, $class);
});
