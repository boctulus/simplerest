<?php

namespace simplerest\controllers;

use simplerest\core\controllers\WebController;

class HomeController extends WebController
{
	# /home/
	function index(){
		$this->__view('home.php');
	}
}
	