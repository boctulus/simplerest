<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\src\Traits;

use Boctulus\Simplerest\Core\Libs\Files;

Trait FilesTrait 
{
    /**
     * Encuentra todos los archivos relevantes para el análisis
     */
    function findRelevantFiles($pattern, $excludePaths = null)
    {        
        if ($excludePaths === null){
            $excludePaths  = [];
        }

        if (is_array($excludePaths)){
            $excludePaths = implode('|', $excludePaths);
        }        

        return Files::recursiveGlob(
            $this->rootPath . DIRECTORY_SEPARATOR . $pattern,
            0,
            $excludePaths
        );
    }

    /**
     * Encuentra todos los archivos *.java y *.kt en el proyecto Android
     * (busqueda recursiva)
     */
    function findCodeFiles(){
        $pattern = '*.java|*.kt';
        return $this->findRelevantFiles($pattern, $this->excludePaths);
    }

    function scanDirectory(string $dir){
        return Files::scanDirectory($dir);
    }

    /**
     * Método auxiliar para extraer el nombre de la clase de un archivo
     */
    function extractClassName($filename, $content)
    {        
        return Files::extractClassName($filename, $content);
    }
}