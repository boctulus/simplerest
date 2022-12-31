<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

class Products extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
        //'users',
        'product_categories',
        'product_comments'
    ];

    static protected $hidden = [

    ];

    // Si es true, el campo desaparece de los GETs
    static protected $hide_in_response = false;

    function __construct()
    {       
        parent::__construct();
    }        
} 
