<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\src\Traits\Resources;

use Boctulus\Simplerest\Core\Traits\ErrorReporting;
use Boctulus\Simplerest\Modules\AndroidEngine\src\Traits\XML;

Trait Drawables 
{
    use ErrorReporting;
    use XML; 

    /**
     * Lista los drawables del proyecto
     * 
     * @return array|null Array con rutas a los archivos drawable o null si hay un error
     * @throws \Exception Si no se encuentra el directorio drawable
     */
    public function getDrawables()
    {
        if ($this->rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }

        $drawablePath = $this->rootPath . '/app/src/main/res/drawable';
        if (!is_dir($drawablePath)) {
            $drawablePath = $this->rootPath . '/res/drawable';
            if (!is_dir($drawablePath)) {
                throw new \Exception("No se encontró el directorio drawable");
            }
        }

        $drawables = [];

        // Buscar en drawable básico
        if (is_dir($drawablePath)) {
            $drawables['drawable'] = static::scanDirectory($drawablePath);
        }

        // Buscar en otros directorios drawable-*
        $resourcesPath = dirname($drawablePath);
        if ($handle = opendir($resourcesPath)) {
            while (false !== ($dir = readdir($handle))) {
                if (preg_match('/^drawable-/', $dir) && is_dir($resourcesPath . '/' . $dir)) {
                    $drawables[$dir] = static::scanDirectory($resourcesPath . '/' . $dir);
                }
            }
            closedir($handle);
        }

        if (empty($drawables)) {
            static::addError("No se encontraron drawables");
            return null;
        }

        return $drawables;
    }
}