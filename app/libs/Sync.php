<?php

namespace simplerest\libs;

use simplerest\core\libs\Files;
use simplerest\core\interfaces\IProcessable;

class Sync implements IProcessable
{
    static $path; 

    static function count() : int {
        return Files::countLines(static::$path);
    }

    static function run($query_sku = null, $offset = null, $limit = null)
    {    
        if (empty(static::$path)){
            throw new \Exception("Especifique el filename");
        }

        dd("Running ...");

        if (empty(trim($query_sku))){
            $query_sku = [];
        } else {
            if (!is_array($query_sku)){
                $query_sku = explode(',', trim($query_sku));
            }
        }

        global $total, $processed, $updated; 

        $total     = 0;
        $processed = 0;
        $updated   = 0;

        $sep = config()['field_separator'] ?? ';';

        Files::processCSV(static::$path, $sep, false, function($p) use ($query_sku) { 
            global $total, $processed, $updated; 

            $total++;

            dd($p, 'P (por procesar)');
        
            $p['sku'] = !isset($p['sku']) ? null : trim($p['sku']);

            // Filtro para pruebas
            if (!empty($query_sku) && !in_array($p['sku'], $query_sku)){
                return;
            }
        
            if (empty($p['sku'])){
                dd("Producto sin SKU: ". var_export($p, true));
                return;
            }
        
            // ...

            $processed++;
        }, [
            'sku',
            'stock',
            'price',
            'sale_price'
        ], $offset, $limit);  

        dd([
            'total'     => $total, 
            'processed' => $processed, 
            'updated'   => $updated
        ], 'RESULTADO DEL LOTE |-----------------------');
    }

}

