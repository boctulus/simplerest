<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class WorkerController extends MyController
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {
        // dequeue
        DB::beginTransaction();

        try {

            $job_row = DB::table('jobs')
            ->orderBy(['id' => 'ASC'])
            ->firstOrFail();  
            
            $id = $job_row['id'];

            DB::table('jobs')
            ->find($id)
            ->delete();
            
            DB::commit(); 

        }catch(\Exception $e){
            DB::rollback();
            dd($e->getMessage(), "Error en transacción");
        }	

        d($job_row);

        $job_object = $job_row['object'];
        $job_params = $job_row['params'];
                   
    }
}

