<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\src\Libs;

use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Traits\ErrorReporting;

/*
    Análisis de código Android

    Esta clase funciona MUY MAL
*/
class CodeStackDetector
{   
    private $rootPath;
    private $errors = [];

    use ErrorReporting; // Trait para manejar errores y advertencias 

    /**
     * Establece la ruta raíz del proyecto Android, ajustándola si es necesario
     * 
     * @param string $path Ruta inicial al directorio del proyecto Android
     * @return void
     */
    public function setRootPath($path)
    {
        $this->rootPath = $this->findRootPath(rtrim($path, '/\\'));
    }

    /**
     * Busca el directorio raíz del proyecto basado en la presencia de gradle.properties
     * 
     * @param string $path Ruta inicial
     * @return string Ruta ajustada al directorio raíz
     */
    protected function findRootPath($path)
    {
        $currentPath = $path;
        $maxLevels = 5; // Límite de niveles a subir
        for ($i = 0; $i <= $maxLevels; $i++) {
            if (Files::fileExists($currentPath . '/gradle.properties')) {
                return $currentPath;
            }
            $parentPath = dirname($currentPath);
            if ($parentPath === $currentPath) {
                break; // No hay más directorios padres
            }
            $currentPath = $parentPath;
        }
        $this->addError("No se encontró gradle.properties en los directorios padres", self::SEVERITY_WARNING);
        return $path; // Retorna la ruta original si no se encuentra
    }

    /**
     * Detecta si el proyecto es de React Native
     * 
     * @return bool
     */
    protected function isReactNative()
    {
        $packageJsonPath = $this->rootPath . '/package.json';
        if (!Files::fileExists($packageJsonPath)) {
            return false;
        }

        $content = Files::readFile($packageJsonPath);
        if ($content === false) {
            $this->addError("No se pudo leer package.json", self::SEVERITY_WARNING);
            return false;
        }

        $json = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->addError("package.json no es un JSON válido", self::SEVERITY_WARNING);
            return false;
        }

        return isset($json['dependencies']['react-native']) || isset($json['devDependencies']['react-native']);
    }

    /**
     * Detecta si el proyecto es de Flutter
     * 
     * @return bool
     */
    protected function isFlutter()
    {
        $pubspecPath = $this->rootPath . '/pubspec.yaml';
        if (!Files::fileExists($pubspecPath)) {
            return false;
        }

        $libDir = $this->rootPath . '/lib';
        if (!Files::dirExists($libDir)) {
            return false;
        }

        $dartFiles = Files::getFiles($libDir, 'dart');
        return !empty($dartFiles);
    }

    /**
     * Detecta si el proyecto es de Android nativo con criterios más estrictos
     * 
     * @return bool
     */
    protected function isAndroidNative()
    {
        $manifestPath = $this->rootPath . '/app/src/main/AndroidManifest.xml';
        $mainDir = $this->rootPath . '/app/src/main';
        $javaFiles = Files::getFiles($mainDir . '/java', 'java');
        $kotlinFiles = Files::getFiles($mainDir . '/kotlin', 'kt');

        return Files::fileExists($manifestPath) && 
               Files::dirExists($mainDir) && 
               (!empty($javaFiles) || !empty($kotlinFiles));
    }

    /**
     * Detecta tecnologías asociadas en el proyecto
     * 
     * @return array Lista de tecnologías detectadas
     */
    protected function detectTechnologies()
    {
        $technologies = [];

        // TypeScript
        if (Files::fileExists($this->rootPath . '/tsconfig.json') || 
            !empty(Files::getFiles($this->rootPath, 'ts')) || 
            !empty(Files::getFiles($this->rootPath, 'tsx'))) {
            $technologies[] = 'TypeScript';
        }

        // Dart
        if (!empty(Files::getFiles($this->rootPath, 'dart'))) {
            $technologies[] = 'Dart';
        }

        // Babel
        if (Files::fileExists($this->rootPath . '/babel.config.js') || 
            Files::fileExists($this->rootPath . '/.babelrc')) {
            $technologies[] = 'Babel';
        }

        // Firebase
        if (Files::fileExists($this->rootPath . '/android/app/google-services.json')) {
            $technologies[] = 'Firebase';
        }

        // GraphQL
        if (!empty(Files::getFiles($this->rootPath, 'graphql')) || 
            !empty(Files::getFiles($this->rootPath, 'gql'))) {
            $technologies[] = 'GraphQL';
        }

        return $technologies;
    }

    /**
     * Detecta el framework principal y las tecnologías asociadas
     * 
     * @return array Array estructurado con el framework y las tecnologías detectadas
     */
    public function detect()
    {
        $result = [
            'framework' => null,
            'technologies' => []
        ];

        if ($this->isReactNative()) {
            $result['framework'] = 'React Native';
        } elseif ($this->isFlutter()) {
            $result['framework'] = 'Flutter';
        } elseif ($this->isAndroidNative()) {
            $result['framework'] = 'Android Native';
        } else {
            $this->addError("No se pudo determinar el framework principal", self::SEVERITY_WARNING);
        }

        $result['technologies'] = $this->detectTechnologies();

        return $result;
    }
}