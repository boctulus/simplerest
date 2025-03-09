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
        // Se $functionsToKeep è vuoto, assumiamo che vogliamo mantenere tutte le funzioni
        if (empty($functionsToKeep)) {
            $functionsToKeep = ['*'];
        }
    
        // Pattern per individuare funzioni globali (inclusi eventuali modificatori)
        $functionPattern = '/(?:(?:public|protected|private|static|final)\s+)*function\s+(\w+)\s*\(.*?\)\s*\{(?:[^{}]++|(?R))*\}/s';
        preg_match_all($functionPattern, $sourceCode, $functionMatches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
    
        // Memorizzare le informazioni delle funzioni
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
    
        // Ordinare le funzioni per posizione nel codice
        usort($functions, function($a, $b) {
            return $a['start'] - $b['start'];
        });
    
        // Determinare quali funzioni mantenere
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
        
        // Se $replace_with_interface è vuoto e $interface_replacement_exclusion_list non lo è,
        // impostiamo $replace_with_interface come tutte le funzioni in $toKeep escluse quelle in $interface_replacement_exclusion_list
        if (empty($replace_with_interface) && !empty($interface_replacement_exclusion_list)) {
            $namesToKeep = array_map(function($f) {
                return $f['name'];
            }, $toKeep);
            $replace_with_interface = array_diff($namesToKeep, $interface_replacement_exclusion_list);
        }
    
        // Ricostruire il codice
        $result = '';
        $lastEnd = 0;
        foreach ($functions as $func) {
            // Aggiungere il codice tra le funzioni (come classi o altro contenuto)
            if ($func['start'] > $lastEnd) {
                $result .= substr($sourceCode, $lastEnd, $func['start'] - $lastEnd);
            }
    
            if (in_array($func, $toKeep)) {
                // Verifica se la funzione è in $replace_with_interface
                if (in_array($func['name'], $replace_with_interface)) {
                    // Se $interface_replacement_exclusion_list non è vuoto, solo le funzioni in esso elencate si mantengono complete
                    if (!empty($interface_replacement_exclusion_list)) {
                        if (in_array($func['name'], $interface_replacement_exclusion_list)) {
                            $result .= $func['code']; // Mantiene la funzione completa
                        } else {
                            // Estrae la firma e sostituisce il corpo
                            $signaturePattern = '/(function\s+\w+\s*\(.*?\))\s*\{/s';
                            if (preg_match($signaturePattern, $func['code'], $signatureMatch)) {
                                $signature = $signatureMatch[1];
                                $result .= $signature . " { /* ya implementado */ }";
                            } else {
                                $result .= $func['code']; // Se il parsing fallisce, mantiene il codice originale
                            }
                        }
                    } else {
                        // Se $interface_replacement_exclusion_list è vuoto, sostituisce tutte le funzioni in $replace_with_interface
                        $signaturePattern = '/(function\s+\w+\s*\(.*?\))\s*\{/s';
                        if (preg_match($signaturePattern, $func['code'], $signatureMatch)) {
                            $signature = $signatureMatch[1];
                            $result .= $signature . " { /* ya implementado */ }";
                        } else {
                            $result .= $func['code'];
                        }
                    }
                } else {
                    $result .= $func['code']; // Mantiene la funzione completa se non è in $replace_with_interface
                }
            }
    
            $lastEnd = $func['end'];
        }
    
        // Aggiungere il codice rimanente dopo l'ultima funzione
        if ($lastEnd < strlen($sourceCode)) {
            $result .= substr($sourceCode, $lastEnd);
        }
    
        return $result;
    }
    
    
    
}