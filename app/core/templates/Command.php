<?php

use simplerest\core\libs\Strings;
use simplerest\core\interfaces\ICommand;
use simplerest\core\traits\CommandTrait;

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



