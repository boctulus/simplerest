<?php

namespace devdojo\calculator;

use simplerest\core\controllers\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

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

