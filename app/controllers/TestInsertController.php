<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\libs\VarDump;
use simplerest\core\Model;
use simplerest\core\traits\SubResourceHandler;

class TestInsertController extends Controller
{
    /*
        https://grok.com/chat/102388e4-47d3-4860-a889-f7b015e9ab77
    */
    function test_insertion_order(){
        $m = new Model();

        $tables = ['tags', 'courses', 'categories', 'users'];
        $tenant_id = 'edu';

        DB::getConnection($tenant_id);
        $order = $m->getInsertionOrder($tables);

        dd($order);
    }

    function test_insertion_order_2(){
        $m = new Model();

        $tables = ['tags', 'courses', 'categories', 'users']; // revisar la lista
        $tenant_id = 'complex01';

        DB::getConnection($tenant_id);

        dd(DB::getTableNames());
        exit;

        $order = $m->getInsertionOrder($tables);

        dd($order);
    }

    function test_insert_struct(){
        DB::getConnection('edu');

        $data = [
            'title' => 'Mathematics for Phisicists',
            'categories' => [
                'name' => 'Mathematics'
            ],
            'users' => [
                ['name' => 'Bob Smith', 'role' => 'professor'],
                ['name' => 'Diana White', 'role' => 'student']
            ]
        ];        
        
        $course_id = DB::table('courses')
            ->connectTo(['categories', 'users'])
            //->dontExec()             
            ->insertStruct($data); 

        dd(
            DB::getLog(), 'SQL' 
        );

        // dd($course_id);
    }
}

