<?php

namespace simplerest\libs;

use simplerest\core\libs\Strings;

/*
    https://claude.ai/chat/dfe4885d-cb8a-46a6-b9d2-894044faac10
*/

class AndroidXmlRenderer
{
    // Propiedades estáticas para configurar el viewport
    public static $viewportWidth = 360; // Ancho del viewport en dp
    public static $viewportHeight = 640; // Alto del viewport en dp
    public static $orientation = 'portrait'; // Orientación (portrait o landscape)

    // Colores predefinidos (simulación de recursos Android)
    private static $colors = [
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

    // Drawables simulados
    private static $drawables = [
        'ic_lock' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM9 8V6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9z" fill="white"/>
        </svg>',
        'slider_track' => '<div style="height: 4px; background-color: rgba(255,255,255,0.3); border-radius: 2px;"></div>',
        'custom_button_red' => 'background-color: #FF5555;',
        'custom_button_orange' => 'background-color: #FFA500;',
        'custom_button_black' => 'background-color: #666666;',
        'custom_button_blue' => 'background-color: #2196F3;',
        'custom_button_green' => 'background-color: #4CAF50;',
        'custom_button_purple' => 'background-color: #9C27B0;'
    ];

    /**
     * Renderiza un string XML de Android a HTML/CSS
     * 
     * @param string $xmlString Contenido del archivo XML de Android
     * @return string HTML renderizado
     */
    public static function render($xmlString)
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
                return isset(self::$colors[$color])
                    ? 'background-color: ' . self::$colors[$color] . '; '
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
            return isset(self::$colors[$colorName]) ? self::$colors[$colorName] : '#000000';
        }

        // Para colores de la app
        if (preg_match('/@color\/(.+)/', $colorRef, $matches)) {
            $colorName = $matches[1];
            return isset(self::$colors[$colorName]) ? self::$colors[$colorName] : '#000000';
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
