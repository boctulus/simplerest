<?php

use simplerest\core\Route;
use simplerest\libs\Debug;

$route = Route::getInstance();

Route::get('hello', function(){
	return "Hellooooo";
});

Route::get('api/v1/cool',  'DumbAuthController@super_cool_action');

/*
	Actualmente el orden es importante...... 
	... que debe corregirse.

	Esta obligandose a ir de lo especifico a lo general
*/

Route::get('admin/una-pagina', function(){
	$content = "Pagina (de acceso restringido)";
	render($content);
});

Route::get('admin/pagina-dos', function(){
	$content = "Pagina dos (de acceso restringido)";
	render($content);
});

Route::get('admin', function(){
	$content = "Panel de Admin";
	render($content);
});


/*
	Si hubiera rutas de consola podria crear comandos y ejecutarlos asi:

	php com get_path_public

	<-- que devolveria PUBLIC_PATH

	Actualmente necesitaria crear un controlador y el comando ser'ia mas largo innecesariamente
*/

/*
	See routes.php.example
*/