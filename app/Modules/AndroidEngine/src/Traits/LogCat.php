<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\Src\Traits;

use Boctulus\Simplerest\Core\Libs\Files;


Trait LogCat 
{
    /*
        Procesa un archivo con contenido del log de LogCat y extrae las
        lineas que se correspoden al nivel de log

        V — Verbose
        D — Debug
        I — Info
        W — Warning
        E — Error
        A — Assert (Fallos que deberían nunca ocurrir)
    */
    function filterLogByLevel(string $logContent, string $levelPattern): array {
        $levels = explode('|', $levelPattern);
        $lines = explode("\n", $logContent);
        $filtered = [];

        foreach ($lines as $line) {
            // Divide la línea por espacios múltiples y reindexa
            $parts = array_values(array_filter(explode(' ', $line), fn($s) => $s !== ''));

            // Verifica que haya al menos 5 columnas
            if (isset($parts[5]) && in_array($parts[5], $levels)) {
                $filtered[] = $line;
            }
        }

        return $filtered;
    }


    /**
     * Extracts Android Log.*("TAG", "Message") statements from code and returns them as structured array.
     *
     * @param string $code Java/Kotlin source code
     * @return array Array of [tag, message, severity]
     */
    function extractAndroidLogs(string $code): array {
        $regex = '/Log\.(d|i|e|w|v|wtf)\s*\(\s*["\']([^"\']+)["\']\s*,\s*["\']([^"\']+)["\']\s*\)\s*;/';
        $result = [];

        if (preg_match_all($regex, $code, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                // $match[1] = severity, [2] = tag, [3] = message
                $result[] = [$match[2], $match[3], $match[3]];
            }
        }

        return $result;
    }

    /*
        Encuentra Log.{level}('{TAG}', '{message}')
        en distintos archivos
    */
    function findLogsInFiles(bool $only_tags = true){
        $files = $this->findCodeFiles();

        $matches = [];
        foreach ($files as $file){
            $matches = array_merge($matches, $this->extractAndroidLogs(file_get_contents($file)));
        }

        if ($only_tags){
            return array_column($matches, 1);
        }

        return $matches;
    }
}