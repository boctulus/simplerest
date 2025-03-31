<?php

namespace Boctulus\Simplerest\Modules\csv_importer\libs;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Logger;
use Boctulus\Simplerest\Core\Interfaces\IProcessable;

class Importer implements IProcessable
{
    static $path; 

    static function count() : int {
        return Files::countLines(static::$path);
    }

    static function run($query_ids = null, $offset = null, $limit = null)
    {    
        if (empty(static::$path)){
            throw new \Exception("Especifique el filename");
        }

        Logger::dd("Running ...");

        if (empty(trim($query_ids))){
            $query_ids = [];
        } else {
            if (!is_array($query_ids)){
                $query_ids = explode(',', trim($query_ids));
            }
        }

        global $total, $processed, $updated; 

        $total     = 0;
        $processed = 0;
        $updated   = 0;

        $sep = Config::get()['field_separator'] ?? ';';

        Files::processCSV(static::$path, $sep, false, function($p) use ($query_ids) { 
            global $total, $processed, $updated; 

            $total++;

            Logger::dd($p, 'P (por procesar)');

            if (empty($p['Username'])){
                Logger::dd("Producto sin Username: ". var_export($p, true));
                return;
            }
        
            // ...

            $processed++;
        }, null, $offset, $limit);  

        dd([
            'total'     => $total, 
            'processed' => $processed, 
            'updated'   => $updated
        ], 'RESULTADO DEL LOTE |-----------------------');
    }

}