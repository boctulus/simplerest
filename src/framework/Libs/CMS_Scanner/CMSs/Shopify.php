<?php

namespace Boctulus\Simplerest\Core\Libs\CMS_Scanner\CMSs;

$implementation = \Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\CMSs\Shopify::class;

if (!class_exists($implementation)) {
    throw new \LogicException(
        'Shopify is optional. Install boctulus/simplerest-web-inspection from a Composer VCS repository.'
    );
}

class_alias($implementation, __NAMESPACE__ . '\\Shopify');
