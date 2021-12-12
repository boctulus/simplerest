<?php

use simplerest\core\Route;

Route::get('calculator', function(){
	echo 'Hello from the calculator package!';
});

/*
 Para trabajar con el controller dentro del paquete
*/

// http://az.lan/add/60/7
Route::get('add', 'devdojo\calculator\CalculatorController@add');

// http://az.lan/subtract/60/7
Route::get('subtract', 'devdojo\calculator\CalculatorController@subtract');