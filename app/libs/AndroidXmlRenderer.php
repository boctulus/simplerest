<?php

namespace Boctulus\Simplerest\Libs;

use Boctulus\Simplerest\Core\Libs\Strings;

/*
    TO-DO

    - Sacar CSS en archivo aparte
    - Verificar realmente renderiza bien los drawables
    - Soporte resto de layouts
    - Soporte resto de elementos (ImageButton, etc.)
    - Cargar colors.xml desde el proyecto Android usando loadColorResource()
    - Cargar strings.xml desde el proyecto Android
    - Soporte de dimensiones (dp, sp, etc.)
    - Soporte para <include> de layouts
    - Soporte para <merge> de layouts

    https://claude.ai/chat/dfe4885d-cb8a-46a6-b9d2-894044faac10
    https://claude.ai/chat/ac020888-33c1-4333-ab2a-e71b2600c8ed
*/
class AndroidXmlRenderer
{
    // Propiedades estáticas para configurar el viewport
    public static $viewportWidth = 360;
    public static $viewportHeight = 640;
    public static $orientation = 'portrait'; // 'portrait' o 'landscape'
    
    // Ruta raíz del proyecto Android
    private static $rootPath = null;
    
    // Cache para recursos cargados
    private static $colorCache = [];
    private static $drawableCache = [];
    private static $drawables = [];
    
    // Colores predefinidos (fallback)
    private static $predefinedColors = [
        'white' => '#FFFFFF',
        'black' => '#333333',
        'red' => '#FF5555',
        'orange' => '#FFA500',
        'blue' => '#2196F3',
        'green' => '#4CAF50',
        'purple' => '#9C27B0',
        'transparent' => 'transparent',
        'gray' => '#CCCCCC'
    ];

    /**
     * Establece la ruta raíz del proyecto Android
     * 
     * @param string $path Ruta al directorio raíz del proyecto Android
     * @return void
     */
    public static function setRootPath($path)
    {
        self::$rootPath = rtrim($path, '/\\');
        
        // Limpiar caché al cambiar la ruta
        self::$colorCache = [];
        self::$drawableCache = [];
    }
    
    /**
     * Renderiza una vista Android por su nombre
     * 
     * @param string $viewName Nombre de la vista (sin extensión)
     * @return string HTML renderizado
     */
    public static function render($viewName)
    {
        if (self::$rootPath === null) {
            return "Error: Root path not set. Call AndroidXmlRenderer::setRootPath() first.";
        }
        
        // Construir la ruta completa al archivo de la vista
        $viewPath = self::$rootPath . '/app/src/main/res/layout/' . $viewName . '.xml';
        
        if (!file_exists($viewPath)) {
            return "Error: View file not found: $viewPath";
        }
        
        $xmlString = file_get_contents($viewPath);
        return self::renderXml($xmlString);
    }
    
    /**
     * Renderiza un string XML de Android a HTML/CSS
     * 
     * @param string $xmlString Contenido del archivo XML de Android
     * @return string HTML renderizado
     */
    public static function renderXml($xmlString)
    {
        // Cargar el XML
        $xml = simplexml_load_string($xmlString);
        if (!$xml) {
            return "Error cargando XML";
        }

        // Iniciar la salida HTML
        $output = self::getHeader();

        // Renderizar el contenido XML
        $output .= self::renderElement($xml);

        // Cerrar el documento HTML
        $output .= self::getFooter();

        return $output;
    }
    
    /**
     * Carga un color desde los recursos del proyecto
     * 
     * @param string $colorName Nombre del color (sin @color/)
     * @return string Valor del color o null si no se encuentra
     */
    private static function loadColorResource($colorName)
    {
        // Verificar si ya está en caché
        if (isset(self::$colorCache[$colorName])) {
            return self::$colorCache[$colorName];
        }
        
        if (self::$rootPath === null) {
            return null;
        }
        
        // Buscar en los archivos colors.xml
        $colorFiles = [
            self::$rootPath . '/app/src/main/res/values/colors.xml',
            self::$rootPath . '/app/src/main/res/values-night/colors.xml'
        ];
        
        foreach ($colorFiles as $file) {
            if (file_exists($file)) {
                $xml = simplexml_load_file($file);
                if ($xml) {
                    foreach ($xml->color as $color) {
                        $name = (string)$color['name'];
                        if ($name === $colorName) {
                            $value = (string)$color;
                            self::$colorCache[$colorName] = $value;
                            return $value;
                        }
                    }
                }
            }
        }
        
        // Si no se encuentra, buscar en los colores predefinidos
        return isset(self::$predefinedColors[$colorName]) ? self::$predefinedColors[$colorName] : null;
    }
    
