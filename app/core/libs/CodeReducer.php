<?php

namespace simplerest\core\libs;

/*
    Clase sencilla para reducir el código PHP manteniendo solo los métodos deseados

    Alternativa a la que provee PHPParser->reduceCode()
*/
class CodeReducer 
{  
    /*
        Reduce el código fuente manteniendo solo las funciones/métodos especificados.
        
        @param string $sourceCode Código fuente a reducir
        @param array $functionsToKeep Funciones/métodos a mantener (puede incluir "*" para todos)
        @param array $functionsToExclude Funciones/métodos a excluir
        @param string $language Lenguaje del código (por ahora solo "php", extensible a "js" o "python")
        @return string Código reducido

        Para excluir funciones

        Ej:

        $file = Files::getContent("D:\\laragon\\www\\simplerest\\etc\\test.php");

        dd(
            (new CodeReducer())->reduce($file, [], ['tb_prefix'])
        );    

        Para incluir funciones

        Ej:

        $file = Files::getContent("D:\\laragon\\www\\simplerest\\etc\\test.php");

        dd(
            (new CodeReducer())->reduce($file, ['tb_prefix'])
        );   
    */
    public function reduceCode(
        $sourceCode, 
        array $functionsToKeep = [], 
        array $functionsToExclude = [], 
        array $replace_with_interface = [], 
        array $interface_replacement_exclusion_list = []
    ) {
        // Si $functionsToKeep está vacío, asumimos que queremos mantener todas las funciones
        if (empty($functionsToKeep)) {
            $functionsToKeep = ['*'];
        }

        // Patrón para detectar funciones globales
        $functionPattern = '/(?:(?:public|protected|private|static|final)\s+)*function\s+(\w+)\s*\(.*?\)\s*\{(?:[^{}]++|(?R))*\}/s';

        preg_match_all($functionPattern, $sourceCode, $functionMatches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        // Almacenar información de las funciones
        $functions = [];
        foreach ($functionMatches as $match) {
            $functionName = $match[1][0];
            $start = $match[0][1];
            $end = $start + strlen($match[0][0]);
            $functions[] = [
                'name' => $functionName,
                'start' => $start,
                'end' => $end,
                'code' => $match[0][0]
            ];
        }

        // Ordenar funciones por posición en el código
        usort($functions, function($a, $b) {
            return $a['start'] - $b['start'];
        });

        // Determinar qué funciones mantener
        $keepAll = in_array('*', $functionsToKeep);
        $toKeep = [];
        foreach ($functions as $func) {
            if ($keepAll) {
                if (!in_array($func['name'], $functionsToExclude)) {
                    $toKeep[] = $func;
                }
            } else {
                if (in_array($func['name'], $functionsToKeep) && !in_array($func['name'], $functionsToExclude)) {
                    $toKeep[] = $func;
                }
            }
        }

        // Reconstruir el código
        $result = '';
        $lastEnd = 0;
        foreach ($functions as $func) {
            // Agregar el código entre funciones (como clases u otro contenido)
            if ($func['start'] > $lastEnd) {
                $result .= substr($sourceCode, $lastEnd, $func['start'] - $lastEnd);
            }

            if (in_array($func, $toKeep)) {
                // Verificar si la función está en $replace_with_interface
                if (in_array($func['name'], $replace_with_interface)) {
                    // Si $interface_replacement_exclusion_list no está vacío, solo las funciones listadas ahí se mantienen completas
                    if (!empty($interface_replacement_exclusion_list)) {
                        if (in_array($func['name'], $interface_replacement_exclusion_list)) {
                            $result .= $func['code']; // Mantener la función completa
                        } else {
                            // Extraer la firma y reemplazar el cuerpo
                            $signaturePattern = '/(function\s+\w+\s*\(.*?\))\s*\{/s';
                            if (preg_match($signaturePattern, $func['code'], $signatureMatch)) {
                                $signature = $signatureMatch[1];
                                $result .= $signature . " { /* ya implementado */ }";
                            } else {
                                $result .= $func['code']; // Si falla el parseo, mantener el original
                            }
                        }
                    } else {
                        // Si $interface_replacement_exclusion_list está vacío, reemplazar todas en $replace_with_interface
                        $signaturePattern = '/(function\s+\w+\s*\(.*?\))\s*\{/s';
                        if (preg_match($signaturePattern, $func['code'], $signatureMatch)) {
                            $signature = $signatureMatch[1];
                            $result .= $signature . " { /* ya implementado */ }";
                        } else {
                            $result .= $func['code'];
                        }
                    }
                } else {
                    $result .= $func['code']; // Mantener la función completa si no está en $replace_with_interface
                }
            }

            $lastEnd = $func['end'];
        }

        // Agregar el código restante después de la última función
        if ($lastEnd < strlen($sourceCode)) {
            $result .= substr($sourceCode, $lastEnd);
        }

        return $result;
    }
    
    
}