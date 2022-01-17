<?php declare(strict_types=1);

namespace simplerest\core\libs;

use simplerest\core\Model;
use simplerest\core\libs\DB;
use simplerest\core\libs\Factory;

class System
{
    /*
        https://factory.dev/pimcore-knowledge-base/how-to/execute-php-pimcore
    */
    static function runInBackground(string $cmd, bool $capture_output = true, $ignore_user_abort = true) {
        ignore_user_abort($ignore_user_abort);

        $output = 'logs/bck_output.txt';

        switch (PHP_OS_FAMILY) {
            case 'Windows':
                if ($capture_output){
                    pclose(popen("start /B $cmd >> $output ", "r")); 
                } else {
                    pclose(popen("start /B $cmd", "r")); 
                }                
                break;
            case 'Linux':
                if ($capture_output){
                    $pid = (int) shell_exec("nohup nice -n 19 $cmd > $output 2>&1 & echo $!");
                } else {
                    $pid = (int) shell_exec("nohup nice -n 19 $cmd > /dev/null 2>&1 & echo $!");
                }
                break;
            default:
            // unsupported
            return false;
        }

        return $pid ?? null;
    }
}

