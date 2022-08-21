<?php declare(strict_types=1);

namespace simplerest\core\libs;

use simplerest\core\libs\Config;
use simplerest\controllers\MyAuthController;
use simplerest\core\libs\Validator;
use simplerest\core\Acl;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\interfaces\IAcl;
use simplerest\core\interfaces\IValidator;
use simplerest\core\interfaces\IAuth;

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

	static function response() : Response {
		return Response::getInstance();
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

	static function acl() : Acl {
		static $instance;

		if ($instance == null){
			$instance = include CONFIG_PATH . 'acl.php';
		}

        return $instance;
	}
}
