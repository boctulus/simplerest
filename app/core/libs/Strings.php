<?php declare(strict_types=1);

namespace simplerest\core\libs;

class Strings 
{	
	static function segment(string $string, string $separator,  int $position){
		$array = explode($separator, $string);

		if (isset($array[$position])){
			return $array[$position];
		}

		return false;
	}

	static function segmentOrFail(string $string, string $separator,  int $position){
		$array = explode($separator, $string);

		if (count($array) === 1 && $position > 0){
			throw new \Exception("There is no segments after explode '$string'");
		}

		if (!isset($array[$position])){
			throw new \Exception("There is no segment in position $position after explode '$string'");
		}

		return $array[$position];
	}

	/*
		Apply tabs to some string
	*/
	static function tabulate(string $str, int $tabs, ?int $first = null, ?int $last = null){
		$lines = explode(PHP_EOL, $str);

		$cnt = count($lines);
        foreach($lines as $ix => $line){
			if ($first !== null && $ix == 0){
				if ($first > 0){
					$lines[$ix] = str_repeat("\t", $first) . $line;
				}  else {
					$lines[$ix] = substr($line, abs($first));
				}
				continue;
			} 

			if ($last !== null && $ix == $cnt-1){
				if ($last < 0){
					$lines[$ix] = substr($line, abs($last));
				}  else {
					$lines[$ix] = str_repeat("\t", $last) . $line;
				}

				continue;
			}

			if ($tabs < 0){
				$lines[$ix] = substr($line, abs($tabs));
			}  else {
				$lines[$ix] = str_repeat("\t",$tabs) . $line;
			}
        }

        $str = implode(PHP_EOL, $lines);

		return $str;
	}


	/*
		Returns $s1 - $s2
	*/
	static function substract(string $s1, string $s2){
		$s2_len = strlen($s2);
		$s1_len = strlen($s1);

		if ($s2_len > $s1_len){
			return;
		}

		if (!self::startsWith($s2, $s1)){
			return;
		}

		return substr($s1, $s2_len);
	}

	// alias
	static function diff(string $s1, string $s2){
		return static::substract($s1, $s2);
	}

	static function trimArray(Array $strings){
		return array_map('trim', $strings);
	}

	static function trimFromLastOcurrence(string $substr, string $str){
		$pos = strrpos($str, $substr);

		if ($pos === false){
			return $str;
		}

		return substr($str, 0, $pos);
	}

	/*
		Returns false if fails

		$pattern can be an Array
		$result_position can be an Array 
	*/
	static function match(string $str, $pattern, $result_position = null){
		if (is_array($pattern)){
			if (is_null($result_position)){
				$result_position = 1;
				$is_pos_array    = false;
			} else {
				$is_pos_array = is_array($result_position);

				if ($is_pos_array){
					if (count($result_position) != count($pattern)){
						throw new \InvalidArgumentException("Number of patterns should be the same as result positions");
					}
				} 
			}

			foreach ($pattern as $ix => $p){
				if (preg_match($p, $str, $matches)){
					if (is_array($result_position)){
						$pos = $result_position[$ix];
					} else {
						$pos = $result_position;
					}

					if (isset($matches[$pos])){
						return $matches[$pos];
					}
				}
			}
		} else {
			if (is_null($result_position)){
				$result_position = 1;
			}			

			if (preg_match($pattern, $str, $matches)){
				if (!isset($matches[$result_position])){
					return false;
				}

				return $matches[$result_position];
			}
		}

		return false;
	}

	static function matchOrFail(string $str, string $pattern, string $error_msg = null) { 
		if (preg_match($pattern, $str, $matches)){			
			return $matches[1];
		}

		if (empty($error_msg)){
			$error_msg = "String $str does not match with $pattern";
		}

		throw new \Exception($error_msg);
	}

	static function ifMatch(string $str, $pattern, callable $fn_success, callable $fn_fail = NULL){
		if (preg_match($pattern, $str, $matches)){
			return call_user_func($fn_success, $matches);
		} else if (is_callable($fn_fail)){
			return call_user_func($fn_fail, $matches);
		} else {
			return $matches;
		}
	}

