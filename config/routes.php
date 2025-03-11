<?php

use simplerest\core\WebRouter;
use simplerest\libs\Debug;
use simplerest\core\libs\Mail;
use simplerest\core\libs\System;
use simplerest\core\libs\Logger;
use simplerest\shortcodes\tax_calc\TaxCalcShortcode;

$route = WebRouter::getInstance();

WebRouter::get('api-test/cors',  'CorsTesterController@get');
WebRouter::post('api-test/cors',  'CorsTesterController@post');
// WebRouter::put('api-test/cors',  'CorsTesterController');
// WebRouter::delete('api-test/cors',  'CorsTesterController');

// Si intentara con otro verbo fallaria --ok
WebRouter::delete('api-test/r1',  'DumbController@test_r1');

// Rutas con parametros --ok
WebRouter::get('user/{id}', 'DumbController@test_r2')->where(['id' => '[0-9]+']);

// Grupos --ok
WebRouter::group('admin', function() {
    WebRouter::get('dashboard', 'DumbController@dashboard');
    WebRouter::get('settings', 'DumbController@settings');
});

// Funciones anonimas --ok
WebRouter::get('testx', function(){
	// echo '<pre>';
	// print_r(get_loaded_extensions());
	// echo '</pre>';	

	if ( false === function_exists('gettext') ) {
		echo "You do not have the gettext library installed with PHP.";
		exit(1);
	}
});

// Rutas desde array --ok
WebRouter::fromArray([ 
    // rutas 

    'GET:/speed_check' => 'DumbController@speedcheck',
    'POST:/products/prices'  => 'DumbController@post_price',
	# '/some/route' => 'DumbController@some_route',
    // ...
]);

WebRouter::get("tax_calc", function() use ($route) {
	set_template('templates/tpl_bt3.php');          
	render(TaxCalcShortcode::get());
});

WebRouter::get('mem', function(){
	dd(System::getMemoryLimit(), 'Memory limit');
	dd(System::getMemoryUsage(), 'Memory usage');
	dd(System::getMemoryUsage(true), 'Memory usage (real)');

	dd(System::getMemoryPeakUsage(), 'Memory peak usage');
	dd(System::getMemoryPeakUsage(true), 'Memory peak usage (real)');
});

WebRouter::get('git/pull', function(){
	dd(
		System::execAtRoot("git pull")
	);
});

/*
	Penosamente no hay para la consola
*/
WebRouter::get('revolut', function(){
	return "http://revolut.me/boctulus";
});

WebRouter::post('api/v1/save_demo', function(){
	$req = request()->as_array()->getBody();
	
	Logger::dd($req);
});

WebRouter::get('api/v1/cool',  'DumbAuthController@super_cool_action');

/*
	Actualmente el orden es importante...... 
	... que debe corregirse.

	Esta obligandose a ir de lo especifico a lo general
*/

WebRouter::get('admin/migrate', function(){
	chdir(ROOT_PATH);
	
	exec("php com migrations migrate", $output_lines, $res_code);
	
	foreach($output_lines as $output_line){
		dd($output_line);
	}

	dd($res_code, 'RES_CODE');
});


WebRouter::get('admin/test_smtp', function(){
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


WebRouter::get('admin/una-pagina', function(){
	$content = "Pagina (de acceso restringido)";
	render($content);
});

WebRouter::get('admin/pagina-dos', function(){
	$content = "Pagina dos (de acceso restringido)";
	render($content);
});


//WebRouter::get('admin', function(){
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