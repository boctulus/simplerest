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
    public function reduceCode($sourceCode, array $functionsToKeep = [], array $functionsToExclude = [], array $replace_with_interface = []) {
        // Si $functionsToKeep está vacío, asumimos que queremos mantener todas las funciones
        if (empty($functionsToKeep)) {
            $functionsToKeep = ['*'];
        }

        // Patrón para detectar funciones globales
        $functionPattern = '/function\s+(\w+)\s*\(.*?\)\s*\{(?:[^{}]++|(?R))*\}/s';
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
                if (in_array($func['name'], $replace_with_interface)) {
                    // Extraer la firma de la función y reemplazar su cuerpo
                    $signaturePattern = '/(function\s+\w+\s*\(.*?\))\s*\{/s';
                    if (preg_match($signaturePattern, $func['code'], $signatureMatch)) {
                        $signature = $signatureMatch[1];
                        $result .= $signature . " { /* ya implementado */ }";
                    } else {
                        $result .= $func['code']; // Si falla el parseo, mantener el original
                    }
                } else {
                    $result .= $func['code']; // Mantener la función completa
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
    
    public function reduceClass($sourceCode, array $methodsToKeep) {
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