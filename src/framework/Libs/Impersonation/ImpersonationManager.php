<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Libs\Impersonation;

/**
 * Enforces the impersonation write policy at the API boundary.
 *
 * ApiController calls this before dispatching any action, so hiding buttons in
 * a UI is not the security boundary: a direct `curl -X PUT` is rejected here.
 */
final class ImpersonationManager
{
    private const WRITE_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    private static ?self $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * @throws \RuntimeException with HTTP code 403 when an impersonated session writes
     */
    public function enforceReadOnly(
        ImpersonationRequestContext $context,
        string $method,
        string $path,
        string $table
    ): void {
        if (!$context->isImpersonating() || !$context->isReadOnly()) {
            return;
        }

        if (!in_array(strtoupper($method), self::WRITE_METHODS, true)) {
            return;
        }

        if (!$this->isApiPath($path)) {
            return;
        }

        throw new \RuntimeException(
            sprintf(
                'Impersonated sessions are read-only: %s %s on "%s" is not allowed.',
                strtoupper($method),
                $path,
                $table
            ),
            403
        );
    }

    private function isApiPath(string $path): bool
    {
        $path = '/' . ltrim($path, '/');

        return str_contains($path, '/api/');
    }
}
