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
       
        $backtrace = null;
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
    