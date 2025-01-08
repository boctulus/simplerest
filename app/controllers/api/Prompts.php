<?php

namespace simplerest\controllers\api;

use simplerest\core\libs\Url;
use simplerest\core\libs\Files;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;
use simplerest\controllers\MyApiController; 

class Prompts extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    static protected $hidden = [

    ];

    static protected $hide_in_response = false;

    function __construct()
    {       
        parent::__construct();
    }     
    
    protected function onPostingAfterCheck($id, array &$data)
    { 
        $data['content'] = [];

        $base_path = $data['base_path'] ?? null;

        foreach ($data['files'] as $file_path) {
            $data['content'][] = Files::getContent($file_path, $base_path);
        }
    }
} 
