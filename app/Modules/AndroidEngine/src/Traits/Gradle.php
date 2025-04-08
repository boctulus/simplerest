<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\src\Traits;

use Boctulus\Simplerest\Core\Traits\ErrorReporting;

Trait Gradle
{
    use ErrorReporting;
    
   /**
     * Lista las buildFeatures definidas en el archivo build.gradle o build.gradle.kts
     * 
     * @return array|null Array asociativo con feature => valor o null si hay un error
     * @throws \Exception Si no se encuentra el archivo build.gradle o build.gradle.kts
     */
    public function getBuildFeatures()
    {
        if ($this->rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }

        // Buscar diferentes variantes de archivos de gradle
        $possiblePaths = [
            '/app/build.gradle',
            '/build.gradle',
            '/app/build.gradle.kts',
            '/build.gradle.kts'
        ];

        $gradlePath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($this->rootPath . $path)) {
                $gradlePath = $this->rootPath . $path;
                break;
            }
        }

        if ($gradlePath === null) {
            throw new \Exception("No se encontró ningún archivo build.gradle o build.gradle.kts");
        }

        $content = file_get_contents($gradlePath);
        if ($content === false) {
            throw new \Exception("No se pudo leer el archivo $gradlePath");
        }

        $features = [];

        // Detectar si es un archivo .kts
        $isKts = pathinfo($gradlePath, PATHINFO_EXTENSION) === 'kts';

        // Patrón de búsqueda adaptado para ambos formatos
        if ($isKts) {
            // Formato para build.gradle.kts
            $pattern = '/buildFeatures\s*\{([^}]+)\}/s';
        } else {
            // Formato para build.gradle
            $pattern = '/buildFeatures\s*{([^}]+)}/s';
        }

        // Buscar sección buildFeatures
        if (preg_match($pattern, $content, $matches)) {
            $featuresSection = $matches[1];

            // Extraer cada característica - adaptado para ambos formatos
            if ($isKts) {
                if (preg_match_all('/(\w+)\s*=\s*(true|false)/', $featuresSection, $featureMatches, PREG_SET_ORDER)) {
                    foreach ($featureMatches as $match) {
                        $features[$match[1]] = $match[2] === 'true';
                    }
                } else {
                    static::addError("No se encontraron características en la sección buildFeatures del archivo .kts");
                }
            } else {
                if (preg_match_all('/(\w+)\s+(true|false)/', $featuresSection, $featureMatches, PREG_SET_ORDER)) {
                    foreach ($featureMatches as $match) {
                        $features[$match[1]] = $match[2] === 'true';
                    }
                } else {
                    static::addError("No se encontraron características en la sección buildFeatures");
                }
            }
        } else {
            static::addError("No se encontró la sección buildFeatures en $gradlePath");
            return null;
        }

        return $features;
    }

}