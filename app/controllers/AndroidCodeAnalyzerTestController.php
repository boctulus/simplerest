<?php

namespace Boctulus\Simplerest\controllers;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;
use Boctulus\Simplerest\Modules\AndroidEngine\src\Libs\AndroidCodeAnalyzer;

class AndroidCodeAnalyzerTestController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        dd("Index of " . __CLASS__);
    }

    /**
     * Prueba para el método getDefaultOrientation
     */
    function test_getDefaultOrientation()
    {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS';

        $an = new AndroidCodeAnalyzer();

        $an->setRootPath($project_path);
        // Output
        dd(
            $an->getDefaultOrientation() ?? 'None',
            'Default orientation'
        );

        // Output errores
        dd(
            $an->getErrors()
        );
    }

    /**
     * Prueba para el método getOrientationLayoutResources
     */
    function test_getOrientationLayoutResources()
    {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS';

        $an = new AndroidCodeAnalyzer();

        $an->setRootPath($project_path);
        // Output
        dd(
            $an->getOrientationLayoutResources()
        );
    }

    /**
     * Prueba para el método getColors
     */
    function test_getColors()
    {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS';

        $an = new AndroidCodeAnalyzer();

        $an->setRootPath($project_path);
        // Output
        dd(
            $an->getColors()
        );

        // Output Errors
        dd(
            $an->getErrors(), 'Errors'
        );
    }

    /**
     * Prueba para el método getStrings
     */
    function test_getStrings()
    {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS';

        $an = new AndroidCodeAnalyzer();

        $an->setRootPath($project_path);
        // Output
        dd(
            $an->getStrings()
        );

        // Output Errors
        dd(
            $an->getErrors(), 'Errors'
        );
    }

    /**
     * Prueba para el método getDrawables
     */
    function test_getDrawables()
    {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS';

        $an = new AndroidCodeAnalyzer();

        $an->setRootPath($project_path);
        // Output
        dd(
            $an->getDrawables()
        );
    }

    /**
     * Prueba para el método getPermissions
     */
    function test_getPermissions()
    {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS';

        $an = new AndroidCodeAnalyzer();

        $an->setRootPath($project_path);
        // Output
        dd(
            $an->getPermissions()
        );
    }

    /**
     * Prueba para el método getBuildFeatures
     */
    function test_getBuildFeatures()
    {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS';

        $an = new AndroidCodeAnalyzer();

        $an->setRootPath($project_path);
        // Output
        dd(
            $an->getBuildFeatures()
        );
    }

    /**
     * Prueba para el método getErrors
     */
    function test_getErrors()
    {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS';

        $an = new AndroidCodeAnalyzer();

        $an->setRootPath($project_path);

        // Ejecutar algunos métodos para generar posibles errores
        $an->getDefaultOrientation();
        $an->getOrientationLayoutResources();

        // Output
        dd(
            $an->getErrors(), 'Errors'
        );
    }

    function test_md()
    {
        $path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS\app\src\main\res\layout\screen_sales_calc.xml';

        $an = new AndroidCodeAnalyzer();

        dd($an->markdown(
            $path,
            false
        ), 'Markdown');

        $an = new AndroidCodeAnalyzer();

        dd($an->markdown(
            $path,
            true
        ), 'Markdown only IDs');

        // Output errores
        dd(
            $an->getErrors()
        );
    }


    /**
     * Prueba para el método findAllXmlIds
     */
    function test_findAllXmlIds()
    {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS';

        $analyzer = new AndroidCodeAnalyzer();
        $analyzer->setRootPath($project_path);
        // Output
        dd(
            $analyzer->findAllXmlIds()
        );
    }

    /**
     * Prueba para el método listActivities
     */
    function test_listActivities()
    {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS';

        $analyzer = new AndroidCodeAnalyzer();
        $analyzer->setRootPath($project_path);
        // Output
        dd(
            $analyzer->listActivities()
        );
    }

    /**
     * Prueba para el método listActivitiesWithReferences
     */
    function test_listActivitiesWithReferences()
    {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS';

        $analyzer = new AndroidCodeAnalyzer();
        $analyzer->setRootPath($project_path);

        $activities = $analyzer->listActivitiesWithReferences();

        // Resultado        
        dd($activities, "Activities encontradas");

        // Errores y mensajes de depuración
        dd($analyzer->getErrors(AndroidCodeAnalyzer::SEVERITY_WARNING), "Errores y mensajes de depuración");
    }


    function test_listener_analisis()
    {
        // En alguna parte donde se use el analizador:
        $analyzer = new AndroidCodeAnalyzer();
        $analyzer->setRootPath('C:\Users\jayso\AndroidStudioProjects\FriendlyPOS');
        $analyzer->setExcludePaths(['C:\Users\jayso\AndroidStudioProjects\FriendlyPOS\app\build\*']);

        // Obtener todos los event listeners que usan findViewById
        $viewListeners = $analyzer->getViewListeners();

        // Obtener todos los event listeners que usan ViewBinding
        $viewBindingListeners = $analyzer->getViewBindingListeners();

        dd($viewListeners, "ViewListeners encontrados");
        dd($viewBindingListeners, "ViewBindingListeners encontrados");
        dd($analyzer->getErrors(), "Errores encontrados");
    }

    function test_listener_analisis_1()
    {
        // En alguna parte donde se use el analizador:
        $analyzer = new AndroidCodeAnalyzer();
        $analyzer->setRootPath('C:\Users\jayso\StudioProjects\DarkCalc');
        $analyzer->setExcludePaths(['C:\Users\jayso\StudioProjects\DarkCalc\app\build\*']);

        // Obtener todos los event listeners que usan findViewById
        $viewListeners = $analyzer->getViewListeners();

        // Obtener todos los event listeners que usan ViewBinding
        $viewBindingListeners = $analyzer->getViewBindingListeners();

        dd($viewListeners, "ViewListeners encontrados");
        dd($viewBindingListeners, "ViewBindingListeners encontrados");
        dd($analyzer->getErrors(), "Errores encontrados");
    }

    /*
        FRAGMENTS
    */

    /**
     * Prueba para el método listFragments
     */
    function test_listFragments()
    {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS';

        $analyzer = new AndroidCodeAnalyzer();
        $analyzer->setRootPath($project_path);
        // Output
        dd(
            $analyzer->listFragments()
        );
    }

    /**
     * Prueba para el método listFragmentsWithReferences
     */
    function test_listFragmentsWithReferences()
    {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS';

        $analyzer = new AndroidCodeAnalyzer();
        $analyzer->setRootPath($project_path);
        // Output
        dd(
            $analyzer->listFragmentsWithReferences()
        );
    }

    /**
     * Prueba para el método listActivitiesWithReferences con fragmentos incluidos
     */
    function test_listActivitiesWithFragments()
    {
        $project_path = 'C:\Users\jayso\AndroidStudioProjects\FriendlyPOS';

        $analyzer = new AndroidCodeAnalyzer();
        $analyzer->setRootPath($project_path);
        // Output
        dd(
            $analyzer->listActivitiesWithFragments()
        );
    }

    function test_modularizer(){
        $analyzer = new AndroidCodeAnalyzer();
        $analyzer->setRootPath('C:\Users\jayso\AndroidStudioProjects\FriendlyPOS');

        $result = $analyzer->generateReusableComponent(
            'app/src/main/res/layout/screen_cashfund.xml',
            '@+id/keypad',
            'numeric_keypad'
        );

        dd($result);
    }    
}
