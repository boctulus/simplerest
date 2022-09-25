<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;

class HomeController extends Controller
{
	# /home/
	function index(){
		$this->__view('home.php');
	}
}
	