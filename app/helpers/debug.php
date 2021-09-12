<?php

use simplerest\libs\Debug;

function dd($val, $msg = null, callable $precondition_fn = null){
    return Debug::dd($val, $msg, $precondition_fn);	
}		

