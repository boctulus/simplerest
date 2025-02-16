<?php

namespace simplerest\controllers\api;

use simplerest\core\libs\Url;
use simplerest\core\libs\Files;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;
use simplerest\controllers\MyApiController; 
use simplerest\core\exceptions\NotFileButDirectoryException;
use simplerest\core\libs\Logger;

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
        // maximo numero de archivos a cargar de un directorio
        $max_files = 100; 

        $data['content'] = [];

        $base_path = $data['base_path'] ?? null;

        $data['files'] = array_unique($data['files']);

        foreach ($data['files'] as $file_path) {
            try {
                $data['content'][$file_path] = Files::getContent($file_path, $base_path);
            } catch (NotFileButDirectoryException $e){
                if ($base_path !== null && !Files::isAbsolutePath($file_path)) {
                    $path = Files::removeFirstSlash($file_path);
                    $path = Files::addTrailingSlash($base_path) . DIRECTORY_SEPARATOR . $file_path;
                } else {
                    $path = $file_path;
                }

                // Logger::dd($path, 'PATH');

                $files = Files::recursiveGlob($path . Files::convertSlashes('/') . '*.*');

                if (count($files) > $max_files){
                    throw new \Exception("So many files to read");
                }
                
                // Logger::dd($files, 'FILEs');

                /*
                    Cuando es un directorio toca todavia en la el JS de la vista manejarlo:
                */
                foreach ($files as $file){  
                    $data['content'][$file] = file_get_contents($file);
                    // Logger::dd($file, 'FILE');
                }
            } 
            
        }
    }
} 
