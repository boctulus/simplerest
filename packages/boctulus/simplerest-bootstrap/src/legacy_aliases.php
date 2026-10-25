<?php

declare(strict_types=1);

use Boctulus\Simplerest\Core\Libs\BootstrapPaginator as LegacyBootstrapPaginator;
use Boctulus\Simplerest\Core\Libs\HtmlBuilder\AdminLte as LegacyAdminLte;
use Boctulus\Simplerest\Core\Libs\HtmlBuilder\Bt5Form as LegacyBt5Form;
use Boctulus\Simplerest\Core\Libs\HtmlBuilder\Html as LegacyHtml;
use Boctulus\Simplerest\Core\Libs\HtmlBuilder\Tag as LegacyTag;
use Boctulus\Simplerest\HtmlBuilder\Html;
use Boctulus\Simplerest\HtmlBuilder\Tag;
use Boctulus\Simplerest\HtmlBuilders\AdminLte;
use Boctulus\Simplerest\HtmlBuilders\Bt5Form;
use Boctulus\Simplerest\Integrations\Libs\BootstrapPaginator;

$aliases = [
    LegacyHtml::class => Html::class,
    LegacyTag::class => Tag::class,
    LegacyBt5Form::class => Bt5Form::class,
    LegacyAdminLte::class => AdminLte::class,
    LegacyBootstrapPaginator::class => BootstrapPaginator::class,
];

foreach ($aliases as $legacyClass => $packageClass) {
    if (!class_exists($legacyClass) && class_exists($packageClass)) {
        class_alias($packageClass, $legacyClass);
    }
}
