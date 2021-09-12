<?php

namespace simplerest\controllers;

class HomeController extends MyController
{
	function index(){
		$this->view('home.php');
	}
}
	