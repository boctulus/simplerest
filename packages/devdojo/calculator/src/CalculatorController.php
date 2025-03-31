<?php

namespace Devdojo\Calculator;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\DB;

class CalculatorController extends Controller
{
    public function add($a, $b){
        echo "Res: ";
    	echo $a + $b;
    }

    public function subtract($a, $b){
        echo "Res: ";
    	echo $a - $b;
    }
}

