<?php

namespace simplerest\controllers;

class HomeController extends MyController
{
	# /home/
	function index(){
		// ...

		$this->view('home.php');
	}

	# /home/show
	function show(){
		$this->view('test_x_dsi.php', [
			'pruebas' => [
				[ 'name' => 'Como se sirven las vistas', 'date' => '22-Set' ],
				[ 'name' => 'Maestro detalle con HATEOAS', 'date' => '22-Set' ]
			],
			'title' => 'DSI - pruebas' /* ... */
		]);
	}
}
	