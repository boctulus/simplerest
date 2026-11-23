<?php

declare(strict_types=1);

use Boctulus\Simplerest\Core\Libs\Impersonation\ImpersonationManager;
use Boctulus\Simplerest\Core\Libs\Impersonation\ImpersonationRequestContext;
use PHPUnit\Framework\TestCase;

/**
 * Regression for docs/issues/api-controller-missing-impersonation-types.md:
 * ApiController imports and calls these types unconditionally, so they must
 * exist and enforce read-only impersonation before any action runs.
 */
final class ImpersonationTest extends TestCase
{
    public function testTheTypesRequiredByApiControllerExist(): void
    {
        self::assertTrue(class_exists(ImpersonationRequestContext::class));
        self::assertTrue(class_exists(ImpersonationManager::class));
    }

    public function testEmptyAuthIsNotImpersonating(): void
    {
        $context = ImpersonationRequestContext::hydrateFromToken([]);

        self::assertFalse($context->isImpersonating());
        self::assertFalse($context->isReadOnly());
        self::assertNull($context->impersonatedBy);
    }

    public function testTokenPayloadHydratesTheImpersonator(): void
    {
        $context = ImpersonationRequestContext::hydrateFromToken(['impersonated_by' => 42]);

        self::assertTrue($context->isImpersonating());
        self::assertTrue($context->isReadOnly());
        self::assertSame(42, $context->impersonatedBy);
    }

    public function testNestedPayloadIsAlsoAccepted(): void
    {
        $context = ImpersonationRequestContext::hydrateFromToken([
            'payload' => ['impersonated_by' => '7'],
        ]);

        self::assertSame(7, $context->impersonatedBy);
    }

    public function testZeroAndEmptyValuesMeanNoImpersonation(): void
    {
        self::assertFalse(ImpersonationRequestContext::hydrateFromToken(['impersonated_by' => 0])->isImpersonating());
        self::assertFalse(ImpersonationRequestContext::hydrateFromToken(['impersonated_by' => ''])->isImpersonating());
        self::assertFalse(ImpersonationRequestContext::hydrateFromToken(['impersonated_by' => null])->isImpersonating());
    }

    /**
     * @dataProvider writeMethodProvider
     */
    public function testImpersonatedWritesAreRejectedWith403(string $method): void
    {
        $context = ImpersonationRequestContext::hydrateFromToken(['impersonated_by' => 1]);

        try {
            ImpersonationManager::getInstance()->enforceReadOnly($context, $method, '/api/v1/items/5', 'items');
            self::fail("$method should have been rejected");
        } catch (RuntimeException $exception) {
            self::assertSame(403, $exception->getCode());
            self::assertStringContainsString('read-only', $exception->getMessage());
        }
    }

    public static function writeMethodProvider(): array
    {
        return [
            'post' => ['POST'],
            'put' => ['PUT'],
            'patch' => ['PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    public function testReadsAreAllowedWhileImpersonating(): void
    {
        $context = ImpersonationRequestContext::hydrateFromToken(['impersonated_by' => 1]);

        ImpersonationManager::getInstance()->enforceReadOnly($context, 'GET', '/api/v1/items', 'items');

        self::assertTrue($context->isImpersonating());
    }

    public function testWritesOutsideTheApiAreAllowed(): void
    {
        $context = ImpersonationRequestContext::hydrateFromToken(['impersonated_by' => 1]);

        ImpersonationManager::getInstance()->enforceReadOnly($context, 'POST', '/web/forms/contact', 'contacts');

        self::assertTrue($context->isImpersonating());
    }

    public function testWritesWithoutImpersonationAreAllowed(): void
    {
        $context = ImpersonationRequestContext::hydrateFromToken([]);

        ImpersonationManager::getInstance()->enforceReadOnly($context, 'DELETE', '/api/v1/items/5', 'items');

        self::assertFalse($context->isImpersonating());
    }

    public function testManagerIsASingleton(): void
    {
        self::assertSame(ImpersonationManager::getInstance(), ImpersonationManager::getInstance());
    }
}
