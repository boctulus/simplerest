<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Libs\Impersonation;

/**
 * Immutable snapshot of the impersonation state carried by the access token.
 *
 * The JWT controller adds `impersonated_by` to the token payload when an
 * administrator acts as another user. Every write performed in that session
 * must be rejected: the administrator is looking through someone else's eyes,
 * not mutating their data.
 */
final class ImpersonationRequestContext
{
    private function __construct(
        public readonly ?int $impersonatedBy,
        public readonly bool $readOnly,
    ) {
    }

    /**
     * @param array<string, mixed> $auth authenticated payload exposed by IAuth::check()
     */
    public static function hydrateFromToken(array $auth): self
    {
        $impersonatedBy = $auth['impersonated_by'] ?? null;

        if ($impersonatedBy === null && isset($auth['payload']) && is_array($auth['payload'])) {
            $impersonatedBy = $auth['payload']['impersonated_by'] ?? null;
        }

        if ($impersonatedBy === null || $impersonatedBy === '' || $impersonatedBy === 0 || $impersonatedBy === '0') {
            return new self(null, false);
        }

        return new self((int) $impersonatedBy, true);
    }

    public function isImpersonating(): bool
    {
        return $this->impersonatedBy !== null;
    }

    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }
}
