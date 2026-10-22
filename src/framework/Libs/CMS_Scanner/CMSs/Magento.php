<?php

namespace Boctulus\Simplerest\Core\Libs\CMS_Scanner\CMSs;

$implementation = \Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\CMSs\Magento::class;

if (!class_exists($implementation)) {
    throw new \LogicException(
        'Magento is optional. Install boctulus/simplerest-integrations from a Composer VCS repository.'
    );
}

class_alias($implementation, __NAMESPACE__ . '\\Magento');