    /**
     * Carga un drawable desde los recursos del proyecto
     * 
     * @param string $drawableName Nombre del drawable (sin @drawable/)
     * @return string Contenido del drawable o null si no se encuentra
     */
    private static function loadDrawableResource($drawableName)
    {
        // Verificar si ya está en caché
        if (isset(self::$drawableCache[$drawableName])) {
            return self::$drawableCache[$drawableName];
        }
        
        if (self::$rootPath === null) {
            return null;
        }
        
        // Posibles extensiones y directorios para buscar
        $extensions = ['.xml', '.png', '.jpg', '.svg'];
        $directories = [
            self::$rootPath . '/app/src/main/res/drawable/',
            self::$rootPath . '/app/src/main/res/drawable-hdpi/',
            self::$rootPath . '/app/src/main/res/drawable-mdpi/',
            self::$rootPath . '/app/src/main/res/drawable-xhdpi/',
            self::$rootPath . '/app/src/main/res/drawable-xxhdpi/',
            self::$rootPath . '/app/src/main/res/drawable-xxxhdpi/'
        ];
        
        // Buscar el archivo drawable
        foreach ($directories as $dir) {
            foreach ($extensions as $ext) {
                $path = $dir . $drawableName . $ext;
                if (file_exists($path)) {
                    $content = file_get_contents($path);
                    
                    // Si es XML, procesarlo como shape drawable
                    if ($ext === '.xml') {
                        return self::processDrawableXml($content, $drawableName);
                    }
                    
                    // Si es una imagen, convertirla a base64
                    $imageType = substr($ext, 1); // Quitar el punto
                    if ($imageType === 'svg') {
                        $imageType = 'svg+xml';
                    }
                    
                    $base64 = base64_encode($content);
                    $drawable = 'background-image: url("data:image/' . $imageType . ';base64,' . $base64 . '"); ';
                    $drawable .= 'background-repeat: no-repeat; background-position: center; background-size: contain; ';
                    
                    self::$drawableCache[$drawableName] = $drawable;
                    return $drawable;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Procesa un archivo XML de drawable 
     * 
     * @param string $xmlContent Contenido XML del drawable
     * @param string $drawableName Nombre del drawable
     * @return string Estilo CSS o HTML para el drawable
     */
    private static function processDrawableXml($xmlContent, $drawableName)
    {
        $xml = simplexml_load_string($xmlContent);
        if (!$xml) {
            return null;
        }
        
        $rootElement = $xml->getName();
        
        // Manejar diferentes tipos de drawables
        switch ($rootElement) {
            case 'shape':
                return self::processShapeDrawable($xml);
                
            case 'vector':
                // Convertir vector a SVG
                $svgContent = self::convertVectorToSvg($xml);
                $svgBase64 = base64_encode($svgContent);
                $drawable = 'background-image: url("data:image/svg+xml;base64,' . $svgBase64 . '"); ';
                $drawable .= 'background-repeat: no-repeat; background-position: center; background-size: contain; ';
                return $drawable;
                
            case 'selector':
                // Para selectores, usamos el primer item (estado normal)
                if (isset($xml->item[0]) && isset($xml->item[0]['drawable'])) {
                    $itemDrawable = (string)$xml->item[0]['drawable'];
                    return self::getDrawable($itemDrawable);
                }
                break;
                
            case 'layer-list':
                // Para layer-list, usamos el último item (capa superior)
                $items = $xml->item;
                $lastItem = end($items);
                if (isset($lastItem['drawable'])) {
                    $itemDrawable = (string)$lastItem['drawable'];
                    return self::getDrawable($itemDrawable);
                }
                break;
        }
        
        return null;
    }
    
    /**
     * Procesa un drawable de tipo shape
     * 
     * @param SimpleXMLElement $xml Elemento XML del shape
     * @return string Estilo CSS para el shape
     */
    private static function processShapeDrawable($xml)
    {
        $style = '';
        
        // Forma
        $shape = isset($xml['shape']) ? (string)$xml['shape'] : 'rectangle';
        
        // Color de fondo (solid)
        $solid = $xml->solid;
        if ($solid && isset($solid['android:color'])) {
            $color = (string)$solid['android:color'];
            $style .= 'background-color: ' . self::resolveColor($color) . '; ';
        }
        
        // Bordes (stroke)
        $stroke = $xml->stroke;
        if ($stroke) {
            $width = isset($stroke['android:width']) ? (string)$stroke['android:width'] : '1dp';
            $color = isset($stroke['android:color']) ? (string)$stroke['android:color'] : '#000000';
            $style .= 'border: ' . self::convertDimension($width) . ' solid ' . self::resolveColor($color) . '; ';
        }
        
        // Esquinas redondeadas
        $corners = $xml->corners;
        if ($corners) {
            if (isset($corners['android:radius'])) {
                $radius = (string)$corners['android:radius'];
                $style .= 'border-radius: ' . self::convertDimension($radius) . '; ';
            } else {
                // Radios específicos
                $attrs = [
                    'android:topLeftRadius', 
                    'android:topRightRadius', 
                    'android:bottomLeftRadius', 
                    'android:bottomRightRadius'
                ];
                $radii = [];
                
                foreach ($attrs as $attr) {
                    $radii[] = isset($corners[$attr]) ? self::convertDimension((string)$corners[$attr]) : '0';
                }
                
                if (count(array_unique($radii)) === 1 && $radii[0] !== '0') {
                    $style .= 'border-radius: ' . $radii[0] . '; ';
                } else if (count(array_filter($radii, function($r) { return $r !== '0'; })) > 0) {
                    $style .= 'border-radius: ' . implode(' ', $radii) . '; ';
                }
            }
        }
        
        // Gradiente
        $gradient = $xml->gradient;
        if ($gradient) {
            $type = isset($gradient['android:type']) ? (string)$gradient['android:type'] : 'linear';
            $startColor = isset($gradient['android:startColor']) ? self::resolveColor((string)$gradient['android:startColor']) : '#000000';
            $endColor = isset($gradient['android:endColor']) ? self::resolveColor((string)$gradient['android:endColor']) : '#FFFFFF';
            $angle = isset($gradient['android:angle']) ? (int)(string)$gradient['android:angle'] : 0;
            
            // Convertir ángulo a dirección CSS
            $direction = 'to bottom';
            if ($angle === 0) $direction = 'to right';
            else if ($angle === 45) $direction = 'to bottom right';
            else if ($angle === 90) $direction = 'to bottom';
            else if ($angle === 135) $direction = 'to bottom left';
            else if ($angle === 180) $direction = 'to left';
            else if ($angle === 225) $direction = 'to top left';
            else if ($angle === 270) $direction = 'to top';
            else if ($angle === 315) $direction = 'to top right';
            
            if ($type === 'linear') {
                $style .= 'background: linear-gradient(' . $direction . ', ' . $startColor . ', ' . $endColor . '); ';
            } else if ($type === 'radial') {
                $style .= 'background: radial-gradient(circle, ' . $startColor . ', ' . $endColor . '); ';
            }
        }
        
        // Padding
        $padding = $xml->padding;
        if ($padding) {
            $attrs = ['android:left', 'android:top', 'android:right', 'android:bottom'];
            $values = [];
            
            foreach ($attrs as $attr) {
                $values[$attr] = isset($padding[$attr]) ? self::convertDimension((string)$padding[$attr]) : '0';
            }
            
            if (count(array_unique($values)) === 1) {
                $style .= 'padding: ' . $values['android:top'] . '; ';
            } else {
                $style .= 'padding: ' . $values['android:top'] . ' ' . $values['android:right'] . ' ' 
                       . $values['android:bottom'] . ' ' . $values['android:left'] . '; ';
            }
        }
        
        return $style;
    }
    
    /**
     * Convierte un vector drawable a SVG
     * 
     * @param SimpleXMLElement $xml Elemento XML del vector
     * @return string Contenido SVG
     */
    private static function convertVectorToSvg($xml)
    {
        $width = isset($xml['android:viewportWidth']) ? (string)$xml['android:viewportWidth'] : '24';
        $height = isset($xml['android:viewportHeight']) ? (string)$xml['android:viewportHeight'] : '24';
        
        $svg = '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '" xmlns="http://www.w3.org/2000/svg">';
        
        // Procesar paths
        foreach ($xml->path as $path) {
            $d = isset($path['android:pathData']) ? (string)$path['android:pathData'] : '';
            $fill = isset($path['android:fillColor']) ? self::resolveColor((string)$path['android:fillColor']) : 'none';
            $stroke = isset($path['android:strokeColor']) ? self::resolveColor((string)$path['android:strokeColor']) : 'none';
            $strokeWidth = isset($path['android:strokeWidth']) ? (string)$path['android:strokeWidth'] : '1';
            
            $svg .= '<path d="' . $d . '" fill="' . $fill . '" stroke="' . $stroke . '" stroke-width="' . $strokeWidth . '"/>';
        }
        
        $svg .= '</svg>';
        return $svg;
    }

    /**
     * Genera la cabecera HTML con los estilos CSS
     */
    private static function getHeader()
    {
        $orientation = self::$orientation === 'landscape' ?
            'width: ' . self::$viewportHeight . 'px; height: ' . self::$viewportWidth . 'px;' :
            'width: ' . self::$viewportWidth . 'px; height: ' . self::$viewportHeight . 'px;';

        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Android XML Renderer</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
            font-family: Roboto, Arial, sans-serif;
        }
        
        .android-device {
            ' . $orientation . '
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            overflow: hidden;
            position: relative;
            border-radius: 16px;
        }
        
        /* LinearLayout */
        .linear-layout {
            display: flex;
            box-sizing: border-box;
            position: relative;
        }
        
        .linear-layout[data-orientation="vertical"] {
            flex-direction: column;
        }
        
        .linear-layout[data-orientation="horizontal"] {
            flex-direction: row;
        }
        
        /* TextView */
        .text-view {
            display: block;
            box-sizing: border-box;
        }
        
        /* View (simple div) */
        .view {
            display: block;
            box-sizing: border-box;
        }
        
        /* SeekBar */
        .seek-bar {
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 30px;
            background: transparent;
            outline: none;
        }
        
        .seek-bar::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #ffffff;
            cursor: pointer;
            position: relative;
            z-index: 2;
        }
        
        .seek-bar-container {
            position: relative;
            padding: 10px 0;
        }
        
        .seek-bar-track {
            position: absolute;
            top: 50%;
            left: 15px;
            right: 15px;
            transform: translateY(-50%);
            height: 4px;
            background-color: rgba(255,255,255,0.3);
            border-radius: 2px;
        }

         /* Estilos para LinearLayout horizontal */
        .linear-layout-horizontal {
            display: flex;
            flex-direction: row;
            width: 100%;
            margin-bottom: 8px;
        }
        
        /* Estilos para botones */
        .android-button {
            flex: 1;
            border: none;
            border-radius: 8px;
            margin: 4px;
            padding: 16px 8px;
            text-align: center;
            font-weight: 500;
            font-size: 24px;
            height: 64px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        /* Colores de fondo para botones */
        .custom-button-red { background-color: #FF5555; }
        .custom-button-orange { background-color: #FFA500; }
        .custom-button-black { background-color: #666666; }
        .custom-button-blue { background-color: #2196F3; }
        .custom-button-green { background-color: #4CAF50; }
        .custom-button-purple { background-color: #9C27B0; }
        
        
        [data-visibility="gone"] {
            display: none !important;
        }
    </style>
</head>
<body>
    <div class="android-device">
';
    }

    /**
     * Genera el pie de página HTML
     */
    private static function getFooter()
    {
        return '
    </div>
</body>
</html>';
    }

    /**
     * Renderiza un elemento XML de Android
     */
    private static function renderElement($element)
    {
        $tagName = strtolower($element->getName());

        switch ($tagName) {
            case 'linearlayout':
                return self::renderLinearLayout($element);

            case 'button':
                return self::renderButton($element);

            case 'textview':
                return self::renderTextView($element);

            case 'view':
                return self::renderView($element);

            case 'seekbar':
                return self::renderSeekBar($element);

            default:
                return "<div>Elemento no soportado: $tagName</div>";
        }
    }

    /**
     * Renderiza un LinearLayout de Android
     */
    private static function renderLinearLayout($element)
    {
        $attrs = self::getAttributes($element);

        // Determinar orientación
        $orientation = isset($attrs['android:orientation']) ? $attrs['android:orientation'] : 'horizontal';

        // Determinar visibilidad
        $visibility = isset($attrs['android:visibility']) ? $attrs['android:visibility'] : 'visible';

        // Extraer dimensiones
        $width = self::getDimension($attrs, 'android:layout_width');
        $height = self::getDimension($attrs, 'android:layout_height');

        // Extraer otros estilos
        $weight = isset($attrs['android:layout_weight']) ? $attrs['android:layout_weight'] : '';
        $margin = self::getMargin($attrs);
        $padding = self::getPadding($attrs);
        $background = self::getBackground($attrs);
        $gravity = self::getGravity($attrs);

        // Construir estilo
        $style = "display: flex; ";
        $style .= "flex-direction: " . ($orientation == 'vertical' ? 'column' : 'row') . "; ";
        $style .= $width . $height;

        if ($weight) {
            $style .= "flex: $weight; ";
        }

        $style .= $margin . $padding . $background . $gravity;

        // Extraer ID
        $id = isset($attrs['android:id']) ? ' id="' . self::cleanId($attrs['android:id']) . '"' : '';

        // Construir clase y atributos data
        $class = 'linear-layout';
        $dataAttrs = ' data-orientation="' . $orientation . '"';

        if ($visibility == 'gone') {
            $dataAttrs .= ' data-visibility="gone"';
        }

        // Iniciar div
        $output = '<div class="' . $class . '"' . $id . $dataAttrs . ' style="' . $style . '">';

        // Renderizar elementos hijos
        foreach ($element->children() as $child) {
            $output .= self::renderElement($child);
        }

        // Cerrar div
        $output .= '</div>';

        return $output;
    }

    /**
     * Renderiza un TextView de Android
     */
    private static function renderTextView($element)
    {
        $attrs = self::getAttributes($element);

        // Extraer texto
        $text = isset($attrs['android:text']) ? self::resolveResource($attrs['android:text']) : '';

        // Extraer dimensiones
        $width = self::getDimension($attrs, 'android:layout_width');
        $height = self::getDimension($attrs, 'android:layout_height');

        // Extraer estilos
        $textSize = isset($attrs['android:textsize']) ? "font-size: " . self::convertDimension($attrs['android:textsize']) . "; " : "";
        $textColor = isset($attrs['android:textcolor']) ? "color: " . self::resolveColor($attrs['android:textcolor']) . "; " : "";
        $textStyle = isset($attrs['android:textstyle']) ? self::getTextStyle($attrs['android:textstyle']) : "";
        $weight = isset($attrs['android:layout_weight']) ? "flex: " . $attrs['android:layout_weight'] . "; " : "";
        $margin = self::getMargin($attrs);
        $padding = self::getPadding($attrs);

        // Construir estilo
        $style = $width . $height . $textSize . $textColor . $textStyle . $weight . $margin . $padding;

        // Extraer ID
        $id = isset($attrs['android:id']) ? ' id="' . self::cleanId($attrs['android:id']) . '"' : '';

        // Renderizar el TextView
        return '<div class="text-view"' . $id . ' style="' . $style . '">' . $text . '</div>';
    }

    /**
     * Renderiza un View simple de Android
     */
    private static function renderView($element)
    {
        $attrs = self::getAttributes($element);

        // Extraer dimensiones
        $width = self::getDimension($attrs, 'android:layout_width');
        $height = self::getDimension($attrs, 'android:layout_height');

        // Extraer estilos
        $background = self::getBackground($attrs);
        $weight = isset($attrs['android:layout_weight']) ? "flex: " . $attrs['android:layout_weight'] . "; " : "";
        $margin = self::getMargin($attrs);
        $padding = self::getPadding($attrs);

        // Construir estilo
        $style = $width . $height . $background . $weight . $margin . $padding;

        // Extraer ID
        $id = isset($attrs['android:id']) ? ' id="' . self::cleanId($attrs['android:id']) . '"' : '';

        // Renderizar View como div
        return '<div class="view"' . $id . ' style="' . $style . '"></div>';
    }

    /**
     * Renderiza un SeekBar de Android
     */
    private static function renderSeekBar($element)
    {
        $attrs = self::getAttributes($element);

        // Extraer propiedades del SeekBar
        $max = isset($attrs['android:max']) ? $attrs['android:max'] : 100;
        $progress = isset($attrs['android:progress']) ? $attrs['android:progress'] : 0;

        // Extraer dimensiones
        $width = self::getDimension($attrs, 'android:layout_width');
        $height = "min-height: 48px; ";

        // Extraer estilos
        $margin = self::getMargin($attrs);
        $padding = self::getPadding($attrs);
        $weight = isset($attrs['android:layout_weight']) ? "flex: " . $attrs['android:layout_weight'] . "; " : "";

        // Extraer thumb y track drawables
        $thumb = isset($attrs['android:thumb']) ? self::getDrawable($attrs['android:thumb']) : '';
        $progressDrawable = isset($attrs['android:progressdrawable']) ? self::getDrawable($attrs['android:progressdrawable']) : '';

        // Construir estilo del contenedor
        $containerStyle = $width . $height . $margin . $padding . $weight . "position: relative;";

        // Extraer ID
        $id = isset($attrs['android:id']) ? ' id="' . self::cleanId($attrs['android:id']) . '"' : '';

        // Renderizar SeekBar como un input range con estilizado
        $output = '<div class="seek-bar-container"' . $id . ' style="' . $containerStyle . '">';
        $output .= '<div class="seek-bar-track">' . $progressDrawable . '</div>';
        $output .= '<input type="range" class="seek-bar" min="0" max="' . $max . '" value="' . $progress . '" style="width: 100%;">';

        // Si hay un thumb personalizado, usamos CSS personalizado o un elemento superpuesto
        if ($thumb) {
            $output .= '<style>
                #' . self::cleanId($attrs['android:id']) . ' .seek-bar::-webkit-slider-thumb {
                    background-image: url(\'data:image/svg+xml;utf8,' . str_replace('"', '\'', $thumb) . '\');
                    background-size: contain;
                    background-repeat: no-repeat;
                    background-position: center;
                    background-color: transparent;
                }
            </style>';
        }

        $output .= '</div>';

        return $output;
    }

    private static function renderButton($element)
{
    $attrs = self::getAttributes($element);

    // Extraer texto
    $text = isset($attrs['android:text']) ? self::resolveResource($attrs['android:text']) : '';

    // Extraer dimensiones
    $width = self::getDimension($attrs, 'android:layout_width');
    $height = self::getDimension($attrs, 'android:layout_height');

    // Extraer el fondo para determinar la clase CSS
    $cssClass = 'android-button';
    // if (isset($attrs['android:background']) && preg_match('/@drawable\/custom_button_([a-z]+)/', $attrs['android:background'], $matches)) {
    //     $color = $matches[1];
    //     $cssClass .= ' custom-button-' . $color;
    // }

    // Extraer estilos
    $background = self::getBackground($attrs);
    $textColor = isset($attrs['android:textcolor']) ? "color: " . self::resolveColor($attrs['android:textcolor']) . "; " : "";
    $textSize = isset($attrs['android:textsize']) ? "font-size: " . self::convertDimension($attrs['android:textsize']) . "; " : "";
    $textStyle = isset($attrs['android:textstyle']) ? self::getTextStyle($attrs['android:textstyle']) : "";
    $weight = isset($attrs['android:layout_weight']) ? "flex: " . $attrs['android:layout_weight'] . "; " : "";
    $margin = self::getMargin($attrs);
    $padding = self::getPadding($attrs);

    $style = $width . $height . $textColor . $textSize . $textStyle . $weight . $margin . $padding;

    // Extraer ID
    $id = isset($attrs['android:id']) ? ' id="' . self::cleanId($attrs['android:id']) . '"' : '';

    // Renderizar Button con la clase CSS adecuada
    return '<button class="' . $cssClass . '"' . $id . ' style="' . $style . '">' . $text . '</button>';
}

    /**
     * Extrae todos los atributos de un elemento XML (namespace-aware)
     */
    private static function getAttributes($element)
    {
        $attrs = [];

        // Atributos sin namespace
        foreach ($element->attributes() as $name => $value) {
            $attrs[strtolower($name)] = (string)$value;
        }

        // Atributos con namespace android
        foreach ($element->attributes('android', true) as $name => $value) {
            $attrs['android:' . strtolower($name)] = (string)$value;
        }

        return $attrs;
    }

    /**
     * Limpia un ID de Android (quita @+id/ o @id/)
     */
    private static function cleanId($id)
    {
        return preg_replace('/^@(\+)?id\//', '', $id);
    }

    /**
     * Obtiene la dimensión CSS para width o height
     */
    private static function getDimension($attrs, $attrName)
    {
        if (!isset($attrs[$attrName])) {
            return '';
        }

        $value = $attrs[$attrName];

        if ($value === 'match_parent' || $value === 'fill_parent') {
            return ($attrName === 'android:layout_width') ? 'width: 100%; ' : 'height: 100%; ';
        } elseif ($value === 'wrap_content') {
            return '';
        } else {
            return ($attrName === 'android:layout_width')
                ? 'width: ' . self::convertDimension($value) . '; '
                : 'height: ' . self::convertDimension($value) . '; ';
        }
    }

    /**
     * Convierte una dimensión de Android a CSS
     */
    private static function convertDimension($value)
    {
        // Convertir dp a px (asumiendo una densidad estándar)
        if (preg_match('/^(\d+)dp$/', $value, $matches)) {
            return $matches[1] . 'px';
        }

        // Convertir sp a px (asumiendo una escala de texto estándar)
        if (preg_match('/^(\d+)sp$/', $value, $matches)) {
            return $matches[1] . 'px';
        }

        // Para valores numéricos sin unidad, asumir dp
        if (is_numeric($value)) {
            return $value . 'px';
        }

        return $value;
    }

    /**
     * Obtiene los márgenes como CSS
     */
    private static function getMargin($attrs)
    {
        $margin = '';

        // Margen completo
        if (isset($attrs['android:layout_margin'])) {
            $margin = 'margin: ' . self::convertDimension($attrs['android:layout_margin']) . '; ';
        } else {
            // Márgenes individuales
            $marginTop = isset($attrs['android:layout_margintop']) ? 'margin-top: ' . self::convertDimension($attrs['android:layout_margintop']) . '; ' : '';
            $marginRight = isset($attrs['android:layout_marginright']) ? 'margin-right: ' . self::convertDimension($attrs['android:layout_marginright']) . '; ' : '';
            $marginBottom = isset($attrs['android:layout_marginbottom']) ? 'margin-bottom: ' . self::convertDimension($attrs['android:layout_marginbottom']) . '; ' : '';
            $marginLeft = isset($attrs['android:layout_marginleft']) ? 'margin-left: ' . self::convertDimension($attrs['android:layout_marginleft']) . '; ' : '';

            $margin = $marginTop . $marginRight . $marginBottom . $marginLeft;
        }

        return $margin;
    }

    /**
     * Obtiene el padding como CSS
     */
    private static function getPadding($attrs)
    {
        $padding = '';

        // Padding completo
        if (isset($attrs['android:padding'])) {
            $padding = 'padding: ' . self::convertDimension($attrs['android:padding']) . '; ';
        } else {
            // Paddings individuales
            $paddingTop = isset($attrs['android:paddingtop']) ? 'padding-top: ' . self::convertDimension($attrs['android:paddingtop']) . '; ' : '';
            $paddingRight = isset($attrs['android:paddingright']) ? 'padding-right: ' . self::convertDimension($attrs['android:paddingright']) . '; ' : '';
            $paddingBottom = isset($attrs['android:paddingbottom']) ? 'padding-bottom: ' . self::convertDimension($attrs['android:paddingbottom']) . '; ' : '';
            $paddingLeft = isset($attrs['android:paddingleft']) ? 'padding-left: ' . self::convertDimension($attrs['android:paddingleft']) . '; ' : '';

            $padding = $paddingTop . $paddingRight . $paddingBottom . $paddingLeft;
        }

        return $padding;
    }

    /**
     * Obtiene el estilo de texto (bold, italic, etc.)
     */
    private static function getTextStyle($textStyle)
    {
        $style = '';

        if ($textStyle === 'bold') {
            $style = 'font-weight: bold; ';
        } elseif ($textStyle === 'italic') {
            $style = 'font-style: italic; ';
        } elseif ($textStyle === 'bold|italic') {
            $style = 'font-weight: bold; font-style: italic; ';
        }

        return $style;
    }

    /**
     * Obtiene el background como CSS
     */
    private static function getBackground($attrs)
    {
        if (!isset($attrs['android:background'])) {
            return '';
        }

        $background = $attrs['android:background'];

        // Color referencia
        if (
            preg_match('/@android:color\/(.+)/', $background, $matches) ||
            preg_match('/@color\/(.+)/', $background, $matches)
        ) {
            return 'background-color: ' . self::resolveColor($background) . '; ';
        }

        // Drawable referencia
        if (preg_match('/@drawable\/(.+)/', $background, $matches)) {
            $drawableName = $matches[1];

            // Si es custom_button_X, tratarlo como background-color
            if (strpos($drawableName, 'custom_button_') === 0) {
                $color = substr($drawableName, 13); // Extraer color (red, blue, etc.)
                return isset(self::$predefinedColors[$color])
                    ? 'background-color: ' . self::$predefinedColors[$color] . '; '
                    : 'background-color: #666666; ';
            }

            // Otros drawables
            return self::getDrawableBackground($drawableName);
        }

        // Color hexadecimal directo
        if (preg_match('/#([0-9A-Fa-f]{6}|[0-9A-Fa-f]{8})/', $background)) {
            return 'background-color: ' . $background . '; ';
        }

        return '';
    }

    /**
     * Obtiene un drawable como elemento HTML o estilo CSS
     */
    private static function getDrawable($drawableRef)
    {
        if (preg_match('/@drawable\/(.+)/', $drawableRef, $matches)) {
            $drawableName = $matches[1];
            return isset(self::$drawables[$drawableName]) ? self::$drawables[$drawableName] : '';
        }
        return '';
    }

    /**
     * Obtiene el estilo de fondo para un drawable
     */
    private static function getDrawableBackground($drawableName)
    {
        if (isset(self::$drawables[$drawableName])) {
            $drawable = self::$drawables[$drawableName];

            // Si parece un estilo CSS, devolverlo directamente
            if (strpos($drawable, 'background-color') !== false) {
                return $drawable;
            }

            // Si parece SVG, usarlo como background-image
            if (strpos($drawable, '<svg') !== false) {
                $svgBase64 = base64_encode($drawable);
                return 'background-image: url("data:image/svg+xml;base64,' . $svgBase64 . '"); background-repeat: no-repeat; background-position: center; background-size: contain; ';
            }
        }

        return '';
    }

    /**
     * Resuelve un color de Android
     */
    private static function resolveColor($colorRef)
    {
        // Para colores del sistema Android
        if (preg_match('/@android:color\/(.+)/', $colorRef, $matches)) {
            $colorName = $matches[1];
            return isset(self::$predefinedColors[$colorName]) ? self::$predefinedColors[$colorName] : '#000000';
        }

        // Para colores de la app
        if (preg_match('/@color\/(.+)/', $colorRef, $matches)) {
            $colorName = $matches[1];
            return isset(self::$predefinedColors[$colorName]) ? self::$predefinedColors[$colorName] : '#000000';
        }

        return $colorRef;
    }

    /**
     * Convierte un gravity de Android a CSS flex align
     */
    private static function getGravity($attrs)
    {
        if (!isset($attrs['android:gravity'])) {
            return '';
        }

        $gravity = $attrs['android:gravity'];
        $style = '';

        if (strpos($gravity, 'center') !== false) {
            $style .= 'justify-content: center; align-items: center; ';
        } else {
            // Horizontal gravity
            if (strpos($gravity, 'left') !== false) {
                $style .= 'justify-content: flex-start; ';
            } elseif (strpos($gravity, 'right') !== false) {
                $style .= 'justify-content: flex-end; ';
            }

            // Vertical gravity
            if (strpos($gravity, 'top') !== false) {
                $style .= 'align-items: flex-start; ';
            } elseif (strpos($gravity, 'bottom') !== false) {
                $style .= 'align-items: flex-end; ';
            }
        }

        return $style;
    }

    /**
     * Resuelve un recurso de Android (string o color)
     */
    private static function resolveResource($resource)
    {
        // Para strings
        if (preg_match('/@string\/([a-z_]+)/', $resource, $matches)) {
            $stringName = $matches[1];
            $strings = [
                'add' => '+',
                'sub' => '-',
                'mul' => '×',
                'div' => '÷',
                'equ' => '=',
                'inv' => '1/x',
                'sqr' => '√',
                'backspace' => '⌫',
                'undo' => '↶',
                'redo' => '↷'
            ];

            return isset($strings[$stringName]) ? $strings[$stringName] : $stringName;
        }

        return $resource;
    }
}

// Ejemplo de uso:
// $xml = file_get_contents('lock_screen.xml');
// echo AndroidXmlRenderer::render($xml);
