<?php

namespace simplerest\core\libs;

class CustomTags
{
    /**
     * Parses a tag string like:
     * [dir path="C:\\xampp\\htdocs\\simplerest\\logs"]
     *
     * It returns an associative array:
     * [
     *   "name"   => "dir",
     *   "params" => [
     *       "path" => "C:\\xampp\\htdocs\\simplerest\\logs"
     *   ]
     * ]
     *
     * @param string $input The tag string to parse.
     * @return array The parsed tag with name and parameters.
     */
    public static function parse($input) {
        $result = [
            "name"   => "",
            "params" => []
        ];
        
        // Cerca il primo tag racchiuso tra parentesi quadre, anche se la stringa contiene altro testo.
        if (preg_match('/\[(.+?)\]/', $input, $matches)) {
            $content = $matches[1];
            // Estrae il nome del tag e il resto dei parametri.
            if (preg_match('/^(\w+)\s*(.*)$/', $content, $matches)) {
                $result["name"] = $matches[1];
                $paramsString = $matches[2];
                
                // Regex per estrarre coppie chiave=valore; supporta valori tra doppie o singole virgolette o non quotati.
                $pattern = '/(\w+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|(\S+))/';
                preg_match_all($pattern, $paramsString, $paramMatches, PREG_SET_ORDER);
                
                foreach ($paramMatches as $param) {
                    $key = $param[1];
                    // Determina il valore dalla corrispondenza trovata.
                    $value = isset($param[2]) && $param[2] !== "" 
                        ? $param[2] 
                        : (isset($param[3]) && $param[3] !== "" ? $param[3] : $param[4]);
                    
                    // Conversione di valori numerici in int o float se applicabile.
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
}

