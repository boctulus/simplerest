<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\src\Traits;

Trait LogCat 
{
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