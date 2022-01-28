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
    protected $name;

    function __construct(string $name = 'default') {
        $this->name = $name;
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
            'queue'  => $this->name,
            'object' => serialize($job),
            'params' => serialize($params)
        ]);
    }

    public function workerFactory(int $workers = 1){  
        for ($i=0; $i<$workers; $i++){
            System::runInBackground("php com worker listen {$this->name}");
        }
    }

}

