<?php

namespace simplerest\libs;

use simplerest\core\libs\Strings;

class AndroidXmlRenderer {
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
        'purple' => '#9C27B0'
    ];
    
    /**
     * Renderiza un string XML de Android a HTML/CSS
     * 
     * @param string $xmlString Contenido del archivo XML de Android
     * @return string HTML renderizado
     */
    public static function render($xmlString) {
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
    private static function getHeader() {
        $orientation = self::$orientation === 'landscape' ? 
            'width: '.self::$viewportHeight.'px; height: '.self::$viewportWidth.'px;' : 
            'width: '.self::$viewportWidth.'px; height: auto;';
            
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Android Calculator Renderer</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #121212;
            font-family: Roboto, Arial, sans-serif;
        }
        
        .android-device {
            '.$orientation.'
            background-color: #121212;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
            overflow: hidden;
            position: relative;
            border-radius: 16px;
            padding: 16px;
        }
        
        /* Estilos para LinearLayout vertical */
        .linear-layout-vertical {
            display: flex;
            flex-direction: column;
            width: 100%;
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
            display: none;
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
    private static function getFooter() {
        return '
    </div>
</body>
</html>';
    }
    
    /**
     * Renderiza un elemento XML de Android
     */
    private static function renderElement($element) {
        $tagName = strtolower($element->getName());
        
        switch ($tagName) {
            case 'linearlayout':
                return self::renderLinearLayout($element);
                
            case 'button':
                return self::renderButton($element);
                
            default:
                return "<div>Elemento no soportado: $tagName</div>";
        }
    }
    
    /**
     * Renderiza un LinearLayout de Android
     */
    private static function renderLinearLayout($element) {
        $attrs = self::getAttributes($element);
        
        // Determinar orientación
        $orientation = isset($attrs['android:orientation']) && $attrs['android:orientation'] == 'vertical' 
            ? 'vertical' 
            : 'horizontal';
        
        // Determinar visibilidad
        $visibility = isset($attrs['android:visibility']) && $attrs['android:visibility'] == 'gone' 
            ? 'gone' 
            : 'visible';
        
        // Obtener ID si existe
        $id = isset($attrs['android:id']) ? self::cleanId($attrs['android:id']) : '';
        
        // Construir clase HTML
        $class = 'linear-layout-' . $orientation;
        
        // Construir atributos HTML
        $htmlAttrs = 'class="' . $class . '"';
        if ($id) {
            $htmlAttrs .= ' id="' . $id . '"';
        }
        if ($visibility == 'gone') {
            $htmlAttrs .= ' data-visibility="gone"';
        }
        
        // Iniciar div
        $output = '<div ' . $htmlAttrs . '>';
        
        // Renderizar elementos hijos
        foreach ($element->children() as $child) {
            $output .= self::renderElement($child);
        }
        
        // Cerrar div
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Renderiza un Button de Android
     */
    private static function renderButton($element) {
        $attrs = self::getAttributes($element);
        
        // Obtener texto del botón
        $text = isset($attrs['android:text']) ? self::resolveResource($attrs['android:text']) : '';
        
        // Obtener ID si existe
        $id = isset($attrs['android:id']) ? self::cleanId($attrs['android:id']) : '';
        
        // Obtener color de fondo
        $background = isset($attrs['android:background']) ? self::getBackgroundClass($attrs['android:background']) : '';
        
        // Obtener color del texto
        $textColor = isset($attrs['android:textcolor']) ? self::resolveColor($attrs['android:textcolor']) : '#FFFFFF';
        
        // Construir atributos HTML
        $htmlAttrs = 'class="android-button ' . $background . '"';
        if ($id) {
            $htmlAttrs .= ' id="' . $id . '"';
        }
        $htmlAttrs .= ' style="color: ' . $textColor . ';"';
        
        // Construir botón
        return '<button ' . $htmlAttrs . '>' . $text . '</button>';
    }
    
    /**
     * Extrae todos los atributos de un elemento XML (namespace-aware)
     */
    private static function getAttributes($element) {
        $attrs = [];
        
        // Atributos sin namespace
        foreach ($element->attributes() as $name => $value) {
            $attrs[$name] = (string)$value;
        }
        
        // Atributos con namespace android
        foreach ($element->attributes('android', true) as $name => $value) {
            $attrs['android:' . strtolower($name)] = (string)$value;
        }
        
        return $attrs;
    }
    
    /**
     * Limpia un ID de Android (quita @+id/)
     */
    private static function cleanId($id) {
        return preg_replace('/^@\+id\//', '', $id);
    }
    
    /**
     * Obtiene la clase CSS para un background drawable
     */
    private static function getBackgroundClass($drawable) {
        if (preg_match('/@drawable\/custom_button_([a-z]+)/', $drawable, $matches)) {
            return 'custom-button-' . $matches[1];
        }
        return '';
    }
    
    /**
     * Resuelve un recurso de Android (string o color)
     */
    private static function resolveResource($resource) {
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
    
    /**
     * Resuelve un color de Android
     */
    private static function resolveColor($colorRef) {
        if (preg_match('/@color\/([a-z_]+)/', $colorRef, $matches)) {
            $colorName = $matches[1];
            return isset(self::$colors[$colorName]) ? self::$colors[$colorName] : '#FFFFFF';
        }
        return $colorRef;
    }
}

