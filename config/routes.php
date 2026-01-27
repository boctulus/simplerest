<?php

use Boctulus\FriendlyposWeb\Controllers\OpenFacturaController;
use Boctulus\Simplerest\Controllers\DumbController;
use Boctulus\Simplerest\Core\Handlers\ApiHandler;
use Boctulus\Simplerest\Core\Libs\Logger;
use Boctulus\Simplerest\Core\Libs\Mail;
use Boctulus\Simplerest\Core\Libs\SiteMap;
use Boctulus\Simplerest\Core\Libs\System;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\WebRouter;
use Boctulus\Simplerest\Libs\Debug;
use Boctulus\Simplerest\Modules\TaxCalc\TaxCalc;
use Boctulus\Simplerest\Modules\Typeform\Typeform;

$route = WebRouter::getInstance();

WebRouter::get('sitemap.xml', function(){
	$sitemap = new SiteMap();
	$sitemap->fromRouter(['sitemap.xml', 'admin/*']);	
	$sitemap->excludePlaceholdedRoutes(); //
	$xml     = $sitemap->generateXML();

	header('Content-Type: application/xml');
	return $xml;
});

WebRouter::any('health', function () {
    return ['ok' => true];
});

// ...

WebRouter::get('api-test/cors',  'CorsTesterController@get');
WebRouter::post('api-test/cors',  'CorsTesterController@post');
// WebRouter::put('api-test/cors',  'CorsTesterController');
// WebRouter::delete('api-test/cors',  'CorsTesterController');

// Si intentara con otro verbo fallaria --ok
WebRouter::delete('api-test/r1',  'DumbController@test_r1');

// Rutas con parametros --ok
WebRouter::get('user/{id}', 'DumbController@test_r2')->where(['id' => '[0-9]+']);

// Inclusion de namespace
WebRouter::get('increment/{num}', 'Boctulus\Simplerest\Controllers\folder\SomeController@inc2')->where(['num' => '[0-9]+']);

WebRouter::get('increment_a/{num}', 'Boctulus\Simplerest\Controllers\folder\SomeController@inc')->where(['num' => '[0-9]+']);
WebRouter::get('increment_b/{num}', 'Boctulus\Simplerest\Controllers\folder\SomeController@inc3')->where(['num' => '[0-9]+']);

// Grupos --ok
WebRouter::group('admin', function() {	
    WebRouter::get('dashboard', 'DumbController@dashboard');
    WebRouter::get('settings', 'DumbController@settings');
});

// Funciones anonimas --ok
WebRouter::get('git/pull', function(){
	dd(
		System::execAtRoot("git pull")
	);
});


// Rutas desde array --ok
WebRouter::fromArray([ 
    // rutas 

    'GET:/speed_check' => 'DumbController@speedcheck',
    'POST:/products/prices'  => 'DumbController@post_price',
	# '/some/route' => 'DumbController@some_route',
    // ...
]);

/*
	Soporte de Middlewares en WebRouter --ok
*/
WebRouter::get('test-mid',  'TestController@mid');

WebRouter::get("tax_calc", function() use ($route) {
	set_template('templates/tpl_bt3.php');          
	render(TaxCalc::get());
});


WebRouter::get("typeform", function() use ($route) {
	set_template('templates/tpl_bt3.php');          
	render(Typeform::get());
});

WebRouter::post("typeform/process", function() use ($route) {
	render(Typeform::process());
});


// Firebase Test Module Routes
WebRouter::get("firebase-test", function() use ($route) {
	set_template('templates/tpl_bt3.php');
	$module = new \Boctulus\Simplerest\Modules\FirebaseTest\FirebaseTest();
	render($module->index());
});

WebRouter::get("firebase-test/config", function() use ($route) {
	set_template('templates/tpl_bt3.php');
	$module = new \Boctulus\Simplerest\Modules\FirebaseTest\FirebaseTest();
	render($module->show_config());
});

WebRouter::get("firebase-test/firestore", function() use ($route) {
	set_template('templates/tpl_bt3.php');
	$module = new \Boctulus\Simplerest\Modules\FirebaseTest\FirebaseTest();
	render($module->test_firestore());
});

WebRouter::get("firebase-test/auth", function() use ($route) {
	set_template('templates/tpl_bt3.php');
	$module = new \Boctulus\Simplerest\Modules\FirebaseTest\FirebaseTest();
	render($module->test_auth());
});

WebRouter::post("firebase-test/auth", function() use ($route) {
	set_template('templates/tpl_bt3.php');
	$module = new \Boctulus\Simplerest\Modules\FirebaseTest\FirebaseTest();
	render($module->test_auth());
});

WebRouter::get("firebase-test/realtime-db", function() use ($route) {
	set_template('templates/tpl_bt3.php');
	$module = new \Boctulus\Simplerest\Modules\FirebaseTest\FirebaseTest();
	render($module->test_realtime_db());
});

WebRouter::get("firebase-test/storage", function() use ($route) {
	set_template('templates/tpl_bt3.php');
	$module = new \Boctulus\Simplerest\Modules\FirebaseTest\FirebaseTest();
	render($module->test_storage());
});

WebRouter::post("firebase-test/storage", function() use ($route) {
	set_template('templates/tpl_bt3.php');
	$module = new \Boctulus\Simplerest\Modules\FirebaseTest\FirebaseTest();
	render($module->test_storage());
});

// Components System Routes
// WebRouter::get('components', 'ComponentsController@index');
// WebRouter::get('components/examples', 'ComponentsController@examples');

WebRouter::get('mem', function(){
	dd(System::getMemoryLimit(), 'Memory limit');
	dd(System::getMemoryUsage(), 'Memory usage');
	dd(System::getMemoryUsage(true), 'Memory usage (real)');

	dd(System::getMemoryPeakUsage(), 'Memory peak usage');
	dd(System::getMemoryPeakUsage(true), 'Memory peak usage (real)');
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

// Grouped admin utilities
WebRouter::group('admin', function() {
	WebRouter::get('migrate', function(){
		chdir(ROOT_PATH);

		exec("php com migrations migrate", $output_lines, $res_code);

		foreach($output_lines as $output_line){
			dd($output_line);
		}

		dd($res_code, 'RES_CODE');
	});

	WebRouter::get('test_smtp', function(){
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
	Wildcard route examples
*/

// Example 1: Catch-all route for file paths
WebRouter::get('files/*', function($path) {
    return [
        'endpoint' => 'files',
        'requested_path' => $path,
        'message' => 'Wildcard route for files endpoint captured the path'
    ];
});

// Example 2: User route with wildcard for nested resources
WebRouter::get('user/{id}/*', function($id, $resource) {
    return [
        'user_id' => $id,
        'resource' => $resource,
        'message' => 'User wildcard route accessed'
    ];
});


// WebRouter::any(
//     'api/{version}/{entity}/*',
// function ($version, $entity, $rest = null) {
//         // ...
//     }
// );

// WebRouter::mount('api', ApiHandler::class);
