<?php

namespace Boctulus\Simplerest\Tests\SubResources;

use Boctulus\Simplerest\Core\Api\ApiController;
use Boctulus\Simplerest\Core\Interfaces\ISubResources;
use Boctulus\Simplerest\Core\Traits\SubResourceHandler;
use PHPUnit\Framework\TestCase;

class ISubResourcesContractTest extends TestCase
{
    public function test_pristine_parameter_is_part_of_the_subresource_interface_contract(): void
    {
        $method = new \ReflectionMethod(ISubResources::class, 'getSubResources');
        $parameters = $method->getParameters();

        self::assertCount(5, $parameters);
        self::assertSame('pristine', $parameters[4]->getName());
        self::assertTrue($parameters[4]->isOptional());
        self::assertFalse($parameters[4]->getDefaultValue());

        $traitMethod = new \ReflectionMethod(SubResourceHandler::class, 'getSubResources');
        self::assertSame($method->getNumberOfParameters(), $traitMethod->getNumberOfParameters());

        self::assertTrue(is_subclass_of(ApiController::class, ISubResources::class));
    }
}
