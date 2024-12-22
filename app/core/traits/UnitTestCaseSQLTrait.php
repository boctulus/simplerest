<?php

namespace simplerest\core\traits;

use simplerest\core\libs\DB;

trait UnitTestCaseSQLTrait
{
    // Normaliza SQL para que las comparaciones (asserts) no fallen por tonterias
    function normalizeSQL($sql, $default_datetime = '2000-01-01 12:00:00') {
        // Eliminar punto y coma final
        $sql = rtrim($sql, ';');
        
        // Eliminar backticks y comillas dobles en nombres de tablas/campos
        $sql = str_replace(['`', '"'], '', $sql);
        
        // Normalizar espacios después de comas
        $sql = preg_replace('/\s*,\s*/', ',', $sql);
        
        // Normalizar espacios múltiples a uno solo
        $sql = preg_replace('/\s+/', ' ', $sql);

        // Normaliza fechas
        if ($default_datetime !== false) {
            // Reemplazar fechas en campos conocidos
            $sql = preg_replace(
                "/(created_at|updated_at|deleted_at)\s*=\s*'[\d\-\s\:]+'/" ,
                "$1 = '$default_datetime'",
                $sql
            );
        }    

        // Eliminar espacios antes y después
        $sql = trim($sql);
        
        return $sql;
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
