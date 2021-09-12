<?php

use simplerest\core\Route;
use simplerest\libs\Debug;

$route = Route::getInstance();

Route::get('hello', function(){
	return "Hellooooo";
});

/*
	See routes.php.example
*/