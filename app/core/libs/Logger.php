<?php declare(strict_types=1);

namespace simplerest\core\libs;;

use simplerest\core\libs\Files;

/*
	Idealmente implementar PSR 3 logger

	https://www.php-fig.org/psr/psr-3/
*/
class Logger
{
    static $logFile = 'log.txt';

    static function getLogFilename(bool $full_path = false)
    {
        if (static::$logFile == null){
            static::$logFile = config()['log_file'];
        }

        return ($full_path ? LOGS_PATH : '') . static::$logFile;
    }
    
    static function truncate($log_file = null){
        Files::writeOrFail(LOGS_PATH . ($log_file ?? static::getLogFilename()), '');
    }
    
    static function getContent( $file = null){
        if ($file == null){
	        $file = static::getLogFilename();
        }

        $path = LOGS_PATH . $file;

		if (!file_exists($path)){
			return false;
		}

		return Files::readOrFail($path);
    }

	static function log($data,  $path = null, $append = true, bool $datetime = true, bool $extra_cr = false)
	{
		$custom_path = true;
		$append      = $append ?? true;
		
		if ($path === null){
			$path = LOGS_PATH . static::getLogFilename();
		} else {
			if (!Strings::contains('/', $path) && !Strings::contains(DIRECTORY_SEPARATOR, $path)){
				$path = LOGS_PATH . $path;
			}
		}

		if (is_array($data) || is_object($data))
			$data = json_encode($data, JSON_UNESCAPED_SLASHES);

		if (config()['error_log'] ?? true){
			$mode = 0;
			if ($custom_path){
				$mode = 3;
			}

			$prefix = '';
			if ($mode === 3 && $datetime){
				$prefix = date('[d-M-Y H:i:s e]') . ' ';
			}	

			error_log($prefix . $data . ($mode == 3 ? PHP_EOL : '') . ($extra_cr ? PHP_EOL : "") , $mode, $path);
		} else {
			$prefix = '';
			if ($datetime){
				$prefix = date('[d-M-Y H:i:s e]') . ' ';
			}			
			
			return Files::writeOrFail($path, $prefix . $data . "\n" . ($extra_cr ? "\n" : ""),  $append ? FILE_APPEND : 0);
		}
	}

	static function dd($data, $msg = '', bool $append = true){
		if (empty($msg)){
			static::log($data, null, $append);
			return;
		}

		static::log([$msg => $data], null, $append);
	}

	static function logError($error){
		if ($error instanceof \Exception){
			$error = $error->getMessage();
		}

		static::log($error, 'errors.txt');
	}

	static function logSQL(string $sql_str){
		static::log($sql_str, 'sql_log.txt');
	}

	
	/*
		Resultado:

		<?php 

		$arr = array (
		'x' => 'Z',
		);
	*/
	static function varExport($data, $path = null, $variable = null){
		if ($path === null){
			$path = LOGS_PATH . 'export.php';
		} else {
			if (!Strings::contains('/', $path) && !Strings::contains(DIRECTORY_SEPARATOR, $path)){
				$path = LOGS_PATH . $path;
			}
		}

		if ($variable === null){
			$bytes = Files::writeOrFail($path, '<?php '. "\r\n\r\n" . 'return ' . var_export($data, true). ';');
		} else {
			if (!Strings::startsWith('$', $variable)){
				$variable = '$'. $variable;
			}
			
			$bytes = Files::writeOrFail($path, '<?php '. "\r\n\r\n" . $variable . ' = ' . var_export($data, true). ';');
		}

		return ($bytes > 0);
	}

}

