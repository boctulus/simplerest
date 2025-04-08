<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\src\Traits;

use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Traits\ErrorReporting;

Trait FilesTrait 
{
    /**
     * Encuentra todos los archivos relevantes para el análisis
     */
    private function findRelevantFiles($pattern, $excludePaths = [])
    {        
        return Files::recursiveGlob(
            $this->rootPath . DIRECTORY_SEPARATOR . $pattern,
            0,
            $excludePaths
        );
    }

    private function findCodeFiles(){
        $pattern = '*.java, *.kt';
        return $this->findRelevantFiles($pattern, $this->excludePaths);
    }

        /**
     * Escanea un directorio y devuelve una lista de archivos
     * 
     * @param string $directory Ruta del directorio a escanear
     * @return array Lista de archivos
     */
    private function scanDirectory($directory)
    {
        $files = [];

        if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $files[] = $file;
                }
            }
            closedir($handle);
        }

        return $files;
    }    

   
    /**
     * Método auxiliar para obtener los archivos fuente
     * Utiliza la configuración ya establecida en la clase AndroidCodeAnalyzer
     */
    private function getSourceFiles($patterns)
    {
        $files = [];
        foreach ($patterns as $pattern) {
            $files = array_merge(
                $files,
                Files::recursiveGlob(
                    $this->rootPath . DIRECTORY_SEPARATOR . $pattern,
                    0,
                    $this->excludePaths
                )
            );
        }
        return $files;
    }

    /**
     * Método auxiliar para extraer el nombre de la clase de un archivo
     */
    private function extractClassName($file, $content)
    {
        $className = basename($file);
        // Extraer el nombre de la clase del contenido
        if (preg_match('/\b(?:class|interface|object)\s+([A-Za-z0-9_]+)/', $content, $matches)) {
            $className = $matches[1];
        }
        return $className;
    }
}