<?php

namespace Boctulus\Simplerest\Core\Libs\code_cleaner;

/**
 * @deprecated The implementation is in boctulus/simplerest-scaffolding.
 */
$implementation = \Boctulus\Simplerest\CodeTools\Cleaners\BootstrapCleaner::class;

if (!class_exists($implementation)) {
    throw new \LogicException(
        'BootstrapCleaner is an optional development tool. Install SimpleRest Code Tools from its VCS repository.'
    );
}

class_alias($implementation, __NAMESPACE__ . '\\BootstrapCleaner');
