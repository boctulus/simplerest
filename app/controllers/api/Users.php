<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Controllers\MyApiController; 

class Users extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    static protected $hidden = [
        //'username',
        // 'firstname',
        // 'lastname',
        // 'is_active',
        // 'is_locked',
        // 'address',
        'belongs_to',
        'email',
        'confirmed_email',
        'created_at',
        'updated_at',
        'updated_by',
        'deleted_by'
    ];

    // Si es true, el campo desaparece de los GETs
    static protected $hide_in_response = true;

    function __construct()
    {       
        parent::__construct();
    }        
} 
