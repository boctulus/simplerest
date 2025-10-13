<?php

namespace Boctulus\Simplerest\Core\exceptions;

use Boctulus\Simplerest\Core\Libs\Logger;

/*
    En teoria deberia ser manejado por el ExceptionHandler

    https://www.php.net/manual/en/language.exceptions.extending.php

*/

class SqlException extends \Exception {
    protected $query;
    protected $data;

    public function __construct($message, $code = 0, $query = null, $data = [], ?\Throwable $previous = null) {
        parent::__construct($message, $code, $previous);

        $this->query = $query;
        $this->data = $data;

        $this->logError($message, $code);
    }


    public function getQuery() {
        return $this->query;
    }

    public function getData() {
        return $this->data;
    }

    protected function logError($message = null, $code = 0) {
        Logger::logError($message);
    }
}

