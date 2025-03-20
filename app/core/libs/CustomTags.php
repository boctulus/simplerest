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
        
        // Remove the starting and ending square brackets
        if (preg_match('/^\[(.+?)\]$/', $input, $matches)) {
            $content = $matches[1];
            // Extract the tag name and the rest of the string (parameters)
            if (preg_match('/^(\w+)\s*(.*)$/', $content, $matches)) {
                $result["name"] = $matches[1];
                $paramsString = $matches[2];
                
                // Regex to match key-value pairs with values in double quotes, single quotes or unquoted.
                $pattern = '/(\w+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|(\S+))/';
                preg_match_all($pattern, $paramsString, $paramMatches, PREG_SET_ORDER);
                
                foreach ($paramMatches as $param) {
                    $key = $param[1];
                    // Determine the value from the available capture groups
                    $value = isset($param[2]) && $param[2] !== "" 
                        ? $param[2] 
                        : (isset($param[3]) && $param[3] !== "" ? $param[3] : $param[4]);
                    
                    // Convert numeric strings to int or float if possible.
                    if (is_numeric($value)) {
                        if (strpos($value, '.') !== false) {
                            $value = (float)$value;
                        } else {
                            $value = (int)$value;
                        }
                    }
                    
                    $result["params"][$key] = $value;
                }
            }
        }
        
        return $result;
    }
}

