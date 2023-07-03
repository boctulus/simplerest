<?php

namespace simplerest\core\libs;

class MediaType
{
    /*       
        Ej:

        Un metodo en un controlador o una ruta conteniendo algo como,...

        function image(){
            $str = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAA.......SuQmCC';

            MediaType::renderImage($str);
        }
    */
    static function renderImage(string $image_content) {
        // Verificar si es base64
        if (strpos($image_content, 'base64') === false) {
            echo 'No se proporcionó una cadena de base64 válida.';
            return;
        }
    
        // Obtener el tipo de imagen
        $matches = [];
        preg_match('/data:image\/(.*?);/', $image_content, $matches);
        $image_type = $matches[1];
    
        // Decodificar la cadena de base64
        $image_data = base64_decode(preg_replace('/data:image\/(.*?);base64,/', '', $image_content));
    
        // Establecer las cabeceras adecuadas
        header('Content-Type: image/' . $image_type);
        header('Content-Length: ' . strlen($image_data));
    
        // Salida de la imagen
        echo $image_data;
        exit;
    }
    
}

