<?php

namespace simplerest\controllers\api;

use simplerest\controllers\MyApiController; 
use simplerest\core\libs\Files;

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

        if (isset($data['base_path'])){
            $base_path = Files::addTrailingSlash($data['base_path']);
        
            foreach ($data['files'] as $file_path){
                if (!Files::isAbsolutePath($file_path)){
                    $file_path         = Files::removeFirstSlash($file_path);
                    $data['content'][] = Files::getContentOrFail($base_path . DIRECTORY_SEPARATOR . $file_path);
                } else {
                    $data['content'][] = Files::getContentOrFail($file_path);
                }                
            }
        } else {
            foreach ($data['files'] as $file_path){
                $data['content'][] = Files::getContentOrFail($file_path);
            }
        }
        
    }
} 
