<?php

use simplerest\core\Route;
use simplerest\libs\Debug;
use simplerest\core\libs\Mail;
use simplerest\core\libs\System;
use simplerest\core\libs\Logger;
use simplerest\shortcodes\tax_calc\TaxCalcShortcode;

$route = Route::getInstance();

Route::get("tax_calc", function() use ($route) {
	set_template('templates/tpl_bt3.php');          
	render(TaxCalcShortcode::get());
});

Route::get('mem', function(){
	dd(System::getMemoryLimit(), 'Memory limit');
	dd(System::getMemoryUsage(), 'Memory usage');
	dd(System::getMemoryUsage(true), 'Memory usage (real)');

	dd(System::getMemoryPeakUsage(), 'Memory peak usage');
	dd(System::getMemoryPeakUsage(true), 'Memory peak usage (real)');
});

Route::get('git/pull', function(){
	dd(
		System::execAtRoot("git pull")
	);
});

/*
	Penosamente no hay para la consola
*/
Route::get('revolut', function(){
	return "http://revolut.me/boctulus";
});

Route::post('api/v1/save_demo', function(){
	$req = request()->as_array()->getBody();
	
	Logger::dd($req);
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
		dd($output_line);
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

	dd(Mail::errors(), 'Error');
	dd(Mail::status(), 'Status');
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