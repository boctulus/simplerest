<?php declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Traits;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Url;

trait ExceptionHandler
{
    /**
     * exception_handler
     *
     * @param  mixed $e
     *
     * @return void
     */
    function exception_handler(\Throwable $e) {
        $current_conn = DB::getCurrentConnectionId();
        DB::closeAllConnections();

        $error_msg = $e->getMessage();

        $config    = Config::get();

        // Detalle interno solo para logs; nunca se expone al cliente cuando debug está OFF.
        $error_location = 'Error on line number ' . $e->getLine() . ' in file - ' . $e->getFile();

        if (!empty($config['log_errors'])) {
            log_error("Error: $error_msg. $error_location");
        }

        $backtrace = null;
        $traces    = null;
        if ($config['debug']) {
            $current_e = new \Exception();
            $traces    = $current_e->getTrace();

            foreach ($traces as $tx => $trace){
                $args = $trace['args'] ?? null;

                if (empty($args)){
                    continue;
                }

                foreach ($args as $ax => $arg){
                    $val = $traces[$tx]['args'][$ax];

                    if ($val instanceof \Throwable) {
                        $trace_str = $val->getTraceAsString();
                        $trace_arr = explode("\n", $trace_str);

                        $traces[$tx]['args'][$ax] = [
                            'message' => $val->getMessage(),
                            'prev'    => $val->getPrevious(),
                            'code'    => $val->getCode(),
                            'file'    => $val->getFile(),
                            'line'    => $val->getLine(),
                            'trace'   => $trace_arr,
                            'extra'   => [
                                'db_connection' => $current_conn
                            ]
                        ];
                    } else {
                        // For non-throwables, keep-it simple and safe
                        if (is_object($val)) {
                            $traces[$tx]['args'][$ax] = 'Object(' . get_class($val) . ')';
                        } elseif (is_array($val)) {
                             $traces[$tx]['args'][$ax] = 'Array(' . count($val) . ')';
                        }
                        // scalars are kept as is
                    }
                }
            }

            try {
                $backtrace      = json_encode($traces, JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;
            } catch (\Throwable $json_err) {
                $backtrace      = "Could not encode trace: " . $json_err->getMessage();
            }

            if ($config['log_stack_trace']) {
                log_error("Trace: $backtrace");
            }
        }

        if (is_cli()){
            $current_e = new \Exception();
            $traces    = $current_e->getTrace();

            dd($traces, $error_msg);
            exit(1);
        }

        // En producción (debug OFF) nunca se expone el mensaje/traza real al cliente.
        $public_msg   = $config['debug'] ? $error_msg : trans('An unexpected error occurred');
        $public_trace = $config['debug'] ? $backtrace : null;

        /*
            Una ruta de API responde SIEMPRE JSON con un código de error real, sin importar
            el User-Agent ni el header Accept. Antes el criterio era isPostmanOrInsomnia(),
            de modo que un navegador, el fetch del SPA o curl recibían HTML con HTTP 200.
            Ver docs/issues/production-debug-flag-hardcoded.md
        */
        if (static::wantsJsonErrorResponse()){
            response()->error($public_msg, 500, $public_trace);
            exit(1);
        }

        // Ruta web: HTML, pero con código HTTP de error real (nunca 200).
        if (!headers_sent()){
            http_response_code(500);
        }

        view('error.php', [
            'status'    => 500,
            'type'      => 'Exception',
            'code'      => $config['debug'] ? ($traces[0]['args'][0]['code'] ?? '') : '',
            'location'  => $config['debug'] ? (($traces[0]['args'][0]['file'] ?? '') . ':' . ($traces[0]['args'][0]['line'] ?? '')) : '',
            'message'   => $public_msg,
            'detail'    => $config['debug'] ? ($traces[0]['args'][0]['trace'] ?? '') : '',
        ], 'templates/tpl_bt5.php');

        exit(1);
    }

    /**
     * ¿La respuesta de error debe ser JSON?
     *
     * true para toda ruta de API (mismo criterio que RequestHandler::parse(): primer
     * segmento 'api', o 'remove_api_slug' activo) y para un cliente que pide JSON
     * explícitamente vía Accept. El User-Agent no participa en la decisión.
     */
    protected static function wantsJsonErrorResponse(): bool
    {
        if (is_cli()){
            return false;
        }

        $config = Config::get();

        if (!empty($config['remove_api_slug'])){
            return true;
        }

        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        $path = preg_replace('/(.*)\/index.php/', '/', $path);

        $base_url = $config['base_url'] ?? '/';
        if ($base_url === ''){
            $base_url = '/';
        }

        if ($base_url !== '/' && strpos($path, $base_url) === 0){
            $path = substr($path, strlen($base_url));
        }

        $first_segment = strtolower(explode('/', ltrim((string) $path, '/'))[0] ?? '');

        if ($first_segment === 'api'){
            return true;
        }

        $accept = strtolower($_SERVER['HTTP_ACCEPT'] ?? '');

        return strpos($accept, 'application/json') !== false;
    }

    /**
	 * Shutdown handler
	 *
	 * @return void
	 */
	public static function shutdown() {
		if ( ( $error = error_get_last() ) ) {
			response()->error( "Script has sthuted down with error", 500, $error);
		}
	}
}
    