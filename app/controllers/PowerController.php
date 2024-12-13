<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\traits\TimeExecutionTrait;
use simplerest\libs\PowerConsumption;

class PowerController extends Controller
{
    function __construct() { parent::__construct(); }

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
}

