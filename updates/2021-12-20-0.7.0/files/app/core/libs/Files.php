<?php declare(strict_types=1);

namespace simplerest\core\libs;

use PDO;
use simplerest\core\libs\SortedIterator;

class Files 
{
	static protected $backup_path;

	static function replace(string $filename, $search, $replace){
		$file  = file_get_contents($filename);
		$file  = str_replace($search, $replace, $file);

		return file_put_contents($filename, $file);
	}

	static function pregReplace(string $filename, $search, $replace){
		$file = file_get_contents($filename);
		$file = preg_replace($search, $replace, $file);
		
		return file_put_contents($filename, $file);
	}

	static function isAbsolutePath(string $path){
		if (PHP_OS_FAMILY === "Windows") {
			if (preg_match('~[A-Z]:'.preg_quote('\\').'~i', $path)){
				return true;
			}

			if (Strings::startsWith('\\', $path)){
				return true;
			}
		}

		if (Strings::startsWith(DIRECTORY_SEPARATOR, $path)){
			return true;
		}

		return false;
	}

	
	static function getAbsolutePath(string $path, string $relative_to =  ROOT_PATH){
		$relative_to = Strings::addTrailingSlash($relative_to);

		if (static::isAbsolutePath($path)){
			return $path;
		}

		if (PHP_OS_FAMILY === "Windows") {
			if (Strings::startsWith('..\\', $path)){
				return realpath($path);
			}
		} else {
			if (Strings::startsWith('..', $path) || Strings::startsWith('.', $path)){
				return realpath($path);
			}
		}
		

		return $relative_to . ltrim(ltrim($path, '/'), '\\');
	}

	static function getRelativePath(string $abs_path, string $relative_to){
		$path = Strings::diff($abs_path, $relative_to); 
		if ($path[0] = '/' || $path[0] == '\\'){
			$path = substr($path, 1);
		}

		return $path;
	}

