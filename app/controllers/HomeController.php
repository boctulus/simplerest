<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;

class HomeController extends Controller
{
	# /home/
	function index(){
		// ...

		$this->__view('home.php');
	}

	# /home/show
	// function show(){
	// 	view('test_x.php', [
	// 		'pruebas' => [
	// 			[ 'name' => 'Como se sirven las vistas', 'date' => '22-Set' ],
	// 			[ 'name' => 'Maestro detalle con HATEOAS', 'date' => '22-Set' ]
	// 		],
	// 		'title' => 'DSI - pruebas'
	// 	]);
	// }
}
	