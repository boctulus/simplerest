<?php

namespace simplerest\libs;

use simplerest\core\interfaces\IValidator;

/*
	Validador de campos de formulario
	Ver 2.1 Beta

	@author boctulus
*/
class Validator implements IValidator
{
	protected $required  = true;
	protected $ignored_fields = [];

	static protected $rules = [];
	static protected $rule_types = [];

	function setRequired(bool $state){
		$this->required = $state;
		return $this;
	}

	function ignoreFields(array $fields){
		$this->ignored_fields = $fields;
		return $this;
	}

	function __construct(){
		static::loadRules();
	}

	// default rules
	static function loadRules(){
		static::$rules = [ 
			'bool' => function($dato) {
				return $dato == 0 || $dato == 1;
			},
			'int' => function($dato) {
				return preg_match('/^(-?[0-9]+)+$/',trim($dato)) == 1;
			},
			'float' => function($dato) {
				$dato = trim($dato);
				return is_numeric($dato);
			},
			'number' => function($dato) {
				$dato = trim($dato);
				return ctype_digit($dato) || is_numeric($dato);
			},
			'str' => function($dato) {
				return is_string($dato);
			},
			'string' => function($dato) {
				return is_string($dato);
			},
			'alpha' => function($dato) {                                   
				return (preg_match('/^[a-z]+$/i',$dato) == 1); 
			},	
			'alpha_num' => function($dato) {                                     
				return (preg_match('/^[a-z0-9]+$/i',$dato) == 1);
			},
			'alpha_dash' => function($dato) {                                     
				return (preg_match('/^[a-z\-_]+$/i',$dato) == 1);
			},
			'alpha_num_dash' => function($dato) {                                    
				return (preg_match('/^[a-z0-9\-_]+$/i',$dato) == 1);
			},	
			'alpha_spaces' => function($dato) {                                     
				return (preg_match('/^[a-z ]+$/i',$dato) == 1);  
			},
			'alpha_utf8' => function($dato) {                                    
				return (preg_match('/^[\pL\pM]+$/u',$dato) == 1); 
			},
			'alpha_num_utf8' => function($dato) {                                    
				return (preg_match('/^[\pL\pM0-9]+$/u',$dato) == 1);
			},
			'alpha_dash_utf8' => function($dato) {                                   
				return (preg_match('/^[\pL\pM\-_]+$/u',$dato) == 1); 	
			},
			'alpha_spaces_utf8' => function($dato) {                                   
				return (preg_match('/^[\pL\pM\p{Zs}]+$/u',$dato) == 1); 		
			},
			'notnum' => function($dato) {
				return preg_match('/[0-9]+/',$dato) == 0;
			},
			'email' => function($dato) {
				return filter_var($dato, FILTER_VALIDATE_EMAIL);
			},
			'url' => function($dato) {
				return filter_var($dato, FILTER_VALIDATE_URL);
			},
			'mac' => function($dato) {
				return filter_var($dato, FILTER_VALIDATE_MAC);
			},		
			'domain' => function($dato) {
				return filter_var($dato, FILTER_VALIDATE_DOMAIN);
			},
			'date' => function($dato) {
				return get_class()::isValidDate($dato);
			},
			'time' => function($dato) {
				return get_class()::isValidDate($dato,'H:i:s');
			},			
			'datetime' => function($dato) {
				return preg_match('/[1-2][0-9]{3}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-5][0-9]/',$dato)== 1;
			},
			'array' => function($dato) {
				return is_array($dato);
			}
		];		
		
		static::$rule_types = array_keys(static::$rules);
	}

	/*
		@param string $dato
		@param string $tipo
		@return mixed
		
		Chequear si es mejor utilizar FILTER_VALIDATE_INT y FILTER_VALIDATE_FLOAT

		https://www.php.net/manual/en/filter.filters.validate.php
		
		
	*/
	static function isType($dato, $tipo){
		if ($dato === NULL)
			throw new \InvalidArgumentException('No data'); 
		
		if (empty($tipo))
			throw new \InvalidArgumentException('Data type is undefined');
			
		if (substr($tipo,0,6) == 'regex:'){
			try{
				$regex = substr($tipo,6);
				return preg_match($regex,$dato) == 1;	
			}catch(\Exception $e){
				throw new \InvalidArgumentException('Invalid regular expression!');
			}	
		}

		if (static::$rules == []){
			static::loadRules();
		}

		if (!in_array($tipo, static::$rule_types)){
			throw new \InvalidArgumentException(sprintf(_('Invalid data type for %s'), $tipo));	
		}

		return static::$rules[$tipo]($dato);
	}	


