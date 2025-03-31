<?php

use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Arrays;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Interfaces\ICommand;
use Boctulus\Simplerest\Core\Traits\CommandTrait;

class FileCommand implements ICommand 
{
	use CommandTrait;

	function list($path, ...$opt){
		$recursive = false;
		$pattern   = '*.*';
		$exclude   = null;
		
		foreach ($opt as $o){ 
            if (preg_match('/^(--recursive|-r)$/', $o)){
                $recursive = true;
            }

			if (preg_match('/^--(pattern)[=|:]([a-z0-9A-ZñÑ_\.-_\*\|]+)$/', $o, $matches)){
				$pattern = $matches[2];
			}

			if (preg_match('/^--(exclude)[=|:]([\:a-z0-9A-ZñÑ_\.\*-_\/\\\\]+)$/', $o, $matches)){
				$exclude = $matches[2];
			}
        }

		$files = $recursive ? Files::recursiveGlob($path . DIRECTORY_SEPARATOR . $pattern, 0, $exclude) : Files::glob($path, $pattern, $exclude);

		print_array($files);
	}

	function help($name = null, ...$args){
        $str = <<<STR
		php com file list {path} [ --pattern= ] [ --exclude= ] [ -- recursive ]
		
		Examples:

		php com file list D:\Android\pos\MyPOS
		php com file list D:\Android\pos\MyPOS --pattern='*.java' --recursive
		php com file list '.' --pattern='*.bat'
		php com file list '.' --pattern='*.bat'  --recursive		
		php com file list D:\Android\pos\MyPOS --recursive --pattern='*.java|*.xml|*.gradle|*.properties' --exclude='D:\Android\pos\MyPOS\app\build\*'
		STR;

        dd(strtoupper(Strings::before(__METHOD__, 'Command::')) . ' HELP');
        dd($str);
    }
}



