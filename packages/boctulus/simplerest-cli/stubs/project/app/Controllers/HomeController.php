<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\WebController;

class HomeController extends WebController
{
	# /home/
	function index(){
		$this->__view('home.php');
	}
}
	