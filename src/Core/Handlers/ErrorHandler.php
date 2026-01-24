<?php

namespace Boctulus\Simplerest\Core\Handlers;

use Boctulus\Simplerest\Core\Libs\Logger;
use Boctulus\Simplerest\Core\Response;

class ErrorHandler
{
    /**
     * Handle errors and exceptions
     *
     * @param \Throwable $e
     * @return void
     */
    public function handle(\Throwable $e): void
    {
        $res = Response::getInstance();

        // Log error
        Logger::logError($e->getMessage());
        Logger::log("Exception in {$e->getFile()}:{$e->getLine()}");

        // Send error response
        $res->error(
            $e->getMessage(),
            $e->getCode() ?: 500,
            "Internal error"
        );
    }
}
