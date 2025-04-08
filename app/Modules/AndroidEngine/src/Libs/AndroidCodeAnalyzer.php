<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\src\Libs;

use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Modules\AndroidEngine\src\Traits\Activities;
use Boctulus\Simplerest\Modules\AndroidEngine\src\Traits\Gradle;
use Boctulus\Simplerest\Core\Traits\ErrorReporting;
use Boctulus\Simplerest\Modules\AndroidEngine\src\Traits\Fragments;
use Boctulus\Simplerest\Modules\AndroidEngine\src\Traits\AndroidManifest;
use Boctulus\Simplerest\Modules\AndroidEngine\src\Traits\Resources\Values;
use Boctulus\Simplerest\Modules\AndroidEngine\src\Traits\DeviceOrientation;
use Boctulus\Simplerest\Modules\AndroidEngine\src\Traits\Listeners;
use Boctulus\Simplerest\Modules\AndroidEngine\src\Traits\Resources\Drawables;

/*
    Análisis de código Android

    TO-DO

    - Mejorar el reconocimiento de event listeners. Falla casi por completo con:

    C:\Users\jayso\StudioProjects\DarkCalc\app\src\main\java\com\boctulus\pc\recalc

    PROMPT:
    http://simplerest.lan/prompt_generator#chat-722
*/

class AndroidCodeAnalyzer
{
    public  $orientation; // 'portrait' o 'landscape'
    private $rootPath;
    private $excludePaths = [];
    private $errors = [];

    use AndroidManifest; // Trait para manejar el AndroidManifest.xml   
    use Gradle; // Trait para manejar archivos de gradle
    use ErrorReporting; // Trait para manejar errores y advertencias    
    use DeviceOrientation; // Trait para detectar la orientación de la aplicación
    use Values; // Trait para manejar valores de recursos (strings, colors, etc.)
    use Drawables; // Trait para manejar drawables
    use Activities; // Trait para manejar Activities
    use Listeners; // Trait para detectar listeners
    use Fragments; // Trait para detectar fragmentos

    /**
     * Establece la ruta raíz del proyecto Android
     * 
     * @param string $path Ruta al directorio raíz del proyecto Android
     * @return void
     */
    public function setRootPath($path)
    {
        $this->rootPath = rtrim($path, '/\\');
    }

    public function setExcludePaths(array $paths)
    {
        $this->excludePaths = array_map('trim', $paths);
    }

    /**
     * Limpia un ID de Android (quita @+id/ o @id/)
     */
    private function cleanId($id)
    {
        return preg_replace('/^@(\+)?id\//', '', $id);
    }
}
