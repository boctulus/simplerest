<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\src\Libs;

use Boctulus\Simplerest\Core\Libs\Files;

/*
    Análisis de código Android
*/

class AndroidCodeAnalyzer
{
    public $orientation; // 'portrait' o 'landscape'

    // Ruta raíz del proyecto Android
    private $rootPath = null;

    // Cola de errores/advertencias
    private $errors = [];

    const ERROR_SEVERITY   = 'error';
    const WARNING_SEVERITY = 'warning';
    const INFO_SEVERITY    = 'info';
    const DEBUG_SEVERITY   = 'debug';

    /**
     * Añade un error/advertencia a la cola
     * 
     * @param string $message Mensaje de error
     * @return void
     */
    private function addError($message, $severity = 'info')
    {
        if (is_string($message)) {
            $message = [
                'type' => $severity,
                'text' => $message
            ];
        }

        $this->errors[] = $message;
    }

    /**
     * Obtiene todos los errores/advertencias acumulados
     * 
     * @return array Lista de errores/advertencias
     */
    public function getErrors()
    {
        return $this->errors;
    }

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

    /**
     * Limpia un ID de Android (quita @+id/ o @id/)
     */
    private function cleanId($id)
    {
        return preg_replace('/^@(\+)?id\//', '', $id);
    }

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
        static::addError("No se encontró una orientación explícita en el AndroidManifest.xml", static::WARNING_SEVERITY);
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
            static::addError("Valor de orientación desconocido: $orientation", static::WARNING_SEVERITY);
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
            $result['layout'] = static::scanDirectory($layoutPath);
        } else {
            static::addError("No se encontró el directorio layout", static::INFO_SEVERITY);
        }

        // Verificar y listar archivos de layout landscape
        $layoutLandPath = $resourcesPath . '/layout-land';
        if (is_dir($layoutLandPath)) {
            $result['layout-land'] = static::scanDirectory($layoutLandPath);
        } else {
            static::addError("No se encontró el directorio layout-land", static::INFO_SEVERITY);
        }

        // Verificar y listar archivos de values estándar
        $valuesPath = $resourcesPath . '/values';
        if (is_dir($valuesPath)) {
            $result['values'] = static::scanDirectory($valuesPath);
        } else {
            static::addError("No se encontró el directorio values", static::WARNING_SEVERITY);
        }

        // Verificar y listar archivos de values landscape
        $valuesLandPath = $resourcesPath . '/values-land';
        if (is_dir($valuesLandPath)) {
            $result['values-land'] = static::scanDirectory($valuesLandPath);
        } else {
            static::addError("No se encontró el directorio values-land", static::INFO_SEVERITY);
        }

        return $result;
    }

    /**
     * Escanea un directorio y devuelve una lista de archivos
     * 
     * @param string $directory Ruta del directorio a escanear
     * @return array Lista de archivos
     */
    private function scanDirectory($directory)
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
    public function getColors()
    {
        if ($this->rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }

        $colorsPath = $this->rootPath . '/app/src/main/res/values/colors.xml';
        if (!file_exists($colorsPath)) {
            $colorsPath = $this->rootPath . '/res/values/colors.xml';
            if (!file_exists($colorsPath)) {
                throw new \Exception("No se encontró el archivo colors.xml", static::WARNING_SEVERITY);
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
            static::addError("No se encontraron definiciones de colores en colors.xml", static::INFO_SEVERITY);
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
            static::addError("No se encontraron definiciones de strings en strings.xml", static::INFO_SEVERITY);
        }

        return $strings;
    }

    /**
     * Lista los drawables del proyecto
     * 
     * @return array|null Array con rutas a los archivos drawable o null si hay un error
     * @throws \Exception Si no se encuentra el directorio drawable
     */
    public function getDrawables()
    {
        if ($this->rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }

        $drawablePath = $this->rootPath . '/app/src/main/res/drawable';
        if (!is_dir($drawablePath)) {
            $drawablePath = $this->rootPath . '/res/drawable';
            if (!is_dir($drawablePath)) {
                throw new \Exception("No se encontró el directorio drawable");
            }
        }

        $drawables = [];

        // Buscar en drawable básico
        if (is_dir($drawablePath)) {
            $drawables['drawable'] = static::scanDirectory($drawablePath);
        }

        // Buscar en otros directorios drawable-*
        $resourcesPath = dirname($drawablePath);
        if ($handle = opendir($resourcesPath)) {
            while (false !== ($dir = readdir($handle))) {
                if (preg_match('/^drawable-/', $dir) && is_dir($resourcesPath . '/' . $dir)) {
                    $drawables[$dir] = static::scanDirectory($resourcesPath . '/' . $dir);
                }
            }
            closedir($handle);
        }

        if (empty($drawables)) {
            static::addError("No se encontraron drawables");
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

    /**
     * Convierte XML a Markdown en un formato amigable y simple para layouts Android
     * 
     * @param string $xml Contenido XML
     * @param bool $include_ids Si es true, incluye los IDs entre corchetes
     * @return string Markdown formateado
     */
    function xmlToMarkdown(string $xml, bool $include_ids = false)
    {
        // Carga el XML como DOM
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // Suprimir errores XML
        $dom->loadXML($xml);
        libxml_clear_errors();

        $markdown = "# Layout XML\n\n";

        // Procesar el nodo raíz
        if ($dom->documentElement) {
            // Rastrea la ruta (breadcrumb) de componentes para reportes de error
            $pathStack = [];
            $markdown .= static::processDomNode($dom->documentElement, 0, $include_ids, $pathStack);
        }

        return $markdown;
    }

    /**
     * Determina si un elemento debería tener un ID basado en su tipo
     * 
     * @param string $elementName Nombre del elemento
     * @return array [requiereId: bool, severidad: string]
     */
    private function shouldHaveId($elementName)
    {
        // Elementos que DEBEN tener ID (severidad WARNING)
        $mustHaveIdElements = [
            'Button',
            'ImageButton',
            'EditText',
            'TextView',
            'CheckBox',
            'RadioButton',
            'Switch',
            'ToggleButton',
            'Spinner',
            'SeekBar',
            'RatingBar',
            'SearchView',
            'RecyclerView',
            'ListView',
            'GridView',
            'ViewPager',
            'Fragment'
        ];

        // Elementos que DEBERÍAN tener ID (severidad INFO)
        $shouldHaveIdElements = [
            'LinearLayout',
            'RelativeLayout',
            'ConstraintLayout',
            'FrameLayout',
            'CoordinatorLayout',
            'DrawerLayout',
            'CardView',
            'ScrollView',
            'HorizontalScrollView',
            'TabLayout',
            'Toolbar',
            'ImageView'
        ];

        if (in_array($elementName, $mustHaveIdElements) || preg_match('/(Button|Input)$/', $elementName)) {
            return [true, static::WARNING_SEVERITY];
        } elseif (in_array($elementName, $shouldHaveIdElements) || preg_match('/(Layout|View)$/', $elementName)) {
            return [true, static::INFO_SEVERITY];
        }

        return [false, static::INFO_SEVERITY];
    }

    /**
     * Procesa recursivamente un nodo DOM y lo convierte a Markdown
     * 
     * @param \DOMNode $node Nodo DOM a procesar
     * @param int $depth Nivel de profundidad actual para la indentación
     * @param bool $include_ids Si es true, incluye los IDs en la salida
     * @param array &$pathStack Pila para rastrear la ruta del elemento actual
     * @return string Markdown para este nodo y sus hijos
     */
    private function processDomNode($node, $depth = 0, $include_ids = false, &$pathStack = [])
    {
        $indent = str_repeat("  ", $depth);
        $markdown = "";

        // Solo procesar nodos de elemento
        if ($node->nodeType !== XML_ELEMENT_NODE) {
            return $markdown;
        }

        // Obtener el nombre del nodo (eliminar el namespace si existe)
        $nodeName = $node->localName ?: $node->nodeName;
        $nodeName = preg_replace('/^.*:/', '', $nodeName);

        // Añadir elemento a la pila de ruta
        $currentPosition = $nodeName . '[' . (count($pathStack) > 0 ? count($pathStack) : '0') . ']';
        $pathStack[] = $currentPosition;

        // Comenzar la sección del elemento
        $markdown .= "{$indent}- **{$nodeName}**";

        // Verificar si tiene ID
        $hasId = false;
        $id = null;

        // Buscar todos los atributos
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $attrName = $attr->nodeName;
                $attrValue = $attr->nodeValue;

                // Verificar si es un atributo ID (con o sin namespace)
                if ($attrName === 'android:id' || $attrName === 'id' || substr($attrName, -3) === ':id') {
                    $hasId = true;
                    $id = static::cleanId($attrValue);
                    if ($include_ids) {
                        $markdown .= " [id:{$id}]";
                    }
                    break;
                }
            }
        }

        // Verificar si este elemento debería tener ID
        list($shouldHaveId, $severity) = static::shouldHaveId($nodeName);
        if ($shouldHaveId && !$hasId) {
            // Crear un breadcrumb para mostrar la ruta al elemento sin ID
            $breadcrumb = implode(' > ', $pathStack);
            static::addError("Elemento '{$nodeName}' sin ID encontrado en: {$breadcrumb}", $severity);
        }

        $markdown .= "\n";

        // Procesar nodos hijos
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE) {
                    $markdown .= static::processDomNode($child, $depth + 1, $include_ids, $pathStack);
                }
            }
        }

        // Quitar el elemento actual de la pila de ruta antes de regresar
        array_pop($pathStack);

        return $markdown;
    }

    /**
     * Lee un archivo XML y lo convierte a Markdown
     * 
     * @param string $file_path Ruta al archivo XML
     * @param bool $include_ids Si es true, incluye los IDs en el resultado
     * @return string Markdown formateado o mensaje de error
     */
    function markdown(string $file_path, bool $include_ids = false)
    {
        if (!file_exists($file_path)) {
            return "Error: El archivo `$file_path` no existe";
        }

        $xml = file_get_contents($file_path);
        if ($xml === false) {
            return "Error: No se pudo leer el archivo `$file_path`";
        }

        return static::xmlToMarkdown($xml, $include_ids);
    }

    /**
     * Lista los identificadores (@+id/{name}) en todas las vistas XML del proyecto
     * 
     * @return array Array estructurado con rutas y sus respectivos IDs
     * @throws \Exception Si no se encuentra el directorio de layouts
     */
    public function findAllXmlIds()
    {
        if ($this->rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }

        // Posibles rutas para layouts
        $layoutPaths = [
            $this->rootPath . '/app/src/main/res/layout',
            $this->rootPath . '/app/src/main/res/layout-land',
            $this->rootPath . '/res/layout',
            $this->rootPath . '/res/layout-land'
        ];

        $result = [];
        $foundAnyPath = false;

        foreach ($layoutPaths as $layoutPath) {
            if (is_dir($layoutPath)) {
                $foundAnyPath = true;
                $files = glob($layoutPath . DIRECTORY_SEPARATOR . '*.xml');

                foreach ($files as $file) {
                    $content = file_get_contents($file);
                    if ($content === false) continue;

                    $ids = [];
                    // Buscar todos los IDs en el formato @+id/name o @id/name
                    if (preg_match_all('/@\+?id\/([a-zA-Z0-9_]+)/', $content, $matches)) {
                        $ids = array_unique($matches[0]); // Eliminar duplicados
                    }

                    if (!empty($ids)) {
                        $relativePath = str_replace($this->rootPath, '', $file);
                        $result[$relativePath] = $ids;
                    }
                }
            }
        }

        if (!$foundAnyPath) {
            throw new \Exception("No se encontró ningún directorio de layouts");
        }

        return $result;
    }

    /////////////////////

    /**
     * Lista todas las Activities del proyecto
     * 
     * @return array Lista de Activities encontradas
     * @throws \Exception Si no se encuentra la carpeta de código Java/Kotlin
     */
    public function listActivities()
    {
        if ($this->rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }

        // Usamos el método de Files para buscar archivos .java y .kt recursivamente
        $sourcePath = $this->rootPath . '/app/src/main/java';

        $this->addError("Buscando archivos fuente en: {$sourcePath}", self::DEBUG_SEVERITY);

        // Verificar si el directorio existe
        if (!is_dir($sourcePath)) {
            $this->addError("El directorio de código fuente no existe: {$sourcePath}", self::ERROR_SEVERITY);
            return [];
        }

        // Usar Files::recursiveGlob para buscar archivos .kt y .java
        $files = Files::recursiveGlob($sourcePath . DIRECTORY_SEPARATOR . '*.kt|*.java');

        if (empty($files)) {
            $this->addError("No se encontraron archivos .kt o .java en {$sourcePath}", self::WARNING_SEVERITY);
            return [];
        }

        $this->addError("Encontrados " . count($files) . " archivos .kt y .java", self::DEBUG_SEVERITY);

        $activities = [];

        // Analizar cada archivo para buscar Activities
        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content === false) {
                $this->addError("No se pudo leer el archivo: {$file}", self::WARNING_SEVERITY);
                continue;
            }

            // Patron para Activities en Kotlin
            if (pathinfo($file, PATHINFO_EXTENSION) === 'kt') {
                // class MainActivity : AppCompatActivity() { ... }
                // class UnlockActivity : AppCompatActivity { ... }
                // class SomeActivity() : AppCompatActivity { ... }
                if (preg_match('/class\s+(\w+Activity)\s*(\([^)]*\))?\s*:\s*\w*Activity/i', $content, $matches)) {
                    $activityName = $matches[1];

                    // Obtener el nombre del paquete
                    $packageName = '';
                    if (preg_match('/package\s+([\w\.]+)/', $content, $packageMatches)) {
                        $packageName = $packageMatches[1];
                    }

                    $activities[] = [
                        'name' => $activityName,
                        'package' => $packageName,
                        'path' => str_replace($this->rootPath, '', $file),
                        'fullName' => $packageName . '.' . $activityName
                    ];

                    $this->addError("Activity encontrada: {$activityName} en {$file}", self::DEBUG_SEVERITY);
                }
            }
            // Patrón para Activities en Java
            else if (pathinfo($file, PATHINFO_EXTENSION) === 'java') {
                // public class MainActivity extends AppCompatActivity { ... }
                if (preg_match('/class\s+(\w+Activity)\s+extends\s+\w*Activity/i', $content, $matches)) {
                    $activityName = $matches[1];

                    // Obtener el nombre del paquete
                    $packageName = '';
                    if (preg_match('/package\s+([\w\.]+);/', $content, $packageMatches)) {
                        $packageName = $packageMatches[1];
                    }

                    $activities[] = [
                        'name' => $activityName,
                        'package' => $packageName,
                        'path' => str_replace($this->rootPath, '', $file),
                        'fullName' => $packageName . '.' . $activityName
                    ];

                    $this->addError("Activity encontrada: {$activityName} en {$file}", self::DEBUG_SEVERITY);
                }
            }
        }

        if (empty($activities)) {
            $this->addError("No se encontraron Activities en ninguno de los archivos analizados", self::WARNING_SEVERITY);
        }

        return $activities;
    }


    /**
     * Lista todas las Activities del proyecto incluyendo referencias a ellas
     * 
     * @return array Lista de Activities con sus referencias
     * @throws \Exception Si no se encuentra la carpeta de código Java/Kotlin
     */
    public function listActivitiesWithReferences()
    {
        // Primero obtenemos todas las activities
        $activities = $this->listActivities();

        if (empty($activities)) {
            return [];
        }

        // Para cada actividad, buscamos referencias
        foreach ($activities as &$activity) {
            $references = [];

            // Verificamos si cada referencia no está vacía antes de agregarla
            $manifest = $this->findActivityInManifest($activity['fullName']);
            if (!empty($manifest)) {
                $references['manifest'] = $manifest;
            }

            $menu = $this->findActivityInMenuFiles($activity['name']);
            if (!empty($menu)) {
                $references['menu'] = $menu;
            }

            $intents = $this->findActivityInIntents($activity['fullName']);
            if (!empty($intents)) {
                $references['intents'] = $intents;
            }

            // Solo agregamos la clave 'references' si hay alguna referencia no vacía
            if (!empty($references)) {
                $activity['references'] = $references;
            }
        }

        return $activities;
    }

    /**
 * Busca la Activity en el AndroidManifest.xml
 * 
 * @param string $activityFullName Nombre completo de la Activity (con paquete)
 * @return array Referencias encontradas en el manifest
 */
