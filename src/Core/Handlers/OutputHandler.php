<?php

namespace Boctulus\Simplerest\Core\Handlers;

use Boctulus\Simplerest\Core\Api\v1\ApiController;
use Boctulus\Simplerest\Core\Controllers\ConsoleController;
use Boctulus\Simplerest\Core\Libs\Cli;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\Url;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;

class OutputHandler
{
    /**
     * Format output based on controller type and context
     *
     * @param object $controller
     * @param mixed $data
     * @return string
     */
    public function format($controller, $data): string
    {
        if ($data === null) {
            return '';
        }

        $res = Response::getInstance();

        // Determinar formato de salida
        $output_format = $controller->getOutputFormat();

        if ($output_format === 'test' && Request::isBrowser()) {
            $output_format = 'dd';
        } else if ($output_format === 'test') {
            $output_format = 'auto';
        }

        if ($output_format === 'auto') {
            // Lógica automática
            if ($controller instanceof ApiController) {
                $output_format = 'json';
            } elseif ($controller instanceof ConsoleController) {
                $output_format = 'dd';
            } elseif (Url::isPostmanOrInsomnia()) {
                $output_format = 'pretty_json';
            } else {
                $output_format = 'dd';
            }
        }

        // Aplicar formato
        switch ($output_format) {
            case 'json':
                $res->setHeader('Content-Type', 'application/json');
                if (!Strings::isJSON($data)){
                    $data = json_encode($data);
                }
                break;

            case 'pretty_json':
                $res->setHeader('Content-Type', 'application/json');
                if (!Strings::isJSON($data)){
                    $data = json_encode($data, JSON_PRETTY_PRINT);
                }
                break;

            case 'dd':
                if (php_sapi_name() === 'cli') {
                    $data = Cli::formatOutput($data, 0, true);
                } else {
                    $data = Strings::formatOutput($data);
                }
                break;
        }

        return $data;
    }
}
