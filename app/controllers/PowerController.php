<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Libs\PowerConsumption;
use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;
use Boctulus\Simplerest\Core\Controllers\ConsoleController;

class PowerController extends ConsoleController
{
    public function __construct()
    {
        parent::__construct();
        $this->setOutputFormat('test');
    }

    function index(){
        return $this->list();
    }

    function calc($currentReading = null)
    {
        if ($currentReading === null){
            return PowerConsumption::report();
        }

        if ($currentReading <= 0) {
            throw new \InvalidArgumentException('Current reading must be greater than zero.');
        }

        // Si el valor pasado es inferior al ultimo almacenado tambien lanzar

        $save = $_GET['save'] ?? false;

        $result = PowerConsumption::calculate($currentReading, (bool) $save);                   

        return $result;
    }

    function report(){
        return PowerConsumption::report();
    }

    function save($currentReading){
        $_GET['save'] = true;
        return $this->calc($currentReading);
    }

    function list($days = 5){
        $res = [];
        $rows = PowerConsumption::listReadings($days);
        $rows = array_reverse($rows); // Invertimos primero para procesar cronológicamente
        $prevReading = null;
    
        foreach ($rows as $row){
            $increment = null;
            if ($prevReading !== null) {
                $increment = $row['reading'] - $prevReading; // Lectura actual - anterior
            }
            $res[$row['created_at']] = [
                'reading' => $row['reading'],
                'increment' => $increment
            ];
            $prevReading = $row['reading'];
        }
    
        return $res;
    }
}

