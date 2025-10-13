<?php

namespace Boctulus\Simplerest\Controllers\api;

use Boctulus\Simplerest\Controllers\MyApiController; 

class Webhooks extends MyApiController
{ 
    static protected $soft_delete = true;

    function __construct()
    {       
        parent::__construct();
    }        
} 
