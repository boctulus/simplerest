<?php

namespace Boctulus\Simplerest\Tests\Unit;

use Boctulus\Simplerest\Core\Handlers\ErrorHandler;
use PHPUnit\Framework\TestCase;

class ErrorHandlerStatusCodeProbe extends ErrorHandler
{
    public function resolveStatusCode(\Throwable $exception): int
    {
        return static::httpStatusCodeFrom($exception);
    }
}

class ErrorHandlerStatusCodeTest extends TestCase
{
    public function testOnlyValidHttpExceptionCodesAreUsedAsResponseStatuses(): void
    {
        $handler = new ErrorHandlerStatusCodeProbe();
        $cases = [
            [0, 500],
            [99, 500],
            [100, 100],
            [401, 401],
            [599, 599],
            [600, 500],
        ];

        foreach ($cases as [$exceptionCode, $expectedStatus]){
            $this->assertSame(
                $expectedStatus,
                $handler->resolveStatusCode(new \RuntimeException('Test error', $exceptionCode)),
                "Unexpected status for exception code {$exceptionCode}"
            );
        }
    }
}
