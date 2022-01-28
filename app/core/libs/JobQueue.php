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

    public function addWorkers(int $workers = 1){  
        for ($i=0; $i<$workers; $i++){
            $pid = System::runInBackground("php com worker listen {$this->name}");

            DB::getDefaultConnection();
            
            DB::table('background_workers')
            ->insert([
                'queue' => $this->name,
                'pid'   => $pid
            ]);
        }
    }

    /*
        Mata de forma violenta a los workers y por ende a los jobs que está ejecutando. Sería como un "--force"

        Una manera más gentil de frenar (pausar) sería mantener una tabla `queues` donde cada cola pueda
        tener un estado (is_active) y que los workers antes de tomar un nuevo job verifiquen que la cola
        sigue activa y sino que terminen.
    */
    static function stop(?string $queue = null){
        DB::getDefaultConnection();

        $pids = DB::table('background_workers')
        ->when(!is_null($queue), function($q) use ($queue){
            $q->where(['queue' => $queue]);
        })
        ->pluck('pid');

        if (empty($pids)){
            return;
        }

        foreach ($pids as $pid){
            $exit_code = shell_exec("kill $pid 2>/dev/null && echo $?");

            if ($exit_code == 0){
                DB::table('background_workers')
                ->where(['pid' => $pid])
                ->delete();
            }
        }

        // Borro cualquier otro proceso que haya quedado escrito en la tabla

        DB::table('background_workers')
        ->when(!is_null($queue), function($q) use ($queue){
            $q->where(['queue' => $queue]);
        })
        ->delete();
    }

}

