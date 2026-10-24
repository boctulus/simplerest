<?php

namespace Boctulus\Simplerest\Core\Handlers;

use Boctulus\Simplerest\Core\Libs\Logger;
use Boctulus\Simplerest\Core\Request;
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
        $httpCode = static::httpStatusCodeFrom($e);

        // Clean all output buffers (in case error happened after output started)
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Determine if client accepts JSON or HTML
        $isCli = (php_sapi_name() === 'cli');
        $acceptsJson = $isCli || Request::acceptsJson();

        if ($acceptsJson) {
            // Send error response as JSON
            $res->error(
                $e->getMessage(),
                $httpCode,
                "Internal error"
            );
        } else {
            http_response_code($httpCode);

            view('error.php', [
                'status'   => $httpCode,
                'type'     => (new \ReflectionClass($e))->getShortName(),
                'code'     => $e->getCode(),
                'location' => $e->getFile() . ':' . $e->getLine(),
                'message'  => $e->getMessage(),
                'detail'   => "Internal error",
            ]);

            exit;
        }
    }

    protected static function httpStatusCodeFrom(\Throwable $e): int
    {
        $code = $e->getCode();

        return is_int($code) && $code >= 100 && $code <= 599 ? $code : 500;
    }
}
