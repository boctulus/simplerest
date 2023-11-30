<?php

namespace boctulus\SW\controllers;

use boctulus\SW\core\libs\Date;
use boctulus\SW\core\libs\Logger;
use boctulus\SW\core\libs\CronJob;
use boctulus\SW\core\libs\Strings;
use simplerest\controllers\MyController;

class InflatorController extends MyController
{
    /*
        Deserializa un objeto y le da vida
    */
    public function inflate(int $job_id)
    {   
        try {

            $job_row = table('jobs')
            ->where(['id' => $job_id])
            ->first();  
    
            if (empty($job_row)){
                throw new \Exception("Job inexistente para job_id = $job_id");
            }
    
            /*
                Array
                (
                    [id] => 1696
                    [queue] => procesar_cat
                    [object] => O:46:"boctulus\SW\background\tasks\ProcesarCategoria":0:{}
                    [params] => a:1:{i:0;s:171:"a:3:{s:4:"slug";s:39:"/bambino/abbigliamento-bimba/pantalone/";s:4:"name";s:9:"Pantaloni";s:4:"link";s:61:"https://www.giglio.com/bambino/abbigliamento-bimba/pantalone/";}";}
                    [created_at] => 0000-00-00 00:00:00
                )
            */
            
            error_log($job_row);
        
            $sr_job_ob = $job_row['object'] ?? null;
            $sr_params = $job_row['params'] ?? null;
    
            if ($sr_job_ob === null){
                throw new \Exception("Job object vacio");
            }
    
            if ($sr_params === null){
                throw new \Exception("Job params vacio");
            }
            
            $job_ob = unserialize($sr_job_ob);
            $params = unserialize($sr_params);
    
            // Parseando $serialized_object es posible obtener el nombre de la clase para enviarlo a logs asi
    
            try {
                error_log("Ejecutando job con job_id = $job_id");

                $job_ob->run(...$params);
    
                $ok = (bool) table('jobs')
                ->where(['id' => $job_id])
                ->delete();

                if ($ok){
                    error_log("Eliminado job con job_id = $job_id");
                } else {
                    error_log("Fallo al eliminar job con job_id = $job_id");
                }

                $ok = (bool) table('job_process')
                ->where(['job_id' => $job_id])
                ->delete();

                if ($ok){
                    error_log("Eliminado registro de processs para job_id = $job_id");
                } else {
                    error_log("Fallo al eliminar registro de processs para job_id = $job_id");
                }                
    
            } catch (\Exception $e){
    
                // $ok = (bool) table('jobs')
                // ->where(['id' => $job_id])
                // ->update([
                //     'taken' => 0
                // ]);
    
                throw $e;
            }  

        } catch (\Exception $e) {
            error_log("Job id: [$job_id] ". $e->getMessage());
        }

        
    }
}

