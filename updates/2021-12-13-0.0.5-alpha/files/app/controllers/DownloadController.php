<?php

namespace simplerest\controllers;

// Estoy usando siempre la misma versión de las APIs
use simplerest\core\api\v1\Download;

class DownloadController extends Download
{
    function files  ($id){
        return $this->get($id);  
    }

    function updates($id){
        $this->table_name = 'updates';
        return $this->get($id);
    }
}