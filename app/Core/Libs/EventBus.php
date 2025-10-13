<?php

namespace Boctulus\Simplerest\core\libs;

use Boctulus\Simplerest\Core\Interfaces\ISubject;
use Boctulus\Simplerest\Core\Traits\EventBusTrait;

/**
 * Class EventBus
 *
 * An implementation of the observer pattern using the EventBusTrait.
 *
 * Author: Pablo Bozzolo
 */
class EventBus implements ISubject {
    use EventBusTrait;
}
