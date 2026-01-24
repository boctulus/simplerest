<?php

namespace Boctulus\Simplerest\Core\Traits;

use Boctulus\Simplerest\Core\Libs\DB;

trait UnitTestCaseSQLTrait
{
    function normalizeSQL($sql, $default_datetime = '2000-01-01 12:00:00') {
        // Eliminar punto y coma final
        $sql = rtrim($sql, ';');
        
        // Ordenar campos en SELECT
        if (preg_match('/^SELECT\s+(.+?)\s+FROM/i', $sql, $matches)) {
            $select_part = $matches[1];
            
            // Si es DISTINCT, preservarlo
            $distinct = '';
            if (stripos($select_part, 'DISTINCT') === 0) {
                $distinct = 'DISTINCT ';
                $select_part = preg_replace('/^DISTINCT\s+/i', '', $select_part);
            }
            
            // Separar campos
            $fields = array_map('trim', explode(',', $select_part));
            
            // Ordenar preservando aliases y expresiones
            usort($fields, function($a, $b) {
                // Extraer nombre base del campo (antes de AS si existe)
                $a_base = trim(preg_replace('/\s+as\s+.+$/i', '', $a));
                $b_base = trim(preg_replace('/\s+as\s+.+$/i', '', $b));
                return strcasecmp($a_base, $b_base);
            });
            
            // Reconstruir la parte SELECT
            $new_select = "SELECT $distinct" . implode(',', $fields);
            $sql = preg_replace('/^SELECT\s+.+?\s+FROM/i', "$new_select FROM", $sql);
        }
        
        // Eliminar backticks y comillas dobles
        $sql = str_replace(['`', '"'], '', $sql);
        
        // Normalizar espacios después de comas
        $sql = preg_replace('/\s*,\s*/', ',', $sql);
        
        // Normalizar espacios múltiples
        $sql = preg_replace('/\s+/', ' ', $sql);
    
        // Normalizar fechas
        if ($default_datetime !== false) {
            $sql = preg_replace(
                "/(created_at|updated_at|deleted_at)\s*=\s*'[\d\-\s\:]+'/" ,
                "$1 = '$default_datetime'",
                $sql
            );
        }    

         // Normalizar fechas en condiciones de igualdad (UPDATE/WHERE)
        $sql = preg_replace(
            "/(created_at|updated_at|deleted_at)\s*=\s*'[\d\-\s\:]+'/" ,
            "$1 = '$default_datetime'",
            $sql
        );

        // Normalizar fechas en VALUES (INSERT)
        $sql = preg_replace(
            "/VALUES\s*\((.*?)'[\d\-\s\:]+'\s*(.*?)\)/i",
            "VALUES ($1'$default_datetime'$2)",
            $sql
        );
    
        return trim($sql);
    }

    // Esta funcion *reemplaza* a la nativa assertEquals() para evaluar SQL (y podria ir a clase derivada de TestCase)
    protected function assertSQLEquals($expected, $actual, string $message = '')
    {
        $this->assertEquals(
            $this->normalizeSQL($expected),
            $this->normalizeSQL($actual),
            $message
        );
    }

    function limit($limit, $offset = 0, $driver = null){
        $ol = [$limit !== null, !empty($offset)]; 

        if ($ol[0] || $ol[1]){
            switch ($driver ?? DB::driver()){
                case 'mysql':
                case 'sqlite':
                    switch($ol){
                        case [true, true]:
                            return "LIMIT $offset, $limit";
                        case [true, false]:
                            return "LIMIT $limit";
                        case [false, true]:
                            return "LIMIT $offset, 18446744073709551615";
                    } 
                    break;    
                case 'pgsql': 
                    switch($ol){
                        case [true, true]:
                            return "OFFSET $offset LIMIT $limit";
                        case [true, false]:
                            return "LIMIT $limit";
                        case [false, true]:
                            return "OFFSET $offset";
                    } 
                    break;      
                default: 
                    throw new \InvalidArgumentException("Invalid driver");	                       
            }
        }
    }

    function rand_fn($driver = null){
        $driver = $driver ?? DB::driver();

        switch ($driver){
            case 'mysql':
            case 'sqlite':
                return 'RAND()';
            case 'pgsql':
                return 'RANDOM()';
            default: 
                throw new \InvalidArgumentException("Invalid driver");	
        }
    }
}
