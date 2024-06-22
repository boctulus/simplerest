<?php

use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\interfaces\ICommand;

class MysqlLogCommand implements ICommand 
{
    function handle($args){
        $fst = array_shift($args);

        if ($fst == 'on'){
            dd("Iniciando logs ...");
            DB::dbLogOn();
            return;
        }

        if ($fst == 'off'){
            dd("Desactivando logs ...");
            DB::dbLogOff();
            return;
        }

        if ($fst == 'start'){
            dd("Activando logs ...");
            DB::dbLogStart();
            return;
        }

        if ($fst == 'dump'){
            dd("Volcando logs ...");
            DB::dbLogDump();
            return;
        }         
    }   
    
    function help(){
        $str = <<<STR
        php com mysql_log on                                  DB::dbLogOn()
        php com mysql_log off                                 DB::dbLogOff()
        php com mysql_log start [-filename=]  que ejecuta ..  DB::dbLogStart()       
        php com mysql_log dump                                DB::dbLogDump() 
        STR;

        dd(strtoupper(Strings::before(__METHOD__, 'Command::')) . ' HELP');
        dd($str);
    }

} 