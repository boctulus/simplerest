<?php

namespace Boctulus\Simplerest\Core\Security\Contracts;

use Boctulus\Simplerest\Core\Security\Domain\AclContext;

/**
 * A self-contained authorization rule.
 * Encapsulates the "what is required" so endpoints declare intent,
 * not role names or permission strings.
 *
 * Example:
 *   class DeleteUserPolicy implements AuthorizationPolicyInterface {
 *       public function isSatisfiedBy(AclContext $ctx, AclEngineInterface $engine): bool {
 *           return $engine->can($ctx, 'delete', 'users');
 *       }
 *   }
 */
interface AuthorizationPolicyInterface
{
    public function isSatisfiedBy(
        AclContext          $context,
        AclEngineInterface  $engine
    ): bool;
}
