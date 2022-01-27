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

    function index()
    {
        while (true)
        {
            // dequeue
            DB::beginTransaction();

            try {

                $job_row = DB::table('jobs')
                ->orderBy(['id' => 'ASC'])
                ->first();  
                
                if (empty($job_row)){
                    DB::rollback();

                    // para no pegarle continuamente a la DB
                    sleep(1);

                    continue;
                }

                $id = $job_row['id'];

                DB::table('jobs')
                ->find($id)
                ->delete();
                
                DB::commit(); 

            }catch(\Exception $e){
                DB::rollback();
                Files::logger("Worker has finished with error :" . $e->getMessage());
            }	

            //d($job_row);

            $job    = unserialize($job_row['object']);
            $params = unserialize($job_row['params']);

            $job->run(...$params);
        }

        d("Quit");
    }
}

