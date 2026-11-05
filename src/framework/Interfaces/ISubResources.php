<?php

declare(strict_types=1);
namespace Boctulus\Simplerest\Core\Interfaces;

Interface ISubResources {
    function getSubResources(string $table, Array $connect_to, ?Object &$instance = null, ?string $tenant_id = null, bool $pristine = false);
}
