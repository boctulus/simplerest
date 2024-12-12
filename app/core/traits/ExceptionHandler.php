<?php declare(strict_types=1);

namespace simplerest\core\traits;

use simplerest\core\libs\DB;
use simplerest\core\libs\Url;

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

        $config    = config();
       
        $backtrace = null;
        if ($config['debug']) {
            $e      = new \Exception();
            $traces = $e->getTrace();

            foreach ($traces as $tx => $trace){
                $args = $exception = $trace['args'] ?? null;

                if (empty($args)){
                    continue;
                }

                foreach ($args as $ax => $arg){
                    $exception = $traces[$tx]['args'][$ax];

                    $trace = $exception->getTraceAsString();
                    $trace = explode("\n", $trace);

                    $traces[$tx]['args'][$ax] = [
                        'message' => $exception->getMessage(),
                        'prev'    => $exception->getPrevious(),
                        'code'    => $exception->getCode(),
                        'file'    => $exception->getFile(),
                        'line'    => $exception->getLine(),
                        'trace'   => $trace,
                        'extra'   => [
                            'db_connection' => $current_conn
                        ]
                    ];
                }
            }

            $backtrace      = json_encode($traces, JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;
            $error_location = 'Error on line number '.$e->getLine().' in file - '.$e->getFile();

            if ($config['log_stack_trace']){
                log_error("Error: $error_msg. Trace: $backtrace");   
            } else{
                log_error("Error: $error_msg");
            }
        } 

        if (is_cli()){
            dd($traces, $error_msg);   
            exit(1);
        }

        // O.... si se solicita salida como JSON en header "Accept"
        if (Url::isPostmanOrInsomnia()){
            response()->error($error_msg, 500, $backtrace);
            exit(1);
        }
        
        view('error.php', [
            'status'    => 500,
            'type'      => 'Exception',
            'code'      => $traces[0]['args'][0]['code'] ?? '',
            'location'  => $traces[0]['args'][0]['file'] . ':'. $traces[0]['args'][0]['line'], 
            'message'   => $traces[0]['args'][0]['message'] ?? '',
            'detail'    => $traces[0]['args'][0]['trace'] ?? '',
        ], 'templates\tpl_bt5.php');

        exit(1);
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
    