<?php

use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Interfaces\ICommand;
use Boctulus\Simplerest\Core\Traits\CommandTrait;

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

	function clear(){
		exec('composer dump-autoload -o', $output1, $return_var1);
		exec('composer clear-cache', $output2, $return_var2);

		dd($return_var1 === 0 && $return_var2 === 0 ? "Cache cleared and autoload dumped successfully" : "Failed to clear cache or dump autoload");
	}

	function help($name = null, ...$args){
        $str = <<<STR
		opcache_clear		
		STR;

        dd(strtoupper(Strings::before(__METHOD__, 'Command::')) . ' HELP');
        dd($str);
    }
}



