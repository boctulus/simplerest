<?php

namespace Boctulus\Simplerest\Core\Libs;

$implementation = \Boctulus\Simplerest\Integrations\Libs\WooCommerceApiClient::class;

if (!class_exists($implementation)) {
    throw new \LogicException(
        'WooCommerceApiClient is optional. Install boctulus/simplerest-integrations from a Composer VCS repository.'
    );
}

class_alias($implementation, __NAMESPACE__ . '\\WooCommerceApiClient');
