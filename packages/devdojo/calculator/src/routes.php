<?php

use simplerest\core\WebRouter;

WebRouter::get('calculator', function(){
	echo 'Hello from the calculator package!';
});

/*
 Para trabajar con el controller dentro del paquete
*/

// http://az.lan/add/60/7
WebRouter::get('add', 'devdojo\calculator\CalculatorController@add');

// http://az.lan/subtract/60/7
WebRouter::get('subtract', 'devdojo\calculator\CalculatorController@subtract');