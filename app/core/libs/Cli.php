<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;

class Cli
{
    // Constantes ANSI para colores
    const ANSI_RESET      = "\033[0m";
    const ANSI_BLUE       = "\033[34m";
    const ANSI_GREEN      = "\033[32m";
    const ANSI_YELLOW     = "\033[33m";
    const ANSI_RED        = "\033[31m";
    const ANSI_MAGENTA    = "\033[35m";
    const ANSI_CYAN       = "\033[36m";
    
    public static function is_cli(){
        return (php_sapi_name() == 'cli');
    }

    public static function formatArrayOutput($data, $indent = 0, $useColors = null) 
    {
        // Si no se especifica useColors, detectar si estamos en CLI
        if ($useColors === null) {
            $useColors = php_sapi_name() === 'cli';
        }

        if (!is_array($data)) {
            return self::formatValue($data, $useColors);
        }

        $output = $useColors ? self::ANSI_YELLOW . "[" . self::ANSI_RESET : "[";
        $output .= "\n";
        $indentStr = str_repeat('    ', $indent + 1);
        
        foreach ($data as $key => $value) {
            $output .= $indentStr;
            
            // Format key
            if (is_string($key)) {
                $output .= $useColors ? self::ANSI_GREEN . "'" . addslashes($key) . "'" . self::ANSI_RESET : "'" . addslashes($key) . "'";
            } else {
                $output .= $useColors ? self::ANSI_BLUE . $key . self::ANSI_RESET : $key;
            }
            
            $output .= $useColors ? self::ANSI_YELLOW . " => " . self::ANSI_RESET : " => ";
            
            // Format value recursivamente
            if (is_array($value)) {
                $output .= self::formatArrayOutput($value, $indent + 1, $useColors);
            } else {
                $output .= self::formatValue($value, $useColors);
            }
            
            $output .= ",\n";
        }
        
        $output .= str_repeat('    ', $indent);
        $output .= $useColors ? self::ANSI_YELLOW . "]" . self::ANSI_RESET : "]";
        return $output;
    }

    protected static function formatValue($value, $useColors = false)
    {
        if (is_string($value)) {
            return $useColors ? 
                   self::ANSI_GREEN . "'" . addslashes($value) . "'" . self::ANSI_RESET : 
                   "'" . addslashes($value) . "'";
        } 
        if (is_null($value)) {
            return $useColors ? 
                   self::ANSI_RED . "null" . self::ANSI_RESET : 
                   "null";
        } 
        if (is_bool($value)) {
            $strValue = $value ? "true" : "false";
            return $useColors ? 
                   self::ANSI_MAGENTA . $strValue . self::ANSI_RESET : 
                   $strValue;
        } 
        // Números (int/float)
        return $useColors ? 
               self::ANSI_CYAN . $value . self::ANSI_RESET : 
               $value;
    }


}

