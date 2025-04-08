<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\src\Traits;

use Boctulus\Simplerest\Core\Traits\ErrorReporting;

Trait DeviceOrientation 
{
    use ErrorReporting;
    use FilesTrait;
    
    /**
     * Determina la orientación predeterminada de la aplicación
     * 
     * @return string|null 'portrait', 'landscape' o null si no se puede determinar
     * @throws \Exception Si no se encuentra el archivo AndroidManifest.xml
     */
    public function getDefaultOrientation()
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

        // Buscar orientación a nivel de aplicación
        if (preg_match('/<application[^>]*android:screenOrientation="([^"]+)"/', $content, $matches)) {
            $this->orientation = static::normalizeOrientation($matches[1]);
            return $this->orientation;
        }

        // Buscar orientación en la actividad principal
        if (preg_match('/<activity[^>]*android:name=".MainActivity"[^>]*android:screenOrientation="([^"]+)"/', $content, $matches)) {
            $this->orientation = static::normalizeOrientation($matches[1]);
            return $this->orientation;
        }

        // Si no se encuentra una orientación explícita
        static::addError("No se encontró una orientación explícita en el AndroidManifest.xml", static::SEVERITY_WARNING);
        $this->orientation = null;
        return null;
    }

    /**
     * Normaliza los valores de orientación a 'portrait' o 'landscape'
     * 
     * @param string $orientation Valor de orientación original
     * @return string 'portrait' o 'landscape'
     */
    private function normalizeOrientation($orientation)
    {
        $portraitValues = ['portrait', 'userPortrait', 'sensorPortrait', 'reversePortrait', 'fullSensor'];
        $landscapeValues = ['landscape', 'userLandscape', 'sensorLandscape', 'reverseLandscape'];

        if (in_array($orientation, $portraitValues)) {
            return 'portrait';
        } elseif (in_array($orientation, $landscapeValues)) {
            return 'landscape';
        } else {
            static::addError("Valor de orientación desconocido: $orientation", static::SEVERITY_WARNING);
            return 'portrait'; // Default a portrait como fallback
        }
    }

    /**
     * Lista los archivos de layout relacionados con la orientación
     * 
     * @return array|null Array asociativo con rutas de archivos o null si hay un error
     * @throws \Exception Si no se puede acceder a los directorios de recursos
     */
    public function getOrientationLayoutResources()
    {
        if ($this->rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }

        $resourcesPath = $this->rootPath . '/app/src/main/res';
        if (!is_dir($resourcesPath)) {
            $resourcesPath = $this->rootPath . '/res';
            if (!is_dir($resourcesPath)) {
                throw new \Exception("No se encontró el directorio de recursos");
            }
        }

        $result = [
            'layout' => [],
            'layout-land' => [],
            'values' => [],
            'values-land' => []
        ];

        // Verificar y listar archivos de layout estándar
        $layoutPath = $resourcesPath . '/layout';
        if (is_dir($layoutPath)) {
            $result['layout'] = $this->scanDirectory($layoutPath);
        } else {
            static::addError("No se encontró el directorio layout", static::SEVERITY_INFO);
        }

        // Verificar y listar archivos de layout landscape
        $layoutLandPath = $resourcesPath . '/layout-land';
        if (is_dir($layoutLandPath)) {
            $result['layout-land'] = $this->scanDirectory($layoutLandPath);
        } else {
            static::addError("No se encontró el directorio layout-land", static::SEVERITY_INFO);
        }

        // Verificar y listar archivos de values estándar
        $valuesPath = $resourcesPath . '/values';
        if (is_dir($valuesPath)) {
            $result['values'] = $this->scanDirectory($valuesPath);
        } else {
            static::addError("No se encontró el directorio values", static::SEVERITY_WARNING);
        }

        // Verificar y listar archivos de values landscape
        $valuesLandPath = $resourcesPath . '/values-land';
        if (is_dir($valuesLandPath)) {
            $result['values-land'] = $this->scanDirectory($valuesLandPath);
        } else {
            static::addError("No se encontró el directorio values-land", static::SEVERITY_INFO);
        }

        return $result;
    }

}