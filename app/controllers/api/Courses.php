<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Core\Api\ApiController; 

class Courses extends ApiController
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