	/*
        Tipo "preg_match()" destructivo

		Va extrayendo substrings que cumplen con un patron mutando la cadena pasada por referencia.
		
		Aplica solo la primera ocurrencia *
		
		En caso de entregarse un callback, se aplica sobre la salida.
	*/
	
    static function slice(string &$str, string $pattern, callable $output_fn = NULL) {
		if (!preg_match('|\((.*)\)|', $pattern)){
			throw new \Exception("Invalid regex expression '$pattern'. It should contains a (group)");
		}

        $ret = null;
        if (preg_match($pattern,$str,$matches)){
            $str = self::replaceFirst($matches[1], '', $str);
            $ret = $matches[1];
        }

        if ($output_fn != NULL){
            $ret = call_user_func($output_fn, $ret);
        }
     
     	return $ret;   
	}


    /*
        preg_match destructivo

        Similar a slice() pero aplica a todas las ocurrencias y no acepta callback.
     */
    static function sliceAll(string &$str, string $pattern) {
        if (preg_match($pattern,$str,$matches)){
            $str = self::replaceFirst($matches[1], '', $str);
            
            return array_slice($matches, 1);
        }
    }

	static function getParamRegex(string $param_name, ?string $arg_expr = '[a-z0-9A-Z_-]+'){
		$equals = !is_null($arg_expr) ? '[=|:]' : '';		
		return '/^--'.$param_name. $equals . '('.$arg_expr.')$/';
	}

	/*
		$param_name can be string | Array
	*/
	static function matchParam(string $str, $param_name, ?string $arg_expr = '[a-z0-9A-Z_-]+'){

		if (is_array($param_name)){
			$patt = [];
			foreach ($param_name as $p){
				$patt[] = Strings::getParamRegex($p, $arg_expr);
			}	
		} else {
			$patt =	Strings::getParamRegex($param_name, $arg_expr);
		}

		$res = Strings::match($str, $patt, 1);

		if ($arg_expr === null){
			return ($res !== false); 
		}

		return $res;		
	}

	static function enclose(Array $a, string $thing){
		return array_map(function($e) use ($thing){
			return "{$thing}$e{$thing}";
		}, $a);
	}
	
	static function backticks(Array $a){
		return static::enclose($a, '`');
	}

	/*
		CamelCase to snake_case
	*/
	static function camelToSnake(string $name){
		$len = strlen($name);

		if ($len== 0)
			return NULL;

			$conv = strtolower($name[0]);
			for ($i=1; $i<$len; $i++){
				$ord = ord($name[$i]);
				if ($ord >=65 && $ord <= 90){
					$conv .= '_' . strtolower($name[$i]);		
				} else {
					$conv .= $name[$i];	
				}					
			}		
	
		if ($name[$len-1] == '_'){
			$name = substr($name, 0, -1);
		}
	
		return $conv;
	}

	/*
		snake_case to CamelCase
	*/
	static function snakeToCamel(string $name){
        return implode('',array_map('ucfirst',explode('_',$name)));
    }

    static function startsWith(string $substr, ?string $text, bool $case_sensitive = true)
	{
		if (empty($text)){
			return;
		}

		if (!$case_sensitive){
			$text = strtolower($text);
			$substr = strtolower($substr);
		}

        $length = strlen($substr);
        return (substr($text, 0, $length) === $substr);
    }

    static function endsWith(string $substr, string $text, bool $case_sensitive = true)
	{
		if (!$case_sensitive){
			$text = strtolower($text);
			$substr = strtolower($substr);
		}

        return substr($text, -strlen($substr))===$substr;
    }

	/*
		Acomodar al órden de parámetros de PHP 8 con str_contains() 

		y corregir dependencias claro.
	*/
	static function contains(string $substr, string $text, bool $case_sensitive = true)
	{
		if (!$case_sensitive){
			$text = strtolower($text);
			$substr = strtolower($substr);
		}

		return ($substr !== '' &&  mb_strpos($text, $substr) !== false);
	}

