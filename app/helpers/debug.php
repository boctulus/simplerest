<?php

use simplerest\libs\Debug;

function dd(...$opt){
    return Debug::dd(...$opt);	
}		

function here(){
    dd('HERE !');
}
