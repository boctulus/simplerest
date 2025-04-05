<?php

namespace Boctulus\Simplerest\controllers;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;
use Boctulus\Simplerest\Modules\AndroidEngine\src\Libs\AndroidCodeAnalyzer;

class AndroidCodeAnalyzerTestController extends Controller
{
    function __construct() { parent::__construct(); }

    function index()
    {
        dd("Index of ". __CLASS__);                   
    }

    /**
     * Prueba para el método getDefaultOrientation
     */
    function test_getDefaultOrientation() {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS'; 
        
        AndroidCodeAnalyzer::setRootPath($project_path);         
        // Output
        dd(
            AndroidCodeAnalyzer::getDefaultOrientation()
        );
    }

    /**
     * Prueba para el método getOrientationLayoutResources
     */
    function test_getOrientationLayoutResources() {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS'; 
        
        AndroidCodeAnalyzer::setRootPath($project_path);         
        // Output
        dd(
            AndroidCodeAnalyzer::getOrientationLayoutResources()
        );
    }

    /**
     * Prueba para el método getColors
     */
    function test_getColors() {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS'; 
        
        AndroidCodeAnalyzer::setRootPath($project_path);         
        // Output
        dd(
            AndroidCodeAnalyzer::getColors()
        );
    }

    /**
     * Prueba para el método getStrings
     */
    function test_getStrings() {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS'; 
        
        AndroidCodeAnalyzer::setRootPath($project_path);         
        // Output
        dd(
            AndroidCodeAnalyzer::getStrings()
        );
    }

    /**
     * Prueba para el método getDrawables
     */
    function test_getDrawables() {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS'; 
        
        AndroidCodeAnalyzer::setRootPath($project_path);         
        // Output
        dd(
            AndroidCodeAnalyzer::getDrawables()
        );
    }

    /**
     * Prueba para el método getPermissions
     */
    function test_getPermissions() {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS'; 
        
        AndroidCodeAnalyzer::setRootPath($project_path);         
        // Output
        dd(
            AndroidCodeAnalyzer::getPermissions()
        );
    }

    /**
     * Prueba para el método getBuildFeatures
     */
    function test_getBuildFeatures() {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS'; 
        
        AndroidCodeAnalyzer::setRootPath($project_path);         
        // Output
        dd(
            AndroidCodeAnalyzer::getBuildFeatures()
        );
    }

    /**
     * Prueba para el método getErrors
     */
    function test_getErrors() {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS'; 
        
        AndroidCodeAnalyzer::setRootPath($project_path);
        
        // Ejecutar algunos métodos para generar posibles errores
        AndroidCodeAnalyzer::getDefaultOrientation();
        AndroidCodeAnalyzer::getOrientationLayoutResources();
        
        // Output
        dd(
            AndroidCodeAnalyzer::getErrors()
        );
    }
}

