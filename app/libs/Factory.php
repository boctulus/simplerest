<?php 

namespace simplerest\libs;

use simplerest\libs\Config;
use simplerest\controllers\MyAuthController;

class Factory {
	static function auth(){
		static $instance;

		if ($instance == null){
			$instance = new MyAuthController();
		}

        return $instance;
	}

	static function response() {
		return \simplerest\core\Response::getInstance();
	}

	static function request() {
		return \simplerest\core\Request::getInstance();
	}

	static function validador(){
		static $instance;

		if ($instance == null){
			$instance = new \simplerest\libs\Validator();
		}

        return $instance;
	}

	static function acl(){
		static $instance;

		if ($instance == null){
			$instance = include CONFIG_PATH . 'acl.php';
		}

        return $instance;
	}

	static function config(){
		static $instance;

		if ($instance == null){
			$instance = new Config();
		}

        return $instance;
	}
}
