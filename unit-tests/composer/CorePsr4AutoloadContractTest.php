<?php

namespace Boctulus\Simplerest\Tests\composer;

use Boctulus\Simplerest\Core\Interfaces\IObserver;
use Boctulus\Simplerest\Core\Interfaces\ISubject;
use Boctulus\Simplerest\Core\Libs\EventBus;
use Boctulus\Simplerest\Core\Traits\EventBusTrait;
use PHPUnit\Framework\TestCase;

class CorePsr4AutoloadContractTest extends TestCase
{
    public function test_event_bus_symbols_autoload_with_canonical_namespace_case(): void
    {
        self::assertTrue(interface_exists(IObserver::class));
        self::assertTrue(interface_exists(ISubject::class));
        self::assertTrue(trait_exists(EventBusTrait::class));
        self::assertInstanceOf(ISubject::class, new EventBus());
    }
}
