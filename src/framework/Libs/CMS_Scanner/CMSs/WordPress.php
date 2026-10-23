<?php

namespace Boctulus\Simplerest\Core\Libs\CMS_Scanner\CMSs;

$implementation = \Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\CMSs\WordPress::class;

if (!class_exists($implementation)) {
    throw new \LogicException(
        'WordPress is optional. Install boctulus/simplerest-web-inspection from a Composer VCS repository.'
    );
}

class_alias($implementation, __NAMESPACE__ . '\\WordPress');
