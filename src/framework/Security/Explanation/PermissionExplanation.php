<?php

namespace Boctulus\Simplerest\Core\Security\Explanation;

final class PermissionExplanation
{
    public const SOURCE_ROLE              = 'role';
    public const SOURCE_USER_TB           = 'user_tb_permissions';
    public const SOURCE_USER_SP           = 'user_sp_permissions';
    public const SOURCE_SPECIAL_READ_ALL  = 'special:read_all';
    public const SOURCE_SPECIAL_WRITE_ALL = 'special:write_all';
    public const SOURCE_DENY_USER         = 'deny:user';
    public const SOURCE_DENY_ROLE         = 'deny:role';

    public const MODE_DIRECT      = 'direct';
    public const MODE_INHERITED   = 'inherited';
    public const MODE_REPLACEMENT = 'replacement';
    public const MODE_WILDCARD    = 'wildcard';
    public const MODE_DENY        = 'deny';

    public function __construct(
        public readonly string $resource,
        public readonly string $action,
        public readonly bool   $granted,
        public readonly string $source,
        public readonly string $mode,
        public readonly bool   $replacedRolePermissions = false,
    ) {}

    public function toArray(): array
    {
        return [
            'resource'                  => $this->resource,
            'action'                    => $this->action,
            'granted'                   => $this->granted,
            'source'                    => $this->source,
            'mode'                      => $this->mode,
            'replaced_role_permissions' => $this->replacedRolePermissions,
        ];
    }
}
