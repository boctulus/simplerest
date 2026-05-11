<?php

namespace Boctulus\Simplerest\Core\Security\Contracts;

use Boctulus\Simplerest\Core\Security\Snapshot\AclSnapshot;

interface AclSnapshotInterface
{
    public function getSnapshot(): AclSnapshot;
}