	// https://stackoverflow.com/a/17161106/980631
	static function recursiveGlob($pattern, $flags = 0) {
		$files = glob($pattern, $flags); 
		foreach (glob(dirname($pattern).'
	static function getDir(string $path){
		$_path = realpath($path);

		if ($_path === false){
			return '';
		}

		$path = $_path;

		if (is_dir($path)){
			return $path;
		}

		return dirname($path);
	}

	static function setBackupDirectory(? string $path = BACKUP_PATH){
		static::$backup_path = $path;

		static::mkDir(static::$backup_path);
	}

	
	static function cp(string $ori, string $dst, bool $simulate = false, bool $overwrite = true){
		$ori = trim($ori);
        $dst = trim($dst);
		
		if (!is_file($ori)){
			throw new \InvalidArgumentException("$ori is not a file as expected");
		}

		if (!file_exists($ori)){
			throw new \InvalidArgumentException("$dst does not exist");
		}

		if (is_dir($dst)){
			$filename = basename($ori);
			$dst = Strings::addTrailingSlash($dst) . $filename;
		}

		if (!$overwrite){
			$file_exists = file_exists($dst);

			if ($file_exists){
				StdOut::pprint("File $dst already exists");
				return;
			}
		}

		if (!empty(static::$backup_path)){
			// sino existiera el archivo en el destino, no tendría sentido respaldarlo

			$file_exists = $file_exists ?? file_exists($dst);

			if ($file_exists){			
				$ori_dir = static::getDir($ori);	

				$trailing_dst_path = Strings::diff($ori_dir, ROOT_PATH);
				
				static::$backup_path = Strings::addTrailingSlash(static::$backup_path);
				
				$bk_dir_path = static::$backup_path . $trailing_dst_path;

				if (!is_dir($bk_dir_path)) {
					static::mkDirOrFail($bk_dir_path);
				}

				if (!isset($filename)){
					$filename = basename($ori);
				}

				if (!@rename($dst, $bk_dir_path . DIRECTORY_SEPARATOR . $filename)){
					throw new \Exception("It was not possible to move $ori to $bk_dir_path");
				} else {
					StdOut::pprint("File $dst was backed up > $bk_dir_path --ok");
				}
			}
		}		
		
        StdOut::pprint("Copying $ori > $dst");

        if (!$simulate){
			$ok = null;
			
			if (!$ok){
				$ok = @copy($ori, $dst);
			}
        } else {
            $ok = true;
        }       

        if ($ok){
            StdOut::pprint("-- ok", true);
        } else {
            StdOut::pprint("-- FAILED !", true);
        }

        return $ok;
    }

	
	
    static function copy(string $ori, string $dst, ?Array $files = null, ?Array $except = null)
    {
		$dst = Strings::removeTrailingSlash($dst);

		$ori_with_trailing_slash = Strings::addTrailingSlash($ori);
		$ori = Strings::removeTrailingSlash(trim($ori));
        $dst = trim($dst);

		if (empty($files)){
			$files = ['glob:*'];
		}	
		
		foreach ($files as $ix => $f){
			$f = trim($f);
			$f = str_replace(["\r\n", "\r", "\n"], '', $f);

			if (empty($f)){
				unset($files[$ix]);
			}
		}

		
		$glob_includes = [];
		foreach ($files as $ix => $f){
			if (Strings::startsWith('glob:', $f)){
				$patt = substr($f, 5);
				$rec  = static::recursiveGlob($ori_with_trailing_slash . $patt);
				
				// glob includes son tomados como relativos
				foreach ($rec as $j => $rf){
					$rec[$j]  = static::getRelativePath($rf, $ori);
				}
				
				$glob_includes = array_merge($glob_includes, $rec);
				unset($files[$ix]);
			} 
			
			if (static::isAbsolutePath($f) && is_dir($f)){
				$files[$ix] = static::getRelativePath($f, $ori);
			}

			// Creo directorio para destino sino existiera 
			#if (!Strings::startsWith('glob:', $f)){
				$dst_dir = $dst . Strings::diff(static::getDir($f), $ori);
				static::mkDirOrFail($dst_dir);
			#}
		}

		$files = array_merge($files, $glob_includes);
		

		if (empty($except)){
			$except = [];
		}

		$except_dirs = [];
		if (is_array($except)){
			
			$glob_excepts = [];
			foreach ($except as $ix => $e){
				if (Strings::startsWith('glob:', $e)){
					$glob_excepts = array_merge($glob_excepts, Files::recursiveGlob($ori_with_trailing_slash . substr($e, 5)));
					unset($except[$ix]);
				}
			}
			$except = array_merge($except, $glob_excepts);

			foreach ($except as $ix => $e){
				if (!Files::isAbsolutePath($e)){
					$except[$ix] = Files::getAbsolutePath($ori . '/'. $e);
				}

				if (is_dir($except[$ix])){
					$except_dirs[] = $except[$ix];
				}
			}
		}
	
        foreach ($files as $_file){
			$_file = trim($_file);

			if (!self::isAbsolutePath($_file)){
				$file = DIRECTORY_SEPARATOR. $_file;
			} else {
				$file = $_file;
			}            

            if (Strings::startsWith('#', $_file) || Strings::startsWith(';', $_file)){
                continue;
            }
            
			
			if (!self::isAbsolutePath($_file)){
				$ori_path = trim($ori . DIRECTORY_SEPARATOR . $_file);
				// $ori_path_abs = static::getAbsolutePath($ori_path);
				$is_file = is_file($ori_path); 
			} else {
				// $ori_path_abs = $_file;
				$ori_path = $_file;
				$ori_path = Strings::substract($ori_path, $ori_with_trailing_slash);
				$is_file = is_file($ori_path);
			}

			$ori_path = Strings::removeUnnecessarySlashes($ori_path);

			if ($is_file){	
				$_dir = static::getDir($ori_path);

				$rel = Strings::substract($_dir, $ori_with_trailing_slash);	
				$_dir_dst = Strings::addTrailingSlash($dst) . $rel;
			
				static::mkDir($_dir_dst);
			}

            if (is_dir($ori_path)){
                static::mkDir($dst . $file);

                $dit = new \RecursiveDirectoryIterator($ori_path, \RecursiveDirectoryIterator::SKIP_DOTS);
                $rit = new \RecursiveIteratorIterator($dit, \RecursiveIteratorIterator::SELF_FIRST);
				$sit = new SortedIterator($rit);

                foreach ($sit as $file) {
                    $file        = $file->getFilename();
                    $full_path   = $sit->current()->getPathname();
					//$current_dir = dirname($full_path);
					
					foreach ($except_dirs as $ix => $e){
						if (Strings::startsWith($e, $full_path)){
							StdOut::pprint("Skiping $file");
							continue 2;
						}
					}
						
					foreach ($except as $ix => $e){
						if ($full_path == $e){
							StdOut::pprint("Skiping $file");
                        	continue 2;
						}
					}

                    $dif = Strings::substract($full_path, $ori);
                    $dst_path =  trim($dst . $dif);

					// Creo directorios faltantes
                    if (is_dir($full_path)){
                        $path = pathinfo($dst_path);

                        $needed_path = Strings::substract($full_path, $ori_path);
                        $dirs = explode(DIRECTORY_SEPARATOR, $needed_path);
                        
                        $p = $dst . DIRECTORY_SEPARATOR . $_file;
            
                        foreach ($dirs as $dir){
                            if ($dir == ''){
                                continue;
                            }

                            $p .=  DIRECTORY_SEPARATOR . $dir;
                            
                            static::mkDir($p);
                        }
						
                        // no se pude copiar un directorio, solo archivos
                        continue;
                    }

                    static::cp($full_path, $dst_path);
                }
                
                continue;
               
            }

			if (static::isAbsolutePath($_file)){
				$_file = Strings::diff($_file, $ori_with_trailing_slash);
			}
			
			$final_path = $dst . DIRECTORY_SEPARATOR . $_file;

            static::cp($ori_path, $final_path);
        }
    }

	
	static function delete(string $file){
		$file = realpath($file);		
		return @unlink($file);
	}

	
	static function deleteOrFail(string $file){
		$file = realpath($file);
		
		if (!file_exists($file) || !is_file($file)){
			throw new \Exception("File $file does not exist");
		}
		
		$ok = @unlink($file);

		if (!$ok){
			throw new \Exception("File $file could not be erased");
		}

		return ;
	}

	
	static function globDelete(string $dir, ?string $glob_pattern = '*.*', bool $recursive = false) {
		$dir = realpath($dir);

		if (is_null($glob_pattern)){
			$glob_pattern = '*.*';
		}
	
		if ($recursive){
			$files = static::recursiveGlob("$dir/$glob_pattern", GLOB_BRACE);
		} else {
			$files = glob("$dir/$glob_pattern", GLOB_BRACE);
		}

		$deleted = 0;
		foreach ($files as $file){
			$filename = basename($file);
			if ($filename == '.' || $filename == '..'){
				continue;
			}

			if (is_file($file)){
				if (unlink($file)){
					$deleted++;
				}
			}
		}

		// Borro directorios recursivamente
		// https://stackoverflow.com/a/27626153/980631
		$resultant_dirs = [];
		while($dirs = glob($dir . '
		if (is_dir($dir)) { 
			$objects = scandir($dir);
			foreach ($objects as $object) { 
				if ($object != "." && $object != "..") { 
					if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
						static::delTree($dir. DIRECTORY_SEPARATOR .$object, $include_self);
					else

					unlink($dir. DIRECTORY_SEPARATOR .$object); 
				} 
			}
			return @rmdir($dir); // *
		} 

		return false;
	}

	static function delTreeOrFail(string $dir, bool $include_self = false){
		static::delTree($dir, $include_self, true);
	}

	static function logger($data, string $file = 'log.txt'){		
		if (is_array($data) || is_object($data))
			$data = json_encode($data);
		
		$data = date("Y-m-d H:i:s"). "\t" .$data;

		return file_put_contents(LOGS_PATH . $file, $data. "\n", FILE_APPEND);
	}

	static function dump($object, $filename = 'dump.txt', $append = false){
		if (!Strings::contains('/', $filename)){
			$path = LOGS_PATH . $filename; 
		} else {
			$path = $filename;
		}

		if ($append){
			file_put_contents($path, var_export($object,  true) . "\n", FILE_APPEND);
		} else {
			file_put_contents($path, var_export($object,  true) . "\n");
		}		
	}
	
    static function mkDir($dir, int $permissions = 0777, bool $recursive = true){
		$ok = null;

		if (!is_dir($dir)) {
			$ok = @mkdir($dir, $permissions, $recursive);
		}

		return $ok;
	}

	// alias
	static function mkdir_ignore($dir, int $permissions = 0777, bool $recursive = true){
		return static::mkDir($dir, $permissions, $recursive);
	}
	
	static function mkDirOrFail($dir, int $permissions = 0777, $recursive = true, string $error = "Failed trying to create %s"){
		$ok = null;

		if (!is_dir($dir)) {
			$ok = @mkdir($dir, $permissions, $recursive);
			if ($ok !== true){
				throw new \Exception(sprintf($error, $dir));
			}
		}

		return $ok;
	}

	static function writableOrFail(string $path, string $error = "Permission error. Path '%s' is not writable"){
		if (!is_writable($path)){
			throw new \Exception(sprintf($error, $path));
		}
	}

	
	static function zip(string $ori, string $dst, ?Array $except = null, bool $overwrite = true)
	{
		if (!extension_loaded('zip') || !file_exists($ori)) {
			return false;
		}
	
		$zip = new \ZipArchive();
		if (!$zip->open($dst, $overwrite && file_exists($dst) ? \ZipArchive::OVERWRITE : \ZipArchive::CREATE)) {
			return false;
		}
	
		if (is_null($except)){
			$except = [];
		}

		$ori = str_replace('\\', '/', realpath($ori));
	
		if (is_dir($ori) === true)
		{
			$new_excluded = [];
			foreach ($except as $ix => $file){
				if (!static::isAbsolutePath($file)){
					$except[$ix] = Files::getAbsolutePath($file, $ori);
				}

				if (is_dir($except[$ix])){
					$new_excluded = array_merge($new_excluded, static::recursiveGlob($except[$ix] . '/*'));	
				}
			}

			$except = array_merge(array_values($except), array_values($new_excluded));

			$files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($ori), \RecursiveIteratorIterator::SELF_FIRST);
	
			foreach ($files as $file)
			{
				$file = str_replace('\\', '/', $file);
	
				// Ignore "." and ".." folders
				if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
					continue;
	
				$file = realpath($file);
	
				if (!empty($except) && in_array($file, $except)){
					continue;
				}

				if (is_dir($file) === true && !in_array($file, $except))
				{
					$zip->addEmptyDir(str_replace($ori . '/', '', $file . '/'));
				}
				else if (is_file($file) === true)
				{
					$zip->addFromString(str_replace($ori . '/', '', $file), file_get_contents($file));
				}
			}
		}
		else if (is_file($ori) === true)
		{
			$zip->addFromString(basename($ori), file_get_contents($ori));
		}
	
		return $zip->close();
	}


	// http://25labs.com/alternative-for-file_get_contents-using-curl/
	static function file_get_contents_curl($url, $retries=5)
	{
		$ua = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.82 Safari/537.36';

		if (extension_loaded('curl') === true)
		{
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url); // The URL to fetch. This can also be set when initializing a session with curl_init().
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // The number of seconds to wait while trying to connect.
			curl_setopt($ch, CURLOPT_USERAGENT, $ua); // The contents of the "User-Agent: " header to be used in a HTTP request.
			curl_setopt($ch, CURLOPT_FAILONERROR, TRUE); // To fail silently if the HTTP code returned is greater than or equal to 400.
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE); // To follow any "Location: " header that the server sends as part of the HTTP header.
			curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE); // To automatically set the Referer: field in requests where it follows a Location: redirect.
			curl_setopt($ch, CURLOPT_TIMEOUT, 10); // The maximum number of seconds to allow cURL functions to execute.
			curl_setopt($ch, CURLOPT_MAXREDIRS, 5); // The maximum number of redirects

			$result = curl_exec($ch);

			curl_close($ch);
		}
		else
		{
			$result = file_get_contents($url);
		}        

		if (empty($result) === true)
		{
			$result = false;

			if ($retries >= 1)
			{
				sleep(1);
				return self::file_get_contents_curl($url, --$retries);
			}
		}    

		return $result;
	}

}    