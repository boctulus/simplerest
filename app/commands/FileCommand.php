<?php

use simplerest\core\libs\Files;
use simplerest\core\libs\Arrays;
use simplerest\core\libs\Strings;
use simplerest\core\interfaces\ICommand;
use simplerest\core\traits\CommandTrait;

class FileCommand implements ICommand 
{
	use CommandTrait;

	function list($path, ...$opt){
		$recursive = false;
		$pattern   = '*.*';
		
		foreach ($opt as $o){ 
            if (preg_match('/^(--recursive|-r)$/', $o)){
                $recursive = true;
            }

			if (preg_match('/^--(pattern)[=|:]([a-z0-9A-ZñÑ_\.\*]+)$/', $o, $matches)){
				$pattern = $matches[2];
			}
        }

		$files = $recursive ? Files::recursiveGlob($path . DIRECTORY_SEPARATOR . $pattern) : Files::glob($path, $pattern);

		print_array($files);
	}

	function help($name = null, ...$args){
        $str = <<<STR
		php com file list {path} [ --pattern= ] [ -- recursive ]
		
		Examples:

		php com file list 'C:\\xampp\\htdocs\\simplerest'
		php com file list 'C:\\xampp\\htdocs\\simplerest' --pattern='*.bat' --recursive
		php com file list '.' --pattern='*.bat'
		php com file list '.' --pattern='*.bat'  --recursive		
		STR;

        dd(strtoupper(Strings::before(__METHOD__, 'Command::')) . ' HELP');
        dd($str);
    }
}



