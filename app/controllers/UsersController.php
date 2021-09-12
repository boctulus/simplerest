<?php

namespace simplerest\controllers;

class UsersController extends MyController
{
	function index(){
		return view('users.php');
	}
}
	