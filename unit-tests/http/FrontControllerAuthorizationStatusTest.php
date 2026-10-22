<?php

namespace Boctulus\Simplerest\Tests\Http;

use Boctulus\Simplerest\Core\FrontController;
use PHPUnit\Framework\TestCase;

class FrontControllerAuthorizationStatusTest extends TestCase
{
    public function test_guest_without_callable_permission_receives_unauthorized_status(): void
    {
        $this->assertSame(401, $this->authorizationFailureStatusCode(false));
    }

    public function test_authenticated_user_without_callable_permission_receives_forbidden_status(): void
    {
        $this->assertSame(403, $this->authorizationFailureStatusCode(true));
    }

    private function authorizationFailureStatusCode(bool $isAuthenticated): int
    {
        $method = new \ReflectionMethod(FrontController::class, 'authorizationFailureStatusCode');

        return $method->invoke(null, $isAuthenticated);
    }
}
