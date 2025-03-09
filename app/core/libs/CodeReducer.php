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
    public function reduceCode($sourceCode, array $functionsToKeep = [], array $functionsToExclude = [], $language = 'php') {
        // Si no se especifican funciones a mantener, mantener todas por defecto
        if (empty($functionsToKeep)) {
            $functionsToKeep = ['*'];
        }

        // Patrón para identificar funciones globales en PHP
        $functionPattern = '/function\s+(\w+)\s*\(.*?\)\s*\{(?:[^{}]++|(?R))*\}/s';
        preg_match_all($functionPattern, $sourceCode, $functionMatches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        // Almacenar información de las funciones encontradas
        $functions = [];
        foreach ($functionMatches as $match) {
            $functionName = $match[1][0]; // Nombre de la función
            $start = $match[0][1];        // Posición inicial
            $end = $start + strlen($match[0][0]); // Posición final
            $functions[] = [
                'name' => $functionName,
                'start' => $start,
                'end' => $end,
                'code' => $match[0][0]    // Código completo de la función
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
            // Agregar el código entre funciones (incluye clases, etc.)
            if ($func['start'] > $lastEnd) {
                $result .= substr($sourceCode, $lastEnd, $func['start'] - $lastEnd);
            }

            // Incluir solo las funciones que queremos mantener
            if (in_array($func, $toKeep)) {
                $result .= $func['code'];
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