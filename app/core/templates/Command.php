<?php

use simplerest\core\interfaces\ICommand;

class __NAME__Command implements ICommand 
{
	/*
		Draft of handle method
	*/
	function handle($args) {
		// if (count($args) === 0){
		//	$this->help();
		//	return;
		// }

		$method = array_shift($args);

		if (!is_callable([$this, $method])){
			dd("Method not found for ". __CLASS__ . "::$method");
			exit;
		}

		call_user_func([$this, $method], ...$args);
	}
}

