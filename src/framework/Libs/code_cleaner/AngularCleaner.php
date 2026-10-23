<?php

namespace Boctulus\Simplerest\Core\Libs\code_cleaner;

/**
 * @deprecated The implementation is in boctulus/simplerest-scaffolding.
 */
$implementation = \Boctulus\Simplerest\CodeTools\Cleaners\AngularCleaner::class;

if (!class_exists($implementation)) {
    throw new \LogicException(
        'AngularCleaner is an optional development tool. Install SimpleRest Code Tools from its VCS repository.'
    );
}

class_alias($implementation, __NAMESPACE__ . '\\AngularCleaner');
