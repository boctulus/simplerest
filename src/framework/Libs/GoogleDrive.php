<?php

namespace Boctulus\Simplerest\Core\Libs;

$implementation = \Boctulus\Simplerest\Integrations\Libs\GoogleDrive::class;

if (!class_exists($implementation)) {
    throw new \LogicException(
        'GoogleDrive is optional. Install boctulus/simplerest-api-clients from a Composer VCS repository.'
    );
}

class_alias($implementation, __NAMESPACE__ . '\\GoogleDrive');
