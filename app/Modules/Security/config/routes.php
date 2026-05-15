<?php

use Boctulus\Simplerest\Core\WebRouter;

$ctrl = 'Boctulus\Simplerest\Modules\Security\Controllers\AclInspectorController';

WebRouter::get('api/v1/acl/assignments',  $ctrl . '@assignments');
WebRouter::get('api/v1/acl/effective',    $ctrl . '@effective');
WebRouter::get('api/v1/acl/explain',      $ctrl . '@explain');
WebRouter::get('api/v1/acl/capabilities', $ctrl . '@capabilities');
WebRouter::get('api/v1/acl/resources',    $ctrl . '@resources');
WebRouter::get('api/v1/acl/user_lookup',  $ctrl . '@userLookup');
WebRouter::get('api/v1/acl/role_graph',   $ctrl . '@roleGraph');
