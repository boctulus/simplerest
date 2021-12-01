<?php

use simplerest\core\Route;
use simplerest\libs\Debug;

$route = Route::getInstance();

Route::get('hello', function(){
	return "Hellooooo";
});

Route::get('api/v1/cool',  'DumbAuthController@super_cool_action');


/*
	See routes.php.example
*/