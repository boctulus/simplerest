<?php

namespace devdojo\calculator;

use simplerest\core\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\libs\Factory;
use simplerest\libs\DB;

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

