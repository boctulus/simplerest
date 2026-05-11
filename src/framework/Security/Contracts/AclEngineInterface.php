<?php

namespace Boctulus\Simplerest\Core\Security\Contracts;

use Boctulus\Simplerest\Core\Security\Domain\AclContext;

/**
 * Alias of AuthorizationServiceInterface.
 * Kept for backward compatibility — prefer AuthorizationServiceInterface in new code.
 */
interface AclEngineInterface extends AuthorizationServiceInterface
{
}
