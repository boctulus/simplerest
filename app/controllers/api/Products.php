<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

class Products extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
        'product_categories',
        'users',
        'product_tags',
        'valoraciones',
        'digital_products'
    ];

    function __construct()
    {       
        parent::__construct();
        $GLOBALS['name_module'] = request()->shiftBodyParam('name_module');
    }       
} 