private function findActivityInManifest($activityFullName)
{
    $references = [];

    $manifestPaths = [
        $this->rootPath . '/app/src/main/AndroidManifest.xml',
        $this->rootPath . '/AndroidManifest.xml'
    ];

    foreach ($manifestPaths as $manifestPath) {
        if (file_exists($manifestPath)) {
            $content = file_get_contents($manifestPath);
            if ($content === false) continue;

            // Buscar la activity en el manifest (con o sin paquete completo)
            $activityName = substr($activityFullName, strrpos($activityFullName, '.') + 1);
            
            // Primero extraer todas las definiciones de activity para analizar cada una individualmente
            preg_match_all('/<activity[^>]*>(.*?)<\/activity>/s', $content, $activityBlocks);
            
            foreach ($activityBlocks[0] as $index => $activityBlock) {
                // Verificar si esta actividad corresponde a la que estamos buscando
                if (preg_match('/android:name="\.?' . preg_quote($activityName) . '"|android:name="' . preg_quote($activityFullName) . '"/', $activityBlock)) {
                    // Agregar referencia de declaración
                    preg_match('/<activity[^>]*android:name="[^"]*"[^>]*>/', $activityBlock, $activityTag);
                    if (!empty($activityTag[0])) {
                        $references[] = [
                            'source' => 'AndroidManifest.xml',
                            'type' => 'declaration',
                            'context' => trim($activityTag[0])
                        ];
                    }
                    
                    // Verificar si es la actividad principal (MAIN y LAUNCHER)
                    if (preg_match('/<action android:name="android.intent.action.MAIN"/', $activityBlock) &&
                        preg_match('/<category android:name="android.intent.category.LAUNCHER"/', $activityBlock)) {
                        $references[] = [
                            'source' => 'AndroidManifest.xml',
                            'type' => 'main_activity',
                            'context' => 'Esta es la actividad principal (MAIN/LAUNCHER)'
                        ];
                    }
                }
            }
            
            break; // Solo necesitamos un manifest
        }
    }

    return $references;
}

    /**
     * Busca la Activity en archivos de menú (XML)
     * 
     * @param string $activityName Nombre de la Activity
     * @return array Referencias encontradas en archivos de menú
     */
    private function findActivityInMenuFiles($activityName)
    {
        $references = [];

        $menuPaths = [
            $this->rootPath . '/app/src/main/res/menu',
            $this->rootPath . '/res/menu'
        ];

        foreach ($menuPaths as $menuPath) {
            if (is_dir($menuPath)) {
                $files = glob($menuPath . DIRECTORY_SEPARATOR . '*.xml');

                foreach ($files as $file) {
                    $content = file_get_contents($file);
                    if ($content === false) continue;

                    if (stripos($content, $activityName) !== false) {
                        $references[] = [
                            'source' => str_replace($this->rootPath, '', $file),
                            'type' => 'menu_reference',
                            'context' => 'Posible referencia en archivo de menú'
                        ];
                    }
                }
            }
        }

        return $references;
    }

    /**
     * Busca referencias a la Activity mediante Intents en código fuente
     * 
     * @param string $activityFullName Nombre completo de la Activity (con paquete)
     * @return array Referencias encontradas en código fuente
     */
    private function findActivityInIntents($activityFullName)
    {
        $references = [];
        $activityName = substr($activityFullName, strrpos($activityFullName, '.') + 1);

        // Rutas donde buscar código fuente
        $sourcePaths = [
            $this->rootPath . '/app/src/main/java',
            $this->rootPath . '/src/main/java',
            $this->rootPath . '/java',
            $this->rootPath . '/app/src/main/kotlin',
            $this->rootPath . '/src/main/kotlin',
            $this->rootPath . '/kotlin'
        ];

        foreach ($sourcePaths as $sourcePath) {
            if (!is_dir($sourcePath)) continue;

            // Buscar recursivamente en archivos .java y .kt
            $javaFiles = Files::recursiveGlob($sourcePath . DIRECTORY_SEPARATOR . '*.java');
            $ktFiles   = Files::recursiveGlob($sourcePath . DIRECTORY_SEPARATOR . '*.kt');
            $files     = array_merge($javaFiles, $ktFiles);

            foreach ($files as $file) {
                $content = file_get_contents($file);
                if ($content === false) continue;

                // Patrones comunes para buscar intents hacia actividades
                $patterns = [
                    // Intent(this, ActivityName.class)
                    '/new\s+Intent\s*\(\s*.*?,\s*' . preg_quote($activityName) . '\.class\s*\)/',
                    // Intent(context, ActivityName::class.java)
                    '/new\s+Intent\s*\(\s*.*?,\s*' . preg_quote($activityName) . '::class\.java\s*\)/',
                    // Intent().setClass(this, ActivityName.class)
                    '/Intent\s*\(\s*\).*?setClass\s*\(\s*.*?,\s*' . preg_quote($activityName) . '\.class\s*\)/',
                    // Intent().setClass(this, ActivityName::class.java)
                    '/Intent\s*\(\s*\).*?setClass\s*\(\s*.*?,\s*' . preg_quote($activityName) . '::class\.java\s*\)/',
                    // startActivity<ActivityName>()
                    '/startActivity\s*<\s*' . preg_quote($activityName) . '\s*>\s*\(/'
                ];

                foreach ($patterns as $pattern) {
                    if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                        foreach ($matches[0] as $match) {
                            // Extraer el contexto (la línea donde se encuentra)
                            $matchPos = $match[1];
                            $lineStart = max(0, strrpos(substr($content, 0, $matchPos), "\n") + 1);
                            $lineEnd = strpos($content, "\n", $matchPos);
                            if ($lineEnd === false) $lineEnd = strlen($content);

                            $lineContent = trim(substr($content, $lineStart, $lineEnd - $lineStart));

                            $references[] = [
                                'source' => str_replace($this->rootPath, '', $file),
                                'type' => 'intent',
                                'context' => $lineContent
                            ];
                        }
                    }
                }
            }
        }

        return $references;
    }
}
