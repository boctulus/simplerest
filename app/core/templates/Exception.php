<?php

namespace __NAMESPACE__;

class __NAME__ extends \Exception {
    public function __construct($message = null, $code = 0, \Throwable $previous = null) {
        // if ($message === null) {
        //    $message = 'Some default message';
        // }

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