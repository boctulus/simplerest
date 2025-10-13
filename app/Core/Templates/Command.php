<?php

use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Interfaces\ICommand;
use Boctulus\Simplerest\Core\Traits\CommandTrait;

class __NAME__Command implements ICommand 
{
	use CommandTrait;

	/*	
		Your methods here
	*/

	function help($name = null, ...$args){
        $str = <<<STR
		commmand 
		commmand
		commmand
		...
		STR;

        dd(strtoupper(Strings::before(__METHOD__, 'Command::')) . ' HELP');
        dd($str);
    }
}



