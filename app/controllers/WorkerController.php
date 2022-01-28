<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Files;
use simplerest\core\libs\DB;

class WorkerController extends MyController
{
    function __construct()
    {
        parent::__construct();        
    }

    function listen(?string $queue = null)
    {
        while (true)
        {
            // dequeue
           
            $job_row = DB::table('jobs')
            ->when(!is_null($queue), function($q) use ($queue) {
                $q->where(['queue' => $queue]);
            })
            ->orderBy(['id' => 'ASC'])
            ->first();  
            
            if (empty($job_row)){
                // para no pegarle continuamente a la DB
                sleep(1);

                continue;
            }

            $id = $job_row['id'];

            $ok = (bool) DB::table('jobs')
            ->find($id)
            ->delete();
                
            if (!$ok){
               continue; 
            }

            //d($job_row);

            $job    = unserialize($job_row['object']);
            $params = unserialize($job_row['params']);

            $job->run(...$params);
        }

        d("Quit");
    }
}

