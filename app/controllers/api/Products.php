<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

class Products extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connectable = [
        'product_categories'
    ];

    function __construct()
    {       
        parent::__construct();
    }        
} 
