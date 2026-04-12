<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Core\Api\ApiController; 

class TblGenero extends ApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		'tbl_usuario',
        'tbl_estado'
	];

    function __construct()
    {       
        parent::__construct();
    }        
} 
