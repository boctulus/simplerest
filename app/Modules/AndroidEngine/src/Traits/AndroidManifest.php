<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\Src\Traits;

use Boctulus\Simplerest\Core\Traits\ErrorReporting;

Trait AndroidManifest 
{
    use ErrorReporting;
    
    /**
     * Lista los permisos definidos en AndroidManifest.xml
     * 
     * @return array|null Array con los permisos o null si hay un error
     * @throws \Exception Si no se encuentra el archivo AndroidManifest.xml
     */
    public function getPermissions()
    {
        if ($this->rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }

        $manifestPath = $this->rootPath . '/AndroidManifest.xml';

        if (!file_exists($manifestPath)) {
            $manifestPath = $this->rootPath . '/app/src/main/AndroidManifest.xml';
            if (!file_exists($manifestPath)) {
                throw new \Exception("No se encontró el archivo AndroidManifest.xml");
            }
        }

        $content = file_get_contents($manifestPath);
        if ($content === false) {
            throw new \Exception("No se pudo leer el archivo AndroidManifest.xml");
        }

        $permissions = [];
        if (preg_match_all('/<uses-permission\s+android:name="([^"]+)"/', $content, $matches)) {
            $permissions = $matches[1];
        } else {
            static::addError("No se encontraron permisos definidos en AndroidManifest.xml");
        }

        return $permissions;
    }   

    public function getManifestPath(){
        $manifestPath = $this->rootPath . '/app/src/main/AndroidManifest.xml';
        return $manifestPath;
    }
}