	static function containsAny(Array $substr, $text, $case_sensitive = true)
	{
		foreach ($substr as $s){
			if (self::contains($s, $text, $case_sensitive)){
				return true;
			}
		}
		return false;
	}


	/*	
		Verifica si la palabra está contenida en el texto.

		Works in Hebrew and any other unicode characters
		Thanks https://medium.com/@shiba1014/regex-word-boundaries-with-unicode-207794f6e7ed
		Thanks https://www.phpliveregex.com/
	*/
	static function containsWord(string $word, string $text, bool $case_sensitive = true) {
		$mod = $case_sensitive ? 'i' : '';
		
		if (preg_match('/(?<=[\s,.:;"\']|^)' . $word . '(?=[\s,.:;"\']|$)/'.$mod, $text)) return true;
	}
	
	/*
		Verifica si *todas* las palabras se hallan en el texto. 
	*/
	static function containsWords(Array $words, string $text, bool $case_sensitive = true) {
		$mod = $case_sensitive ? 'i' : '';

		foreach($words as $word){
			if (!preg_match('/(?<=[\s,.:;"\']|^)' . $word . '(?=[\s,.:;"\']|$)/'.$mod, $text)){
				return false;
			} 
		}		
		return true;
	}

	/*
		Verifica si al menos una palabra es encontrada en el texto
	*/
	static function containsAnyWord(Array $words, string $text, bool $case_sensitive = true) {
		foreach($words as $word){
			if (self::containsWord($word, $text, $case_sensitive)){
				return true;
			} 
		}	
		return false;	
	}

	static function equal(string $s1, string $s2, bool $case_sensitive = true){
		if ($case_sensitive === false){
			$s1 = strtolower($s1);
			$s2 = strtolower($s2);
		}
		
		return ($s1 === $s2);
	}

	static function rTrim(string $needle, string $haystack)
    {
        if (substr($haystack, -strlen($needle)) === $needle){
			return substr($haystack, 0, - strlen($needle));
		}
		return $haystack;
    }

	static function replace($search, $replace, &$subject, $count = NULL, $case_sensitive = true){

		if ($case_sensitive){
			$subject = str_replace($search, $replace, $subject, $count);
		} else {
			$subject = str_ireplace($search, $replace, $subject, $count);
		}		
	}

	/**
	* String replace nth occurrence
	* 
	* @author	filipkappa
	*/
	static function replaceNth(string $search, string $replace, string $subject, ?int $occurrence)
	{
		$search = preg_quote($search);
		return preg_replace("/^((?:(?:.*?$search){".--$occurrence."}.*?))$search/", "$1$replace", $subject);
	}
   
	static function removeMultipleSpaces($str){
		return preg_replace('!\s+!', ' ', $str);
	}


	/*
		Atomiza string (divivirlo en caracteres constituyentes)
		Source: php.net
	*/
	static function stringTochars($s){
		return	preg_split('//u', $s, -1, PREG_SPLIT_NO_EMPTY);
	}	
		
	
	/*
		str_replace() de solo la primera ocurrencia
	*/
	static function replaceFirst($from, $to, $subject)
	{
		$from = '/'.preg_quote($from, '/').'/';
		return preg_replace($from, $to, $subject, 1);
	}
	
	/*
		str_replace() de solo la ultima ocurrencia
	*/
	static function replaceLast($search, $replace, $subject)
	{
		$pos = strrpos($subject, $search);
	
		if($pos !== false)    
			$subject = substr_replace($subject, $replace, $pos, strlen($search));
		
		return $subject;
	}
	
	
	/*
		Hace el substr() desde el $ini hasta $fin
		
		@param string 
		@param int indice de inicio
		@param int indice final
		@return string el substr() de inicio a fin	
	*/
	static function middle(string $str, int $ini, ?int $end = null) : string {
		if ($end == 0){
			return substr($str, $ini);
		} else {
			$len = $end - $ini;
			return substr($str, $ini, $len);
		}
	}

	static function left(string $str, $to_pos){
		return Strings::middle($str, 0, $to_pos);        
	}

