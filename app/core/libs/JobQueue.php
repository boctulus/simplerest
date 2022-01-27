<?php declare(strict_types=1);

namespace simplerest\core\libs;

use simplerest\core\libs\System;

/*
    Instancias de JobQueue (y no de sus jobs) deben correrse en "segundo plano"
    a fin de que sean los jobs en una cola sean síncronos.

    El procesamiento parelelo se da a nivel de workers
*/
class JobQueue
{
    static protected $workers = 1;

    function __construct() {
        $this->job_queue = new \SplQueue();
    }

    public function dispatch(string $job_class, ...$params){
        if (!class_exists($job_class)){
            throw new \Exception ("Class '$job_class' doesn't exist");
        } 

        if (!$job_class::isActive()){
            throw new \Exception("Job is disabled");
        }

        $job = new $job_class();

        if (! $job instanceof Task){
            throw new \Exception ("Class '$job_class' should be instance of Task");
        }

        DB::getDefaultConnection();

        // enqueue
        DB::table('jobs')
        ->insert([
            'object' => serialize($job),
            'params' => serialize($params)
        ]);
    }

    /*
        Funciona con un solo worker

        Habría que ver si con generators se podría hacer algo.
    */
    public function exec(){  
        // La cola debería estar en una base de datos...... y pues encolar es ingresar un registro
        // que podría ser el objeto serializado o el nombre del job + params   
        
        // Así cuando un Worker termina de procesar un Job solo debe revisar en la DB si hay jobs pendientes
        // ... y los procesa
        
        while (!$this->job_queue->isEmpty()){
            $arr    = $this->job_queue->dequeue();
            $job    = $arr['object'];
            $params = $arr['params'];

            $ret = $job->run(...$params);
        }
    }

}

