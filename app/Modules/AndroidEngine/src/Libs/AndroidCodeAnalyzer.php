<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\Src\Libs;

use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Traits\ErrorReporting;
use Boctulus\Simplerest\Modules\AndroidEngine\Src\Traits\Activities;
use Boctulus\Simplerest\Modules\AndroidEngine\Src\Traits\AndroidManifest;
use Boctulus\Simplerest\Modules\AndroidEngine\Src\Traits\DeviceOrientation;
use Boctulus\Simplerest\Modules\AndroidEngine\Src\Traits\Fragments;
use Boctulus\Simplerest\Modules\AndroidEngine\Src\Traits\Gradle;
use Boctulus\Simplerest\Modules\AndroidEngine\Src\Traits\Listeners;
use Boctulus\Simplerest\Modules\AndroidEngine\Src\Traits\Resources\Drawables;
use Boctulus\Simplerest\Modules\AndroidEngine\Src\Traits\Resources\Values;
use Boctulus\Simplerest\Modules\AndroidEngine\Src\Traits\XMLLayouts;
use Boctulus\Simplerest\Modules\AndroidEngine\Src\Traits\LogCat;

/*
    Análisis de código Android

    TODO: convertir en package
*/

class AndroidCodeAnalyzer
{
    public  $orientation; // 'portrait' o 'landscape'
    private $rootPath;
    private $excludePaths = [];
    private $errors = [];

    use AndroidManifest; // Trait para manejar el AndroidManifest.xml   
    use Gradle; // Trait para manejar archivos de gradle       
    use DeviceOrientation; // Trait para detectar la orientación de la aplicación
    use Values; // Trait para manejar valores de recursos (strings, colors, etc.)
    use Drawables; // Trait para manejar drawables
    use Activities; // Trait para manejar Activities
    use Listeners; // Trait para detectar listeners
    use Fragments; // Trait para detectar fragmentos
    use XMLLayouts; // Trait para manejo de Layouts
    use LogCat;

    use ErrorReporting; // Trait para manejar errores y advertencias 

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
