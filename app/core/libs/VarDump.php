<?php declare(strict_types=1);

namespace simplerest\core\libs;

use simplerest\core\libs\Url; 

// Dumper
class VarDump
{
	public static $render = false;

	protected static function pre(callable $fn, ...$args){
		echo '<pre>';
		$fn($args);
		echo '</pre>';
	}

	protected static function export($v = null, $msg = null, bool $additional_carriage_return = false) 
	{	
		$type = gettype($v);

		$PostmanGenerator = Url::isPostman() || Url::isInsomnia();
		
		$cli  = (php_sapi_name() == 'cli');
		$br   = ($cli || $PostmanGenerator) ? PHP_EOL : '<br/>';
		$p    = ($cli || $PostmanGenerator) ? PHP_EOL . PHP_EOL : '<p/>';

		$pre = !$cli;	

		if (Url::isPostman() || Url::isInsomnia() || $type != 'array'){
			$pre = false;
		}
		
		$fn = function($x) use ($type, $PostmanGenerator, $pre){
			$pp = function ($fn, $dato) use ($pre){
				if ($pre){
					self::pre(function() use ($fn, $dato){ 
						$fn($dato);
					});
				} else {
					$fn($dato);
				}
			};

			switch ($type){
				case 'boolean':
				case 'string':
				case 'double':
				case 'float':
					$pp('print_r', $x);
					break;
				case 'array':
					if ($PostmanGenerator){
						$pp('var_export', $x);
					} else {
						$pp('print_r', $x);
					}
					break;	
				case 'integer':
					$pp('var_export', $x);
					break;
				default:
				$pp('var_dump', $x);
			}	
		};
		
		if ($type == 'boolean'){
			$v = $v ? 'true' : 'false';
		}	

		if (!empty($msg)){
			$cfg = config();
			$ini = $cfg['var_dump_separators']['start'] ?? '--| ';
			$end = $cfg['var_dump_separators']['end']   ?? '';

			echo "{$ini}$msg{$end}". (!$pre ? $br : '');
		}
			
		$fn($v);			
	
		switch ($type){
			case 'boolean':
			case 'string':
			case 'double':
			case 'float':	
			case 'integer':
				$include_break = true;
				break;
			case 'array':
				$include_break = $PostmanGenerator;
				break;	
			default:
				$include_break = false;
		}	

		if (!$cli && !$PostmanGenerator && $type != 'array'){
			echo $br;
		}

		if ($include_break && ($cli ||$PostmanGenerator)){
			echo $br;
		}

		if ($additional_carriage_return){
			echo $br;
		}
	}	

	// acá podría retener el buffer y hacer algun ajuste
	static public function dd($val = null, $msg = null, bool $additional_carriage_return = false)
	{
		self::export($val, $msg, $additional_carriage_return);
	}

	static function hideResponse(){
        self::$render = false;
    }

    static function showResponse(){
        self::$render = true;
    }
}