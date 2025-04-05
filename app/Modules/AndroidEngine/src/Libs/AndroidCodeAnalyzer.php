<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\src\Libs;

use Boctulus\Simplerest\Core\Libs\Strings;

/*
    TODO:

    - Determinar la orientación de la pantalla (portrait o landscape)

        . Orientación Default para la Aplicación

            <application
                android:screenOrientation="landscape"
                android:label="@string/app_name">
                <!-- Otras configuraciones -->
            </application>

        . Orientación por Activity

            <activity android:name=".MainActivity"
                    android:screenOrientation="landscape">
                <!-- Otras configuraciones -->
            </activity>

        . Listar res/values relacionados:

            res/layout/: Para el diseño en orientación vertical.
            res/layout-land/: Para el diseño en orientación horizontal.
            res/values/: Para valores generales.
            res/values-land/: Para valores específicos cuando la orientación sea horizontal.
            etc 

    - Listar colores (nombres) con sus valores (de colors.xml)

    - Listar strings (de strings.xml)

    - Listar drawables )

    - Listar permisos de AndroidManifest

    - Listar buildFeatures

        android {
            ...
            buildFeatures {
                dataBinding true
            }
        }

*/

class AndroidCodeAnalyzer
{    
    public static $orientation; // 'portrait' o 'landscape'
    
    // Ruta raíz del proyecto Android
    private static $rootPath = null;
    
    // Cola de errores/advertencias
    private static $errors = [];
    
    /**
     * Establece la ruta raíz del proyecto Android
     * 
     * @param string $path Ruta al directorio raíz del proyecto Android
     * @return void
     */
    public static function setRootPath($path)
    {
        self::$rootPath = rtrim($path, '/\\');
    }
    
    /**
     * Limpia un ID de Android (quita @+id/ o @id/)
     */
    private static function cleanId($id)
    {
        return preg_replace('/^@(\+)?id\//', '', $id);
    }
    
    /**
     * Añade un error/advertencia a la cola
     * 
     * @param string $message Mensaje de error
     * @return void
     */
    private static function addError($message)
    {
        self::$errors[] = $message;
    }
    
    /**
     * Obtiene todos los errores/advertencias acumulados
     * 
     * @return array Lista de errores/advertencias
     */
    public static function getErrors()
    {
        return self::$errors;
    }
    
    /**
     * Determina la orientación predeterminada de la aplicación
     * 
     * @return string|null 'portrait', 'landscape' o null si no se puede determinar
     * @throws \Exception Si no se encuentra el archivo AndroidManifest.xml
     */
    public static function getDefaultOrientation()
    {
        if (self::$rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }
        
        $manifestPath = self::$rootPath . '/AndroidManifest.xml';
        
        if (!file_exists($manifestPath)) {
            $manifestPath = self::$rootPath . '/app/src/main/AndroidManifest.xml';
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
            self::$orientation = self::normalizeOrientation($matches[1]);
            return self::$orientation;
        }
        
        // Buscar orientación en la actividad principal
        if (preg_match('/<activity[^>]*android:name=".MainActivity"[^>]*android:screenOrientation="([^"]+)"/', $content, $matches)) {
            self::$orientation = self::normalizeOrientation($matches[1]);
            return self::$orientation;
        }
        
        // Si no se encuentra una orientación explícita
        self::addError("No se encontró una orientación explícita en el AndroidManifest.xml");
        self::$orientation = null;
        return null;
    }
    
    /**
     * Normaliza los valores de orientación a 'portrait' o 'landscape'
     * 
     * @param string $orientation Valor de orientación original
     * @return string 'portrait' o 'landscape'
     */
    private static function normalizeOrientation($orientation)
    {
        $portraitValues = ['portrait', 'userPortrait', 'sensorPortrait', 'reversePortrait', 'fullSensor'];
        $landscapeValues = ['landscape', 'userLandscape', 'sensorLandscape', 'reverseLandscape'];
        
        if (in_array($orientation, $portraitValues)) {
            return 'portrait';
        } elseif (in_array($orientation, $landscapeValues)) {
            return 'landscape';
        } else {
            self::addError("Valor de orientación desconocido: $orientation");
            return 'portrait'; // Default a portrait como fallback
        }
    }
    
    /**
     * Lista los archivos de layout relacionados con la orientación
     * 
     * @return array|null Array asociativo con rutas de archivos o null si hay un error
     * @throws \Exception Si no se puede acceder a los directorios de recursos
     */
    public static function getOrientationLayoutResources()
    {
        if (self::$rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }
        
        $resourcesPath = self::$rootPath . '/app/src/main/res';
        if (!is_dir($resourcesPath)) {
            $resourcesPath = self::$rootPath . '/res';
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
            $result['layout'] = self::scanDirectory($layoutPath);
        } else {
            self::addError("No se encontró el directorio layout");
        }
        
        // Verificar y listar archivos de layout landscape
        $layoutLandPath = $resourcesPath . '/layout-land';
        if (is_dir($layoutLandPath)) {
            $result['layout-land'] = self::scanDirectory($layoutLandPath);
        } else {
            self::addError("No se encontró el directorio layout-land");
        }
        
        // Verificar y listar archivos de values estándar
        $valuesPath = $resourcesPath . '/values';
        if (is_dir($valuesPath)) {
            $result['values'] = self::scanDirectory($valuesPath);
        } else {
            self::addError("No se encontró el directorio values");
        }
        
        // Verificar y listar archivos de values landscape
        $valuesLandPath = $resourcesPath . '/values-land';
        if (is_dir($valuesLandPath)) {
            $result['values-land'] = self::scanDirectory($valuesLandPath);
        } else {
            self::addError("No se encontró el directorio values-land");
        }
        
        return $result;
    }
    
