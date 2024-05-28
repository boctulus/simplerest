<?php

namespace simplerest\controllers;

use simplerest\core\libs\Logger;
use simplerest\core\controllers\Controller;

class InflatorController extends Controller
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
            
            dd($job_row, 'job');
        
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
                dd("Ejecutando job con job_id = $job_id");

                if (!is_object($job_ob) || !method_exists($job_ob, 'run')) {
                    throw new \Exception("Job::run() with job_id = $job_id is not callable");
                }

                $job_ob->run(...$params);
    
                $ok = (bool) table('jobs')
                ->where(['id' => $job_id])
                ->delete();

                if ($ok){
                    dd("Eliminado job con job_id = $job_id");
                } else {
                    dd("Fallo al eliminar job con job_id = $job_id");
                }

                $ok = (bool) table('job_process')
                ->where(['job_id' => $job_id])
                ->delete();

                if ($ok){
                    dd("Eliminado registro de processs para job_id = $job_id");
                } else {
                    dd("Fallo al eliminar registro de processs para job_id = $job_id");
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
            dd("Job id: [$job_id] ". $e->getMessage());
        }

        
    }
}

