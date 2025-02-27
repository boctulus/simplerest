<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 

class Courses extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
        'users', 'tags', 'categories'
	];

    static protected $hidden = [

    ];

    static protected $hide_in_response = false;

    function __construct()
    {       
        parent::__construct();
    }        
} 
