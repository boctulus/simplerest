<?php declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Controllers\MyAuthController;
use Boctulus\Simplerest\Core\Libs\Validator;
use Boctulus\Simplerest\Core\Acl;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\Interfaces\IAcl;
use Boctulus\Simplerest\Core\Interfaces\IValidator;
use Boctulus\Simplerest\Core\Interfaces\IAuth;

/*
	Usar el Container de dependencias en vez de seguir creando factories !
*/

class Factory {
	static function auth() : IAuth {
		static $instance;

		if ($instance == null){
			$instance = new MyAuthController();
		}

        return $instance;
	}

	static function acl() : Acl {
		static $instance;

		if ($instance == null){
			$instance = include CONFIG_PATH . 'acl.php';
		}

        return $instance;
	}

	static function response($data = null, ?int $http_code = 200) : Response {
		$ret = Response::getInstance();

		if ($data != null){
			$ret->send($data, $http_code);
		}

		return $ret;
	}

	static function request() : Request {
		return Request::getInstance();
	}

	static function validador() : Validator {
		static $instance;

		if ($instance == null){
			$instance = new Validator();
		}

        return $instance;
	}
}
