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
    protected \SplQueue $job_queue;

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

        $this->job_queue->enqueue([
            'object' => $job,
            'params' => $params
        ]);
    }

    public function exec(){
        $arr    = $this->job_queue->dequeue();
        $job    = $arr['object'];
        $params = $arr['params'];

        return $job->run(...$params);
    }

}

