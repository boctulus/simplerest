<?php

use simplerest\core\interfaces\ICommand;
use simplerest\core\libs\DB;
use simplerest\core\libs\Files;
use simplerest\core\libs\Strings;
use simplerest\core\traits\CommandTrait;

/*
    php com help {comando}

    Ej:

    php com help mysql_log
*/
class HelpCommand implements ICommand 
{
    use CommandTrait;

    function help($name = null, ...$args)
    {
        dd("Sections:");

        if ($name == null){
            $comm_files   = Files::glob(COMMANDS_PATH, '*Command.php');

            foreach ($comm_files as $file){
                $name = Strings::matchOrFail(Files::convertSlashes($file, '/'), '|/([a-zA-Z0-9_]+)Command.php|');
                dd($name, null, false);
            }
            
            dd("\r\nUsage: help [command] [args...]");
            return;
        }
         
        $name         = Strings::snakeToCamel($name);
        $commandClass = $name . "Command";
        
        $comm_files   = Files::glob(COMMANDS_PATH, '*Command.php');
        
        foreach ($comm_files as $file){
            $_name      = Strings::matchOrFail(Files::convertSlashes($file, '/'), '|/([a-zA-Z0-9_]+)Command.php|');
            
            if ($name != $_name){
                continue;
            }
        
            require $file;
        
            if (class_exists($commandClass)){      
                $commandInstance = new $commandClass();
                
                if (method_exists($commandInstance, 'handle')) {
                    $commandInstance->help(); // podria recibir $args si help() recibiera $args
                    $routing = false;
                } else {
                    throw new \Exception("Command without handle");
                }
            }
        }
            
    }

   
    // Mas metodos de ayuda
    
} 