    /**
     * Escanea un directorio y devuelve una lista de archivos
     * 
     * @param string $directory Ruta del directorio a escanear
     * @return array Lista de archivos
     */
    private static function scanDirectory($directory)
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
     * Lista los colores definidos en colors.xml
     * 
     * @return array|null Array asociativo con nombre => valor o null si hay un error
     * @throws \Exception Si no se encuentra el archivo colors.xml
     */
    public static function getColors()
    {
        if (self::$rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }
        
        $colorsPath = self::$rootPath . '/app/src/main/res/values/colors.xml';
        if (!file_exists($colorsPath)) {
            $colorsPath = self::$rootPath . '/res/values/colors.xml';
            if (!file_exists($colorsPath)) {
                throw new \Exception("No se encontró el archivo colors.xml");
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
            self::addError("No se encontraron definiciones de colores en colors.xml");
        }
        
        return $colors;
    }
    
    /**
     * Lista los strings definidos en strings.xml
     * 
     * @return array|null Array asociativo con nombre => valor o null si hay un error
     * @throws \Exception Si no se encuentra el archivo strings.xml
     */
    public static function getStrings()
    {
        if (self::$rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }
        
        $stringsPath = self::$rootPath . '/app/src/main/res/values/strings.xml';
        if (!file_exists($stringsPath)) {
            $stringsPath = self::$rootPath . '/res/values/strings.xml';
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
            self::addError("No se encontraron definiciones de strings en strings.xml");
        }
        
        return $strings;
    }
    
    /**
     * Lista los drawables del proyecto
     * 
     * @return array|null Array con rutas a los archivos drawable o null si hay un error
     * @throws \Exception Si no se encuentra el directorio drawable
     */
    public static function getDrawables()
    {
        if (self::$rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }
        
        $drawablePath = self::$rootPath . '/app/src/main/res/drawable';
        if (!is_dir($drawablePath)) {
            $drawablePath = self::$rootPath . '/res/drawable';
            if (!is_dir($drawablePath)) {
                throw new \Exception("No se encontró el directorio drawable");
            }
        }
        
        $drawables = [];
        
        // Buscar en drawable básico
        if (is_dir($drawablePath)) {
            $drawables['drawable'] = self::scanDirectory($drawablePath);
        }
        
        // Buscar en otros directorios drawable-*
        $resourcesPath = dirname($drawablePath);
        if ($handle = opendir($resourcesPath)) {
            while (false !== ($dir = readdir($handle))) {
                if (preg_match('/^drawable-/', $dir) && is_dir($resourcesPath . '/' . $dir)) {
                    $drawables[$dir] = self::scanDirectory($resourcesPath . '/' . $dir);
                }
            }
            closedir($handle);
        }
        
        if (empty($drawables)) {
            self::addError("No se encontraron drawables");
            return null;
        }
        
        return $drawables;
    }
    
    /**
     * Lista los permisos definidos en AndroidManifest.xml
     * 
     * @return array|null Array con los permisos o null si hay un error
     * @throws \Exception Si no se encuentra el archivo AndroidManifest.xml
     */
    public static function getPermissions()
    {
        if (self::$rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }
        
        $manifestPath = self::$rootPath . '/AndroidManifest.xml';
        
        if (!file_exists($manifestPath)) {
            $manifestPath = self::$rootPath . '/app/src/main/AndroidManifest.xml';
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
            self::addError("No se encontraron permisos definidos en AndroidManifest.xml");
        }
        
        return $permissions;
    }
    
    /**
     * Lista las buildFeatures definidas en el archivo build.gradle
     * 
     * @return array|null Array asociativo con feature => valor o null si hay un error
     * @throws \Exception Si no se encuentra el archivo build.gradle
     */
    public static function getBuildFeatures()
    {
        if (self::$rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }
        
        $gradlePath = self::$rootPath . '/app/build.gradle';
        if (!file_exists($gradlePath)) {
            $gradlePath = self::$rootPath . '/build.gradle';
            if (!file_exists($gradlePath)) {
                throw new \Exception("No se encontró el archivo build.gradle");
            }
        }
        
        $content = file_get_contents($gradlePath);
        if ($content === false) {
            throw new \Exception("No se pudo leer el archivo build.gradle");
        }
        
        $features = [];
        
        // Buscar sección buildFeatures
        if (preg_match('/buildFeatures\s*{([^}]+)}/s', $content, $matches)) {
            $featuresSection = $matches[1];
            
            // Extraer cada característica
            if (preg_match_all('/(\w+)\s+(true|false)/', $featuresSection, $featureMatches, PREG_SET_ORDER)) {
                foreach ($featureMatches as $match) {
                    $features[$match[1]] = $match[2] === 'true';
                }
            } else {
                self::addError("No se encontraron características en la sección buildFeatures");
            }
        } else {
            self::addError("No se encontró la sección buildFeatures en build.gradle");
            return null;
        }
        
        return $features;
    }
}

