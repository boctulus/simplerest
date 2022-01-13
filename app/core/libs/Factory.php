<?php declare(strict_types=1);

namespace simplerest\core\libs;

use simplerest\core\libs\Config;
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
			$instance = new \simplerest\core\libs\Validator();
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
}