	static function right(string $str, $from_pos){
		return Strings::middle($str, $from_pos, 0);        
	}

	/*
		Parse php class from file
	*/
	static function getClassName(string $file_str, bool $fully_qualified = true){
		$pre_append = '';
			
		if ($fully_qualified){
			$namespace = Strings::match($file_str, '/namespace[ ]{1,}([^;]+)/');
			$namespace = trim($namespace);

			if (!empty($namespace)){
				$pre_append = "$namespace\\";
			}
		}	
		
		$class_name = $pre_append . Strings::matchOrFail($file_str, '/class ([a-z][a-z0-9_]+)/i');

		return $class_name;
	}

	/*
		Parse php class given the filename
	*/
	static function getClassNameByFileName(string $filename, bool $fully_qualified = true){
		$file = file_get_contents($filename);
		return self::getClassName($file, $fully_qualified);
	}

    /**
	 * Scretet_key generator
	 *
	 * @return string
	 */
	static function secretKeyGenerator(){
		$arr=[];
		for ($i=0;$i<(512/7);$i++){
			$arr[] = chr(rand(32,38));
			$arr[] = chr(rand(40,47));
			$arr[] = chr(rand(58,64));
			$arr[] = chr(rand(65,90));
			$arr[] = chr(rand(91,96));
			$arr[] = chr(rand(97,122));	
			$arr[] = chr(rand(123,126));
		}	
    
        shuffle($arr);
		return substr(implode('', $arr),0,512);
	}

	// https://stackoverflow.com/a/4964352
	function toBase($num, $b=62) {
		$base='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$r = $num  % $b ;
		$res = $base[$r];
		$q = floor($num/$b);
		while ($q) {
		  $r = $q % $b;
		  $q =floor($q/$b);
		  $res = $base[$r].$res;
		}
		return $res;
	}
	
	/*
		Determina si un registro cumple o no con las condiciones expuestas

		Los operadores son practicamente los mismos que los de ApiController

	*/
	static function filter(Array $reg, Array $conditions)
	{
		/*
			Volver búsquedas insensitivas al case (sin implementar)
		*/	
		$case_sensitive = false; 

		$ok = true;
		foreach($conditions as $field => $cond)
		{
			if (!is_array($cond)){                
				if ($cond == 'null' && $reg[$field] === null){                   
					continue;
				}

				if (strpos($cond, ',') === false){
					if ($reg[$field] == $cond){
						continue;
					}
				} else {
					$vals = explode(',', $cond);
					if (in_array($reg[$field], $vals)){
						continue;
					}
				}  
				
				$ok = false;
		
			} else {
				// some operators
		
				foreach($cond as $op => $val)
				{

					if (strpos($val, ',') === false)
					{
						switch ($op) {
							case 'eq':
								if ($reg[$field] == $val){                                    
									continue 2;
								}
								break;
							case 'neq':
								if ($reg[$field] != $val){                
									continue 2;
								}
								break;	
							case 'gt':
								if ($reg[$field] > $val){                           
									continue 2;
								}
								break;	
							case 'lt':
								if ($reg[$field] < $val){                             
									continue 2;
								}
								break;
							case 'gteq':
								if ($reg[$field] >= $val){
									$ok = true;
									continue 2;
								}
								break;	
							case 'lteq':
								if ($reg[$field] <= $val){                     
									continue 2;
								}
								break;	
							case 'contains':
								if (Strings::contains($val, $reg[$field])){                           
									continue 2;
								}
								break;    
							case 'notContains':
								if (!Strings::contains($val, $reg[$field])){                  ;
									continue 2;
								}
								break; 
							case 'startsWith':
								if (Strings::startsWith($val, $reg[$field])){                           
									continue 2;
								}
								break; 
							case 'notStartsWith':
								if (!Strings::startsWith($val, $reg[$field])){               
									continue 2;
								}
								break; 
							case 'endsWith':             
								if (Strings::endsWith($val, $reg[$field])){                 
									continue 2;
								}
								break;      
							case 'notEndsWith':
								if (!Strings::endsWith($val, $reg[$field])){                           
									continue 2;
								}
								break;  
							case 'containsWord':
								if (Strings::containsWord($val, $reg[$field])){                           
									continue 2;
								}
								break;   
							case 'notContainsWord':
								if (!Strings::containsWord($val, $reg[$field])){                           
									continue 2;
								}
								break;  
						
							default:
								throw new \InvalidArgumentException("Operator '$op' is unknown", 1);
								break;
						}

					} else {
						// operadores con valores que deben ser interpretados como arrays
						$vals = explode(',', $val);

						switch ($op) {
							case 'between':
								if (count($vals)>2){
									throw new \InvalidArgumentException("Operator between accepts only two arguments");
								}

								if ($reg[$field] >= $vals[0] && $reg[$field] <= $vals[1]){
									continue 2;
								}
								break;
							case 'notBetween':
								if (count($vals)>2){
									throw new \InvalidArgumentException("Operator between accepts only two arguments");
								}

								if ($reg[$field] < $vals[0] || $reg[$field] > $vals[1]){
									continue 2;
								}
								break;
							case 'in':                            
								if (in_array($reg[$field], $vals)){
									continue 2;
								}
								break;
							case 'notIn':                            
								if (!in_array($reg[$field], $vals)){
									continue 2;
								}
								break; 
							case 'contains':
								if (Strings::containsAny($vals, $reg[$field])){                           
									continue 2;
								}
								break;   
							case 'notContains':
								if (!Strings::containsAny($vals, $reg[$field])){                           
									continue 2;
								}
								break;        
							case 'containsWord':
								if (Strings::containsAnyWord($vals, $reg[$field])){                           
									continue 2;
								}
								break;   
							case 'notContainsWord':
								if (!Strings::containsAnyWord($vals, $reg[$field])){                           
									continue 2;
								}
								break;     

							default:
								throw new \InvalidArgumentException("Operator '$op' is unknown", 1);
								break;    
						}

					}
					$ok = false;

				}
	
				
			}

			if (!$ok){
				break;
			}
		
		} 

		return $ok;
	}

