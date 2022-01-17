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
    static function runBackgroundProcess(string $cmd) : ?int {
        $log = 'logs/bck_output.txt';

        switch (PHP_OS_FAMILY) {
            case 'Windows':
                pclose(popen("start /B $cmd", "r")); 
                break;
            case 'Linux':
                $pid = (int) shell_exec("nohup nice -n 19 $cmd > $log 2>&1 & echo $!");
                break;
            default:
            // unsupported
        }

        return $pid ?? null;
    }
}

