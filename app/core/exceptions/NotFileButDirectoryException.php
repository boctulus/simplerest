<?php

namespace simplerest\core\exceptions;

class NotFileButDirectoryException extends \Exception {
    public function __construct($message = null, $code = 0, \Throwable $previous = null) {
        if ($message === null) {
           $message = 'Expected file. Given directory';
        }

        parent::__construct($message, $code, $previous);

        $this->sendNotifications($message, $code);
        $this->logError($message, $code);
    }

    protected function sendNotifications($message = null, $code = 0) {
        // send some notifications here
    }

    protected function logError($message = null, $code = 0) {
        // do some logging here
        // Logger::logError($message);
    }

}