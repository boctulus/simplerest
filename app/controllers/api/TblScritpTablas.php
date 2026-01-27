<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Core\Api\ApiController; 

class TblScritpTablas extends ApiController
{ 
    static protected $soft_delete = true;

    function __construct()
    {       
        parent::__construct();
    }        
} 
