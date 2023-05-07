<?php

use simplerest\core\Route;
use simplerest\libs\Debug;
use simplerest\core\libs\Mail;
use simplerest\core\libs\Files;

$route = Route::getInstance();

/*
	Penosamente no hay para la consola
*/
Route::get('revolut', function(){
	return "http://revolut.me/boctulus";
});

Route::post('api/v1/save_demo', function(){
	$req = request()->as_array()->getBody();
	
	Files::dump($req);
});

Route::get('api/v1/cool',  'DumbAuthController@super_cool_action');

/*
	Actualmente el orden es importante...... 
	... que debe corregirse.

	Esta obligandose a ir de lo especifico a lo general
*/

Route::get('admin/migrate', function(){
	chdir(ROOT_PATH);
	
	exec("php com migrations migrate", $output_lines, $res_code);
	
	foreach($output_lines as $output_line){
		d($output_line);
	}

	dd($res_code, 'RES_CODE');
});


Route::get('admin/test_smtp', function(){
	Mail::debug(4);
	Mail::setMailer('ovh');

	Mail::send(
		[
			'email' => 'boctulus@gmail.com',
			'name' => 'Pablo'
		],
		'Pruebita 001JRBX',
		'Hola!<p/>Esto es una más <b>prueba</b> con el server de Planex<p/>Chau'
	);

	d(Mail::errors(), 'Error');
	d(Mail::status(), 'Status');
});


Route::get('admin/una-pagina', function(){
	$content = "Pagina (de acceso restringido)";
	render($content);
});

Route::get('admin/pagina-dos', function(){
	$content = "Pagina dos (de acceso restringido)";
	render($content);
});


//Route::get('admin', function(){
//	$content = "Panel de Admin";
//	render($content);
//});



/*
	Si hubiera rutas de consola podria crear comandos y ejecutarlos asi:

	php com get_path_public

	<-- que devolveria PUBLIC_PATH

	Actualmente necesitaria crear un controlador y el comando ser'ia mas largo innecesariamente
*/

/*
	See routes.php.example
*/