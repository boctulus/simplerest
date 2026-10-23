<?php

namespace Boctulus\Simplerest\Core\Libs;

$implementation = \Boctulus\Simplerest\Integrations\Libs\BootstrapPaginator::class;

if (!class_exists($implementation)) {
    throw new \LogicException(
        'BootstrapPaginator is optional. Install boctulus/simplerest-bootstrap-html from a Composer VCS repository.'
    );
}

class_alias($implementation, __NAMESPACE__ . '\\BootstrapPaginator');