	/*
		@param array $rules
		@param array $data
		@return mixed

	*/
	function validate(array $rules, array $data){
		//var_export(['rules' => $rules, 'data' => $data]);

		// i18n
        bindtextdomain('validator', LOCALE_PATH);
		textdomain('validator');

		//dd($data, 'DATA:');

		if (empty($rules))
			throw new \InvalidArgumentException('No validation rules!');
		
		$errores = [];
		
		/*
			Crea array con el campo como índice
		*/
		$push_error = function ($campo, array $error, array &$errores){
			if(isset($errores[$campo]))
				$errores[$campo][] = $error;
			else{
				$errores[$campo] = [];
				$errores[$campo][] = $error;
			}	
		};
			
		$msg = [];
		foreach($rules as $field => $rule){
			//dd($rule, "RULE $field :");
			
			//var_export(array_diff(array_keys($rule), ['messages']));
			if (isset($rules[$field]['messages'])){
				$msg[$field] = $rule['messages'];
			}				

			//if (isset($data[$field]))			
			//	dd($data[$field], 'VALOR:');			

			//echo "---------------------------------<p/>\n";
			

			// multiple-values for each field

			if (!isset($data[$field])){
				if ($this->required && isset($rule['required']) && $rule['required']){
					$err = (isset($msg[$field]['required'])) ? $msg[$field]['required'] :  "Field is required";
					$push_error($field,['data'=> null, 'error'=> 'required', 'error_detail' =>_($err)],$errores);
				}	
				
				continue;
			}
				
			foreach ((array) $data[$field] as $dato){
				//dd(['field' => $field, 'data' => $dato]);

				//var_export($rules[$field]['messages']);
				//var_export(array_diff(array_keys($rule), ['messages']));

				//$constraints = array_diff(array_keys($rule), ['messages']);
				//var_export($constraints);

				if (!isset($dato) || $dato === '' || $dato === null){
					//var_export(['field' =>$dato, 'required' => $rule['required']]);
	
					if ($this->required && isset($rule['required']) && $rule['required']){
						$err = (isset($msg[$field]['required'])) ? $msg[$field]['required'] :  "Field is required";
						$push_error($field,['data'=> null, 'error'=> 'required', 'error_detail' => _($err)],$errores);
					}
	
					continue 2;
				}
				
				if(in_array($field, (array) $this->ignored_fields))
					continue 2;
				
				if (!isset($rule['required']) || $this->required)
					$rule['required'] = false;
	
				$avoid_type_check = false;
				if($rule['required']){
					if(trim($dato)==''){
						$err = (isset($msg[$field]['required'])) ? $msg[$field]['required'] :  "Field is required";
						$push_error($field,['data'=>$dato, 'error'=>'required', 'error_detail' => _($err)],$errores);
					}						
				}	
				
				if (isset($rule['in'])){
					if (!is_array($rule['in']))
						throw new \InvalidArgumentException("IN requieres an array");

					$err = (isset($msg[$field]['in'])) ? $msg[$field]['in'] :  sprintf(_("%s is not a valid value"),$dato);
					if (!in_array($dato, $rule['in'])){
						$push_error($field,['data'=>$dato, 'error'=>'in', 'error_detail' => _($err)],$errores);
					}					
				}

				if (isset($rule['not_in'])){
					if (!is_array($rule['not_in']))
						throw new \InvalidArgumentException("in requieres an array");

					$err = (isset($msg[$field]['not_in'])) ? $msg[$field]['not_in'] :  sprintf(_("%s is not a valid value"),$dato);
					if (in_array($dato, $rule['not_in'])){
						$push_error($field,['data'=>$dato, 'error'=>'not_in', 'error_detail' => _($err)],$errores);
					}					
				}

				if (isset($rule['between'])){
					if (!is_array($rule['between']))
						throw new \InvalidArgumentException("between requieres an array");

					if (count($rule['between'])!=2)
						throw new \InvalidArgumentException("between requieres an array of two values");

					if ($dato > $rule['between'][1] || $dato < $rule['between'][0]){
						$err = (isset($msg[$field]['between'])) ? $msg[$field]['between'] : sprintf(_("%s is not between %s and %s"), $dato, $rule['between'][0], $rule['between'][1]);
						$push_error($field,['data'=>$dato, 'error'=>'between', 'error_detail' => _($err)],$errores);
					}					
				}

				if (isset($rule['not_between'])){
					if (!is_array($rule['not_between']))
						throw new \InvalidArgumentException("not_between requieres an array");

					if (count($rule['not_between'])!=2)
						throw new \InvalidArgumentException("not_between requieres an array of two values");

					if (!($dato > $rule['not_between'][1] || $dato < $rule['not_between'][0])){
						$err = (isset($msg[$field]['not_between'])) ? $msg[$field]['not_between'] :  sprintf(_("%s should be less than %s or gretter than %s"), $dato, $rule['not_between'][0], $rule['not_between'][1]);
						$push_error($field,['data'=>$dato, 'error'=>'not_between', 'error_detail' => _($err)],$errores);
					}					
				}

				if (isset($rule['type']) && in_array($rule['type'],['number','int','float','decimal']) && trim($dato)=='')
					$avoid_type_check = true;
				
				if (isset($rule['type']) && !$avoid_type_check)
					if (!get_class()::isType($dato, $rule['type'])){
						$err =  (isset($msg[$field]['type'])) ? $msg[$field]['type'] : "It's not a valid {$rule['type']}";
						$push_error($field,['data'=>$dato, 'error'=>'type', 'error_detail' => _($err)],$errores);
					}						
					
						
				if(isset($rule['type'])){	
					if (in_array($rule['type'],['str','not_num','email']) || strpos($rule['type'], 'regex:') === 0 ){
							
							if(isset($rule['min'])){ 
								$rule['min'] = (int) $rule['min'];
								if(strlen($dato)<$rule['min']){
									$err = (isset($msg[$field]['min'])) ? $msg[$field]['min'] :  "The minimum length is %d characters";
									$push_error($field,['data'=>$dato, 'error'=>'min', 'error_detail' => sprintf(_($err),$rule['min'])],$errores);
								}									
							}
							
							if(isset($rule['max'])){ 
								$rule['max'] = (int) $rule['max'];
								if(strlen($dato)>$rule['max']){
									$err = (isset($msg[$field]['max'])) ? $msg[$field]['max'] :  'The maximum length is %d characters';
									$push_error($field,['data'=>$dato, 'error'=>'max', 'error_detail' => sprintf(_($err), $rule['max'])],$errores);
								}
									
							}
					}	
					
					if(in_array($rule['type'],['number','int','float','decimal'])){
							
							if(isset($rule['min'])){ 
								$rule['min'] = (float) $rule['min']; // cast
								if($dato<$rule['min']){
									$err = (isset($msg[$field]['min'])) ? $msg[$field]['min'] :  'Minimum is %d';
									$push_error($field,['data'=>$dato, 'error'=>'min', 'error_detail' => sprintf(_($err), $rule['min'])],$errores);
								}
									
							}
							
							if(isset($rule['max'])){ 
								$rule['max'] = (float) $rule['max']; // cast
								if($dato>$rule['max']){
									$err = (isset($msg[$field]['max'])) ? $msg[$field]['max'] :  'Maximum is %d';

									$push_error($field,['data'=>$dato, 'error'=>'max', 'error_detail' => sprintf(_($err), $rule['max'])],$errores);
								}
									
							}
					}	
					
					if(in_array($rule['type'],['time','date'])){
							
						$t0 = strtotime($dato);
		
						if(isset($rule['min'])){ 
							if($t0<strtotime($rule['min'])){
								$err = (isset($msg[$field]['min'])) ? $msg[$field]['min'] :  'Minimum is '.$rule['min'];
								$push_error($field,['data'=>$dato, 'error'=>'min', 'error_detail' => _($msg)],$errores);
							}
								
						}
						
						if(isset($rule['max'])){ 
							if($t0>strtotime($rule['max'])){
								$err = (isset($msg[$field]['max'])) ? $msg[$field]['max'] : 'Maximum is '.$rule['max'];
								$push_error($field,['data'=>$dato, 'error'=>'max', 'error_detail' => _($msg)],$errores);
							}
								
						}
					}	
					
				}	
			}
							
		}

		$validated = empty($errores) ? true : $errores;

		return $validated;
	}
	
	
	private static function isValidDate($date, $format = 'd-m-Y') {
		$dateObj = \DateTime::createFromFormat($format, $date);
		return $dateObj && $dateObj->format($format) == $date;
	}
}


/*
	Helper
*/


// @author: vitalyart dot ru
if (!function_exists('array_key_first')) {
    /**
     * Gets the first key of an array
     *
     * @param array $array
     * @return mixed
     */
    function array_key_first(array $array)
    {
        if (count($array)) {
            reset($array);
            return key($array);
        }

        return null;
    }
}

