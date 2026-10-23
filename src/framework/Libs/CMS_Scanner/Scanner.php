<?php

namespace Boctulus\Simplerest\Core\Libs\CMS_Scanner;

$implementation = \Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\Scanner::class;

if (!class_exists($implementation)) {
    throw new \LogicException(
        'Scanner is optional. Install boctulus/simplerest-web-inspection from a Composer VCS repository.'
    );
}

class_alias($implementation, __NAMESPACE__ . '\\Scanner');
