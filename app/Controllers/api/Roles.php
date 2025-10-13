<?php

namespace Boctulus\Simplerest\Controllers\api;

use Boctulus\Simplerest\Controllers\MyApiController; 
use Boctulus\Simplerest\Core\Libs\Factory;

class Roles extends MyApiController
{
    function __construct()
    {
        parent::__construct();
    }

    function create($id = null){
        error('Not implemented', 501, "Roles are read-only");
    }

    function put($id = null){
        error('Not implemented', 501, "Roles are read-only");
    }

    function patch($id = null){
        error('Not implemented', 501, "Roles are read-only");
    }

    function delete($id = null){
        error('Not implemented', 501, "Roles are read-only");
    }

} // end class
