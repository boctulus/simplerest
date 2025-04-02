<?php

namespace Boctulus\Simplerest\libs;

use Boctulus\Simplerest\Core\Libs\Arrays;
use Boctulus\Simplerest\Core\Libs\Strings;

/*
    Generar excepciones, etc
    
*/
class Documentor
{
    /**
     * Lee un archivo JSON y genera MarkDown a partir del mismo.
     *
     * @param string $filePath Ruta del archivo JSON.
     * @return string El contenido convertido a Markdown o mensaje de error.
     */
    public static function fromJSONtoMarkDown($filePath) {
        if (!file_exists($filePath)) {
            return "Archivo no encontrado: " . $filePath;
        }
        
        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);
        
        if ($data === null) {
            return "JSON inválido.";
        }
        
        // Convertir el array a Markdown usando una función recursiva
        return self::convertToMarkdown($data);
    }

    /**
     * Función recursiva para convertir un array a formato Markdown.
     *
     * @param mixed $data Datos a convertir.
     * @param int $level Nivel de anidamiento para determinar la cantidad de '#'
     * @return string Markdown generado.
     */
    private static function convertToMarkdown($data, $level = 0) {
        $markdown = "";
        
        if (is_array($data)) {
            // Verifica si es un array asociativo
            if (Arrays::isAssoc($data)) {
                foreach ($data as $key => $value) {
                    // El encabezado usa (nivel + 1) '#' para cumplir con la jerarquía Markdown
                    $headerPrefix = str_repeat("#", $level + 1);
                    $markdown .= $headerPrefix . " " . $key . "\n\n";
                    $markdown .= self::convertToMarkdown($value, $level + 1);
                }
            } else {
                // Es un array indexado
                foreach ($data as $item) {
                    if (is_array($item)) {
                        $markdown .= self::convertToMarkdown($item, $level);
                    } else {
                        $markdown .= "- " . $item . "\n";
                    }
                }
                $markdown .= "\n";
            }
        } else {
            // Si es un valor escalar, se imprime directamente.
            $markdown .= $data . "\n\n";
        }
        
        return $markdown;
    }

    /**
     * Determina si un array es asociativo.
     *
     * @param array $arr Array a verificar.
     * @return bool True si es asociativo, false si es indexado.
     */
    private static function isAssociativeArray(array $arr) {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}

