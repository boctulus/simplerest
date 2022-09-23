<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 
use simplerest\core\libs\Factory;

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
