<?php

namespace simplerest\core\libs;

/*
    Clase sencilla para reducir el código PHP manteniendo solo los métodos deseados

    Alternativa a la que provee PHPParser->reduceCode()
*/
class CodeReducer 
{  
    public function reduce($sourceCode, array $methodsToKeep) {
        // Paso 1: Extraer el contenido hasta el cuerpo de la clase
        if (!preg_match('/^(.*?class\s+\w+.*?\{)/s', $sourceCode, $matches)) {
            throw new \Exception("No se pudo encontrar la declaración de clase");
        }
        
        $header = $matches[1];
        $classBodyStart = strlen($header);
        
        // Paso 2: Encontrar todos los métodos en la clase
        $methodPattern = '/^\s*((?:public|private|protected|static)*\s+function\s+(\w+)\s*\(.*?\).*?\{(?:[^{}]++|(?R))*\})/sm';
        preg_match_all($methodPattern, substr($sourceCode, $classBodyStart), $methodMatches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        
        $methods = [];
        foreach ($methodMatches as $match) {
            $methodCode = $match[1][0];
            $methodName = $match[2][0];
            $position = $classBodyStart + $match[1][1];
            $length = strlen($methodCode);
            
            $methods[] = [
                'name' => $methodName,
                'code' => $methodCode,
                'start' => $position,
                'end' => $position + $length,
                'keep' => in_array($methodName, $methodsToKeep)
            ];
        }
        
        // Paso 3: Ordenar métodos por posición
        usort($methods, function($a, $b) {
            return $a['start'] - $b['start'];
        });
        
        // Paso 4: Reconstruir el código manteniendo solo los métodos deseados
        $result = $header;
        $lastEnd = $classBodyStart;
        $replacedAny = false;
        
        foreach ($methods as $method) {
            // Agregar código entre el último método y éste
            if ($method['start'] > $lastEnd) {
                $result .= substr($sourceCode, $lastEnd, $method['start'] - $lastEnd);
            }
            
            if ($method['keep']) {
                $result .= $method['code'];
                $replacedAny = false;
            } else {
                if (!$replacedAny) {
                    $result .= "    // ...\n";
                    $replacedAny = true;
                }
            }
            
            $lastEnd = $method['end'];
        }
        
        // Agregar el resto del código después del último método
        $result .= substr($sourceCode, $lastEnd);
        
        return $result;
    }
}