<?php

namespace Boctulus\Simplerest\Core\Interfaces;

Interface ISubResources {
    function getSubResources(string $table, Array $connect_to, ?Object &$instance = null, ?string $tenant_id = null);
} 