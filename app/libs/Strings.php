<?php

namespace simplerest\libs;

use simplerest\libs\Debug;

class Strings 
{	
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

	static function trimFromLastOcurrence(string $substr, string $str){
		$pos = strrpos($str, $substr);

		if ($pos === false){
			return $str;
		}

		return substr($str, 0, $pos);
	}

	/*
		Returns NULL if fails
	*/
	static function match(string $str, $pattern, callable $fn = NULL, int $result_position = 1){
		if (preg_match($pattern, $str, $matches)){
			if (!isset($matches[$result_position])){
				return;
			}

			if ($fn != NULL)
				$matches[$result_position] = call_user_func($fn, $matches[$result_position]);
			
			return $matches[$result_position];
		}
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

	/*
		CamelCase to snake_case
	*/
	static function camelToSnake($name){
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
	static function snakeToCamel($name){
        return implode('',array_map('ucfirst',explode('_',$name)));
    }

    static function startsWith($substr, $text, $case_sensitive = true)
	{
		if (!$case_sensitive){
			$text = strtolower($text);
			$substr = strtolower($substr);
		}

        $length = strlen($substr);
        return (substr($text, 0, $length) === $substr);
    }

    static function endsWith($substr, $text, $case_sensitive = true)
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
	static function contains($substr, $text, $case_sensitive = true)
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
	static function containsWord($word, $text, $case_sensitive = true) {
		$mod = $case_sensitive ? 'i' : '';
		
		if (preg_match('/(?<=[\s,.:;"\']|^)' . $word . '(?=[\s,.:;"\']|$)/'.$mod, $text)) return true;
	}
	
	/*
		Verifica si *todas* las palabras se hallan en el texto. 
	*/
	static function containsWords(Array $words, $text, $case_sensitive = true) {
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
	static function containsAnyWord(Array $words, $text, $case_sensitive = true) {
		foreach($words as $word){
			if (self::containsWord($word, $text, $case_sensitive)){
				return true;
			} 
		}	
		return false;	
	}

	// type is not compared
	static function equal($s1, $s2, $case_sensitive = true){
		if ($case_sensitive == false){
			$s1 = strtolower($s1);
			$s2 = strtolower($s2);
		}
		
		return ($s1 == $s2);
	}

	static function rTrim($needle, $haystack)
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
	* @param	type $search  		Search string
	* @param	type $replace	 	Replace string
	* @param	type $subject	 	Source string
	* @param	type $occurrence 	Nth occurrence
	* @return	type 				Replaced string
	* @author	filipkappa
	*/
	static function replaceNth(string $search, string $replace, string $subject, int $occurrence)
	{
		$search = preg_quote($search);
		return preg_replace("/^((?:(?:.*?$search){".--$occurrence."}.*?))$search/", "$1$replace", $subject);
	}
   
	static function replaceMultipleSpaces($str){
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
	static function getClassName(string $file, bool $fully_qualified = true){
		$pre_append = '';
			
		if ($fully_qualified){
			$namespace = Strings::match($file, '/namespace[ ]{1,}([^;]+)/');
			$namespace = trim($namespace);

			if (!empty($namespace)){
				$pre_append = "$namespace\\";
			}
		}	
		
		$class_name = $pre_append . Strings::matchOrFail($file, '/class ([a-z][a-z0-9_]+)/i');

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
        
	static function removeDuplicateSlashes(string $path){
		$path = trim($path);
		return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
	}

	static function addTrailingSlash(string $path){
		$path = trim($path);

		if (!static::endsWith('/', $path) && !static::endsWith('\\', $path)){
			return $path . DIRECTORY_SEPARATOR;
		}

		return $path;
	}

	static function removeTrailingSlash(string $path){
		$path = trim($path);

		if (static::endsWith('/', $path) || static::endsWith('\\', $path)){
			return substr($path, 0, strlen($path)-1);
		}
		
		return $path;
	}

}


