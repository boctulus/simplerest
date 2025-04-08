<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\src\Traits\Resources;

use Boctulus\Simplerest\Core\Traits\ErrorReporting;
use Boctulus\Simplerest\Modules\AndroidEngine\src\Traits\XML;

Trait Values 
{
    use ErrorReporting;
    use XML;

    /**
     * Lista los colores definidos en colors.xml
     * 
     * @return array|null Array asociativo con nombre => valor o null si hay un error
     * @throws \Exception Si no se encuentra el archivo colors.xml
     */
    public function getColors()
    {
        if ($this->rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }

        $colorsPath = $this->rootPath . '/app/src/main/res/values/colors.xml';
        if (!file_exists($colorsPath)) {
            $colorsPath = $this->rootPath . '/res/values/colors.xml';
            if (!file_exists($colorsPath)) {
                throw new \Exception("No se encontró el archivo colors.xml", $this->getWarningLevel());
            }
        }

        $content = file_get_contents($colorsPath);
        if ($content === false) {
            throw new \Exception("No se pudo leer el archivo colors.xml");
        }

        $colors = [];
        if (preg_match_all('/<color\s+name="([^"]+)">([^<]+)<\/color>/', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $colors[$match[1]] = $match[2];
            }
        } else {
            static::addError("No se encontraron definiciones de colores en colors.xml", static::SEVERITY_INFO);
        }

        return $colors;
    }

    /**
     * Lista los strings definidos en strings.xml
     * 
     * @return array|null Array asociativo con nombre => valor o null si hay un error
     * @throws \Exception Si no se encuentra el archivo strings.xml
     */
    public function getStrings()
    {
        if ($this->rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }

        $stringsPath = $this->rootPath . '/app/src/main/res/values/strings.xml';
        if (!file_exists($stringsPath)) {
            $stringsPath = $this->rootPath . '/res/values/strings.xml';
            if (!file_exists($stringsPath)) {
                throw new \Exception("No se encontró el archivo strings.xml");
            }
        }

        $content = file_get_contents($stringsPath);
        if ($content === false) {
            throw new \Exception("No se pudo leer el archivo strings.xml");
        }

        $strings = [];
        if (preg_match_all('/<string\s+name="([^"]+)"(?:[^>]*)>([^<]+)<\/string>/', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $strings[$match[1]] = $match[2];
            }
        } else {
            static::addError("No se encontraron definiciones de strings en strings.xml", static::SEVERITY_INFO);
        }

        return $strings;
    }
    
}