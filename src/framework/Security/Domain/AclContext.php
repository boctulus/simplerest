<?php

namespace Boctulus\Simplerest\Core\Security\Domain;

use Boctulus\Simplerest\Core\Security\Compiler\EffectivePermissionCompiler;
use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;

final class AclContext
{
    public function __construct(
        public readonly ?int   $userId              = null,
        public readonly array  $roles               = [],
        public readonly bool   $authenticated       = false,
        public readonly array  $userSpPerms         = [],
        public readonly array  $userTbPerms         = [],
        public readonly ?int   $rowId               = null,
        public readonly ?int   $folderId            = null,
        public readonly ?array $compiledPermissions = null,
        public readonly array  $userDenyPerms       = [],
        public readonly array  $userDenySpPerms     = [],
    ) {}

    /**
     * Eager factory: compile per-user effective permissions from the snapshot
     * and return a context with `compiledPermissions` populated for O(1)
     * runtime evaluation.
     */
    public static function withCompiled(
        AclSnapshot                  $snapshot,
        EffectivePermissionCompiler  $compiler,
        ?int                         $userId,
        array                        $roles,
        bool                         $authenticated,
        array                        $userSpPerms     = [],
        array                        $userTbPerms     = [],
        array                        $userDenyPerms   = [],
        array                        $userDenySpPerms = [],
        ?int                         $rowId           = null,
        ?int                         $folderId        = null,
    ): self {
        $compiled = $compiler->compileForUser(
            $snapshot,
            $roles,
            $userSpPerms,
            $userTbPerms,
            $userDenyPerms,
            $userDenySpPerms
        );

        return new self(
            userId:              $userId,
            roles:               $roles,
            authenticated:       $authenticated,
            userSpPerms:         $userSpPerms,
            userTbPerms:         $userTbPerms,
            rowId:               $rowId,
            folderId:            $folderId,
            compiledPermissions: $compiled,
            userDenyPerms:       $userDenyPerms,
            userDenySpPerms:     $userDenySpPerms,
        );
    }
}
