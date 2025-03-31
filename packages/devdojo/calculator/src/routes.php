<?php

use Boctulus\Simplerest\Core\WebRouter;

WebRouter::get('calculator', function(){
	echo 'Hello from the calculator package!';
});

/*
 Para trabajar con el controller dentro del paquete
*/

// http://az.lan/add/60/7
WebRouter::get('add', 'Devdojo\Calculator\CalculatorController@add');

// http://az.lan/subtract/60/7
WebRouter::get('subtract', 'Devdojo\Calculator\CalculatorController@subtract');