	static function realPathNoCoercive(string $path){
		$_path = realpath($path);

		return $_path === false ? $path : $_path;
	}

	static function replaceSlashes(string $path){
		return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
	}
        
	static function removeUnnecessarySlashes(string $path): string{
       	return preg_replace('#/+#','/',$path);
	}

	static function removeTrailingSlash(string $path): string{
		$path = static::realPathNoCoercive($path);

		if (Strings::endsWith('\\', $path) || Strings::endsWith('/', $path)){
			return substr($path, 0, strlen($path)-1);
		}

		return $path;
	}

	static function addTrailingSlash(string $path): string{
		$path = static::realPathNoCoercive($path);

		if (!Strings::endsWith('\\', $path) && !Strings::endsWith('/', $path)){
			return $path . DIRECTORY_SEPARATOR;
		}

		return $path;		
	}

	static function deinterlace(string $literal){
        $arr = str_split($literal);

        $str1 = '';
        for ($i=0; $i<strlen($literal); $i+=2){
            if ($i>strlen($literal)-1){
                break;
            }
            $str1 .= $arr[$i];
        }

        $str2 = '';
        for ($i=1; $i<strlen($literal); $i+=2){
            if ($i>strlen($literal)-1){
                break;
            }
            $str2 .= $arr[$i];
        }
        
        return [$str1, $str2];
    }

    static function interlace(Array $str){
        $ret = '';

        if (count($str) === 0){
            return;
        } 

        if (count($str) === 1){
            return $str[0];
        } 

        $max_len = 0;
        $arr = [];
        foreach ($str as $ix => $s){
			$ls = strlen($s);
            if ($ls > $max_len){
                $max_len = $ls;
            }

            $arr[] = str_split($s);
        }

        for ($i=0; $i<$max_len; $i++){
            foreach ($arr as $a){
                if (isset($a[$i])){
                    $ret .= $a[$i];
                }
            }
        }
        
        return $ret;
    }
}


