<?php

namespace simplerest\core\libs;

class CustomTags {
    // Array para almacenar los callbacks registrados por nombre de tag.
    protected static $callbacks = [];

    /**
     * Registra un callback para un tag específico.
     *
     * @param string   $tagName  Nombre del tag (por ejemplo, "calc", "dir").
     * @param callable $callback Función callback que recibirá los parámetros del tag.
     */
    public static function register($tagName, callable $callback) {
        self::$callbacks[$tagName] = $callback;
    }
    
    /**
     * Realiza el parsing de un tag individual.
     *
     * Ejemplo de input:
     * [dir path="C:\\xampp\\htdocs\\simplerest\\logs"]
     *
     * Retorna:
     * [
     *     "name"   => "dir",
     *     "params" => [
     *         "path" => "C:\\xampp\\htdocs\\simplerest\\logs"
     *     ]
     * ]
     *
     * @param string $input La cadena que contiene el tag.
     * @return array El tag parseado con nombre y parámetros.
     */
    public static function parse($input) {
        $result = [
            "name"   => "",
            "params" => []
        ];
        
        // Se espera que $input sea un tag encerrado en corchetes.
        if (preg_match('/^\[(.+?)\]$/', $input, $matches)) {
            $content = $matches[1];
            // Extrae el nombre del tag y el resto de la cadena (parámetros)
            if (preg_match('/^([\w\-]+)\s*(.*)$/', $content, $matches)) {
                $result["name"] = $matches[1];
                $paramsString = $matches[2];
                
                // Regex para extraer parejas clave=valor; soporta valores entre comillas dobles, simples o sin comillas.
                $pattern = '/(\w+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|(\S+))/';
                preg_match_all($pattern, $paramsString, $paramMatches, PREG_SET_ORDER);
                
                foreach ($paramMatches as $param) {
                    $key = $param[1];
                    // Selecciona el primer grupo no vacío.
                    $value = isset($param[2]) && $param[2] !== "" 
                        ? $param[2] 
                        : (isset($param[3]) && $param[3] !== "" ? $param[3] : $param[4]);
                    
                    // Conversión de valores numéricos a int o float, si corresponde.
                    if (is_numeric($value)) {
                        $value = (strpos($value, '.') !== false) ? (float)$value : (int)$value;
                    }
                    
                    $result["params"][$key] = $value;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Realiza el parsing de todos los tags encontrados en una cadena.
     *
     * Ejemplo de input:
     * 'Ejecuta estos calculos [calc op=(50,34,676) operation="mul"] y [calc op=(56676,67) operation="div"]. Dame el resultado en un JSON'
     *
     * Retorna un array con cada tag parseado.
     *
     * @param string $input La cadena que contiene uno o varios tags.
     * @return array Un array de tags parseados.
     */
    public static function parseAll($input) {
        $results = [];
        
        // Busca todas las ocurrencias de un tag entre corchetes en la cadena.
        if (preg_match_all('/\[(.+?)\]/', $input, $matches)) {
            // $matches[0] contiene las coincidencias completas incluyendo corchetes.
            foreach ($matches[0] as $tag) {
                $results[] = self::parse($tag);
            }
        }
        
        return $results;
    }
    
    /**
     * Reemplaza cada tag en la cadena por el resultado de aplicar su callback registrado.
     *
     * Si no se encuentra un callback para un tag, se retorna el tag original.
     *
     * @param string $input La cadena de entrada.
     * @return string La cadena con los tags reemplazados.
     */
    public static function render($input) {
        return preg_replace_callback('/\[(.+?)\]/', function ($match) {
            $tagStr = $match[0];
            $tagData = self::parse($tagStr);
            if (!empty($tagData['name']) && isset(self::$callbacks[$tagData['name']])) {
                $callback = self::$callbacks[$tagData['name']];
                // Se ejecuta el callback pasando los parámetros del tag.
                return call_user_func($callback, $tagData['params']);
            }
            // Si no hay callback registrado, se retorna el tag sin modificar.
            return $tagStr;
        }, $input);
    }
    
    // Alias para render.
    public static function replace($input) {
        return self::render($input);
    }    
}

