<?php

namespace simplerest\core\exceptions;

use simplerest\core\libs\Logger;

/*
    En teoria deberia ser manejado por el ExceptionHandler

    https://www.php.net/manual/en/language.exceptions.extending.php

*/
class SqlException extends \PDOException {
    public function __construct($message = null, $code = 0) {
        parent::__construct($message, $code);

        $this->sendNotifications($message, $code);
        $this->logError($message, $code);
    }

    protected function sendNotifications($message = null, $code = 0) {
        // send some notifications here
    }

    protected function logError($message = null, $code = 0) {
        // do some logging here
        Logger::logError($message);
    }

}