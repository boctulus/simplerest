<?php

namespace Boctulus\Simplerest\Controllers;

// Estoy usando siempre la misma versión de las APIs
use Boctulus\Simplerest\Core\API\v1\Download;

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