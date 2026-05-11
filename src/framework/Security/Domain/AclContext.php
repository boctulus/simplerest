<?php

namespace Boctulus\Simplerest\Core\Security\Domain;

final class AclContext
{
    public function __construct(
        public readonly ?int   $userId         = null,
        public readonly array  $roles           = [],
        public readonly bool   $authenticated   = false,
        public readonly array  $userSpPerms     = [],
        public readonly array  $userTbPerms     = [],
        public readonly ?int   $rowId           = null,
        public readonly ?int   $folderId        = null,
    ) {}
}
