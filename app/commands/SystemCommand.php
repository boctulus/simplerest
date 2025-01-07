<?php

use simplerest\core\libs\Strings;
use simplerest\core\interfaces\ICommand;
use simplerest\core\traits\CommandTrait;

class SystemCommand implements ICommand 
{
	use CommandTrait;

	/*	
		Your methods here
	*/

	function opcache_clear(){
		if (!function_exists('opcache_reset')) {
			die("Function opcache_reset() not available");
		}

		$ok = opcache_reset();
		dd($ok ? "OP Cache was cleared" : "OP Cache could Not been cleared :|");
	}

	function help($name = null, ...$args){
        $str = <<<STR
		opcache_clear		
		STR;

        dd(strtoupper(Strings::before(__METHOD__, 'Command::')) . ' HELP');
        dd($str);
    }
}



