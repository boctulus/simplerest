<?php // declare(strict_types=1);

namespace simplerest\core\libs;

use simplerest\core\libs\DB;
use simplerest\core\libs\i18n\Translate;
use simplerest\core\interfaces\IValidator;

/*
	Validador de campos de formulario
	Ver 2.3 Beta

	@author boctulus@gmail.com
*/
class Validator implements IValidator
{
	protected $required  = true;
	protected $ignored_fields = [];
	protected $uniques = [];
	protected $errors  = [];
	protected $table   = null;

	static protected $rules = [];
	static protected $rule_types = [];

	/*
		https://stackoverflow.com/a/1989284/980631
	*/	
	function __construct(){
		// i18n
		Translate::bind('validator');

		static::loadDefinitions();
	}

	function getErrors() : array {
		return $this->errors;
	}

	function setUniques(Array $uniques, string $table){
		$this->uniques = $uniques;
		$this->table   = $table;
		return $this;
	}

	// default rules
	static function loadDefinitions(){
		static::$rules = [ 
			'boolean' => function($value) {
				return $value == 0 || $value == 1;
			},
			'integer' => function($value) {
				return preg_match('/^(-?[0-9]+)+$/',trim($value)) == 1;
			},
			'float' => function($value) {
				$value = trim($value);
				return is_numeric($value);
			},
			'number' => function($value) {
				$value = is_string($value) ? trim($value) : $value;
				return ctype_digit($value) || is_numeric($value);
			},
			'not_numeric' => function($value) {
				$value = trim($value);
				return preg_match('/^[^0-9]+$/', $value) === 1;  // Solo pasa si NO contiene números
			},
			'string' => function($value) {
				if (is_string($value)) {
					return true;
				} elseif (is_array($value) && !empty($value) && array_keys($value) !== range(0, count($value) - 1)) {
					// Es un array asociativo, lo aceptamos como "JSON decodificado"
					trigger_error("Se esperaba un string para el campo, pero se recibió un array asociativo. Considera usar tipo 'json' en las reglas.", E_USER_WARNING);
					return true;
				}
				return false;
			},
			'alpha' => function($value) {                                   
				return (preg_match('/^[a-z]+$/i',$value) == 1); 
			},	
			'alpha_num' => function($value) {                                     
				return (preg_match('/^[a-z0-9]+$/i',$value) == 1);
			},
			'alpha_dash' => function($value) {                                     
				return (preg_match('/^[a-z\-_]+$/i',$value) == 1);
			},
			'alpha_num_dash' => function($value) {                                    
				return (preg_match('/^[a-z0-9\-_]+$/i',$value) == 1);
			},	
			'alpha_spaces' => function($value) {                                     
				return (preg_match('/^[a-z ]+$/i',$value) == 1);  
			},
			'alpha_utf8' => function($value) {                                    
				return (preg_match('/^[\pL\pM]+$/u',$value) == 1); 
			},
			'alpha_num_utf8' => function($value) {                                    
				return (preg_match('/^[\pL\pM0-9]+$/u',$value) == 1);
			},
			'alpha_dash_utf8' => function($value) {                                   
				return (preg_match('/^[\pL\pM\-_]+$/u',$value) == 1); 	
			},
			'alpha_spaces_utf8' => function($value) {                                   
				return (preg_match('/^[\pL\pM\p{Zs}]+$/u',$value) == 1); 		
			},
			'email' => function($value) {
				return filter_var($value, FILTER_VALIDATE_EMAIL);
			},
			'url' => function($value) {
				return filter_var($value, FILTER_VALIDATE_URL);
			},
			'mac' => function($value) {
				return filter_var($value, FILTER_VALIDATE_MAC);
			},		
			'domain' => function($value) {
				return filter_var($value, FILTER_VALIDATE_DOMAIN);
			},
			'date' => function($value) {
				return get_class()::isValidDate($value);
			},
			'time' => function($value) {
				return get_class()::isValidDate($value,'H:i:s');
			},			
			'datetime' => function($value) {
				return preg_match('/[1-2][0-9]{3}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-5][0-9]/',$value)== 1;
			},
			'json' => function($value) {
				if (is_string($value)) {
					// Verifica si es un JSON válido
					json_decode($value);
					return json_last_error() === JSON_ERROR_NONE;
				} elseif (is_array($value)) {
					// Acepta cualquier array como un JSON decodificado
					return true;
				}
				return false;
			},
			'object' => function($value) {
				return is_array($value) && !isset($value[0]);
			},			
			'either' => function($value, $options) {
				foreach ($options['accepts'] as $type) {
					if (static::isType($value, $type)) {
						return true;
					}
				}
				return false;
			}
		];		

		/*
			Alias
		*/

		static::$rules['int']           = static::$rules['integer'];
		static::$rules['num']           = static::$rules['number'];
		static::$rules['numeric']       = static::$rules['number'];
		static::$rules['not-number']    = static::$rules['not_numeric'];
		static::$rules['not-num']       = static::$rules['not_numeric'];
		static::$rules['notnum'] 	    = static::$rules['not_numeric'];
		static::$rules['not-numeric']   = static::$rules['not_numeric'];
		static::$rules['bool']          = static::$rules['boolean'];
		static::$rules['str']           = static::$rules['string'];
		static::$rules['timestamp']     = static::$rules['datetime'];
		
		static::$rule_types = array_keys(static::$rules);
	}

	// Para ser usado en UPDATEs
	function setRequired(bool $state){
		$this->required = $state;
		return $this;
	}

	function ignoreFields(array $fields){
		$this->ignored_fields = $fields;
		return $this;
	}

	/*
		@param string $value
		@param string $expected_type
		@return mixed
		
		Chequear si es mejor utilizar FILTER_VALIDATE_INT y FILTER_VALIDATE_FLOAT

		https://www.php.net/manual/en/filter.filters.validate.php
		
		
	*/
	static function isType($value, string $expected_type, bool $null_throw_exception = false){
		if ($value === NULL){
			if (!$null_throw_exception){
				throw new \InvalidArgumentException('No data'); 
			} else {
				return false;
			}
		}			
		
		if (empty($expected_type)){
			throw new \InvalidArgumentException('Data type is undefined');
		}
			
		if (substr($expected_type,0,6) == 'regex:'){
			try{
				$regex = substr($expected_type,6);
				return preg_match($regex,$value) == 1;	
			}catch(\Exception $e){				
				throw new \InvalidArgumentException('Regex malformed');
			}	
		}

		if (substr($expected_type,0,8) == 'decimal('){
			$regex = substr($expected_type,6);
			$nums  = substr($expected_type, strlen('decimal('), -1);
			list($tot,$dec) = explode(',', $nums);

			$f = explode('.',$value);
			return (strlen($value) <= ($tot+1) && strlen($f[1] ?? '') <= $dec);	
		}

		if (static::$rules == []){
			static::loadDefinitions();
		}

		// La única modificación real es aquí:
		if (!in_array($expected_type, static::$rule_types)){
			throw new \InvalidArgumentException('Invalid data type: ' . $expected_type);
		}

		return static::$rules[$expected_type]($value);
	}	

	protected function validateStructure($value, $structure, $field_path = '') {
		$errors = [];
		
		// Si es un array de objetos/elementos
		if (is_array($value) && isset($value[0])) {
			foreach ($value as $index => $item) {
				$item_path = $field_path ? "$field_path.$index" : $index;
				$item_errors = $this->validateStructure($item, $structure, $item_path);
				$errors = array_merge($errors, $item_errors);
			}
			return $errors;
		}
	
		// Para objetos individuales
		if ($structure['type'] === 'object' && isset($structure['fields'])) {
			if (!is_array($value)) {
				return [[$field_path, 'type', 'Expected object/array']];
			}
	
			foreach ($structure['fields'] as $key => $rules) {
				$full_path = $field_path ? "$field_path.$key" : $key;
				
				if (!isset($value[$key])) {
					if (isset($rules['required']) && $rules['required']) {
						$errors[] = [$full_path, 'required', 'Field is required'];
					}
					continue;
				}
	
				if (isset($rules['structure'])) {
					$nested_errors = $this->validateStructure($value[$key], $rules['structure'], $full_path);
					$errors = array_merge($errors, $nested_errors);
				} else {
					if (!$this->validateValue($value[$key], $rules)) {
						$errors[] = [$full_path, 'type', "Invalid type for field"];
					}
				}
			}
		}
	
		return $errors;
	}
	
	protected function validateValue($value, $rules) {
		// Reutiliza la lógica existente de validación
		$type = $rules['type'] ?? null;
		if (!$type) {
			return true;
		}
		
		try {
			return static::isType($value, $type);
		} catch(\Exception $e) {
			return false;
		}
	}

	/*
		@param array $data
		@param array $rules
		@return boolean

	*/
	function validate(array $data, ?array $rules = null, $fillables = null, $not_fillables = null) : bool 
	{
		if (empty($rules))
			throw new \InvalidArgumentException('No validation rules!');
		
		$errors = [];

		if ($fillables !== null){
			foreach ($data as $field => $value){
				if (!in_array($field, $fillables)){
					$errors[$field][] = [
						"error" => "fillable",
						"error_detail" => "Field is not fillable"
					];
				}
			}

			// Por eficiencia si hay campos no-fillables, aborto.
			if (!empty($errors)){
				$this->errors = $errors;
				return false;
			}
		}

		if ($not_fillables !== null){
			foreach ($data as $field => $value){
				if (in_array($field, $not_fillables)){
					$errors[$field][] = [
						"error"        => "not_fillable",
						"error_detail" => "Field is not fillable"
					];
				}
			}

			// Por eficiencia si hay errores, aborto.
			if (!empty($errors)){
				$this->errors = $errors;
				return false;
			}
		}

		if (!empty($this->uniques)){
			foreach ($this->uniques as $unique_field){
				if (isset($data[$unique_field])){
					if (DB::table($this->table)->where([
						$unique_field => $data[$unique_field] 
					])->exists()){
						$errores[$unique_field] = [
							"error" => "unique",
							"error_detail" => "Field is no unique"
						];
					}
				}
			}

			if (!empty($errors)){
				$this->errors = $errors;
				return false;
			}
		}

		/*
			Crea array con el campo como índice
		*/
		$push_error = function ($campo, array $error, array &$errors){
			if(isset($errors[$campo]))
				$errors[$campo][] = $error;
			else{
				$errors[$campo] = [];
				$errors[$campo][] = $error;
			}	
		};
			
		$msg = [];
		foreach($rules as $field => $rule){
			if (isset($rule['type']) && $rule['type'] == 'array'){
				if (isset($data[$field])){
					$value = $data[$field];
				
					if (!is_array($value)){
						$err = sprintf(trans("Invalid Data type. Expected Array"));
						$push_error($field,['data'=>$value, 'error'=>'type', 'error_detail' => trans($err)], $errors);
					}

					if (isset($rule['len']) && count($value) != $rule['len']){
						$err = sprintf(trans("Array has not the expected lenght of %d"), $rule['len']);
						$push_error($field,['data'=>$value, 'error'=>'len', 'error_detail' => trans($err)], $errors);
					}

					if (isset($rule['min_len']) && count($value) < $rule['min_len']){
						$err = sprintf(trans("Array has not the minimum expected lenght of %d"),  $rule['min_len']);
						$push_error($field,['data'=>$value, 'error'=>'min_len', 'error_detail' => trans($err)], $errors);
					}

					if (isset($rule['max_len']) && count($value) > $rule['max_len']){
						$err = sprintf(trans("Array has not the maximum expected lenght of %d"),  $rule['max_len']);
						$push_error($field,['data'=>$value, 'error'=>'max_len', 'error_detail' => trans($err)], $errors);
					}
				}
			}
			
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
					$push_error($field,['data'=> null, 'error'=> 'required', 'error_detail' =>trans($err)],$errors);
				}	
				
				continue;
			}
				
			foreach ((array) $data[$field] as $value){
				//dd(['field' => $field, 'data' => $value]);

				//var_export($rules[$field]['messages']);
				//var_export(array_diff(array_keys($rule), ['messages']));

				//$constraints = array_diff(array_keys($rule), ['messages']);
				//var_export($constraints);

				if (!isset($value) || $value === '' || $value === null){
					//var_export(['field' =>$value, 'required' => $rule['required']]);
	
					if ($this->required && isset($rule['required']) && $rule['required']){
						$err = (isset($msg[$field]['required'])) ? $msg[$field]['required'] :  "Field is required";
						$push_error($field,['data'=> null, 'error'=> 'required', 'error_detail' => trans($err)],$errors);
					}
	
					continue 2;
				}
				
				if(in_array($field, (array) $this->ignored_fields))
					continue 2;
				
				if (!isset($rule['required']) || $this->required)
					$rule['required'] = false;
	
				$avoid_type_check = false;
				if($rule['required']){
					if(trim($value)==''){
						$err = (isset($msg[$field]['required'])) ? $msg[$field]['required'] :  "Field is required";
						$push_error($field,['data'=>$value, 'error'=>'required', 'error_detail' => trans($err)],$errors);
					}						
				}	

				// Si hay una estructura definida, validarla
				if (isset($rule['structure'])) {
					$structure_errors = $this->validateStructure($data[$field], $rule['structure'], $field);
					foreach ($structure_errors as [$path, $error_type, $message]) {
						$errors[$path][] = [
							'error' => $error_type,
							'error_detail' => $message
						];
					}
					continue;
				}

				// Creo un alias entre in y set
				if (isset($rule['set'])){
					$rule['in'] = $rule['set'];
				}
				
				if (isset($rule['in'])){
					if (!is_array($rule['in']))
						throw new \InvalidArgumentException("IN requieres an array");

					$err = (isset($msg[$field]['in'])) ? $msg[$field]['in'] : sprintf(trans("%s is not a valid value. Accepted: %s"), $value, implode(',', $rule['in']));
					if (!in_array($value, $rule['in'])){
						$push_error($field,['data'=>$value, 'error'=>'in', 'error_detail' => trans($err)],$errors);
					}					
				}

				if (isset($rule['not_in'])){
					if (!is_array($rule['not_in']))
						throw new \InvalidArgumentException("in requieres an array");

					$err = (isset($msg[$field]['not_in'])) ? $msg[$field]['not_in'] : sprintf(trans("%s is not a valid value. Accepted: %s"), $value, implode(',', $rule['in']));
					if (in_array($value, $rule['not_in'])){
						$push_error($field,['data'=>$value, 'error'=>'not_in', 'error_detail' => trans($err)],$errors);
					}					
				}

				if (isset($rule['between'])){
					if (!is_array($rule['between']))
						throw new \InvalidArgumentException("between requieres an array");

					if (count($rule['between'])!=2)
						throw new \InvalidArgumentException("between requieres an array of two values");

					if ($value > $rule['between'][1] || $value < $rule['between'][0]){
						$err = (isset($msg[$field]['between'])) ? $msg[$field]['between'] : sprintf(trans("%s is not between %s and %s"), $value, $rule['between'][0], $rule['between'][1]);
						$push_error($field,['data'=>$value, 'error'=>'between', 'error_detail' => trans($err)],$errors);
					}					
				}

				if (isset($rule['not_between'])){
					if (!is_array($rule['not_between']))
						throw new \InvalidArgumentException("not_between requieres an array");

					if (count($rule['not_between'])!=2)
						throw new \InvalidArgumentException("not_between requieres an array of two values");

					if (!($value > $rule['not_between'][1] || $value < $rule['not_between'][0])){
						$err = (isset($msg[$field]['not_between'])) ? $msg[$field]['not_between'] :  sprintf(trans("%s should be less than %s or gretter than %s"), $value, $rule['not_between'][0], $rule['not_between'][1]);
						$push_error($field,['data'=>$value, 'error'=>'not_between', 'error_detail' => trans($err)],$errors);
					}					
				}

				if (isset($rule['type']) && in_array($rule['type'],['number','int','float','double']) && trim($value)=='')
					$avoid_type_check = true;
				
				if (isset($rule['type']) && !$avoid_type_check){
					if ($rule['type'] != 'array' && !get_class($this)::isType($value, $rule['type'])){
						$err =  (isset($msg[$field]['type'])) ? $msg[$field]['type'] :  sprintf(trans("It's not a valid %s"), $rule['type']);
						$push_error($field,['data'=>$value, 'error'=>'type', 'error_detail' => sprintf(trans($err), $rule['type'])],$errors);
					}
				}
						
				if(isset($rule['type'])){	
					if (in_array($rule['type'],['str','string','not_num','email']) || strpos($rule['type'], 'regex:') === 0 ){
            
						if(isset($rule['min'])){ 
							$rule['min'] = (int) $rule['min'];
							if(strlen(trim($value)) < $rule['min']){
								$err = (isset($msg[$field]['min'])) ? $msg[$field]['min'] : "The minimum length is %d characters";
								$push_error($field,['data'=>$value, 'error'=>'min', 'error_detail' => sprintf(trans($err),$rule['min'])],$errors);
							}                                    
						}
							
						if(isset($rule['max'])){ 
							$rule['max'] = (int) $rule['max'];
							if(strlen(trim($value)) > $rule['max']){
								$err = (isset($msg[$field]['max'])) ? $msg[$field]['max'] : 'The maximum length is %d characters';
								$push_error($field,['data'=>$value, 'error'=>'max', 'error_detail' => sprintf(trans($err), $rule['max'])],$errors);
							}                                
						}
					}    
					
					if(in_array($rule['type'],['number','int','float','double'])){
							
							if(isset($rule['min'])){ 
								$rule['min'] = (float) $rule['min']; // cast
								if($value<$rule['min']){
									$err = (isset($msg[$field]['min'])) ? $msg[$field]['min'] :  'Minimum is %d';
									$push_error($field,['data'=>$value, 'error'=>'min', 'error_detail' => sprintf(trans($err), $rule['min'])],$errors);
								}
									
							}
							
							if(isset($rule['max'])){ 
								$rule['max'] = (float) $rule['max']; // cast
								if($value>$rule['max']){
									$err = (isset($msg[$field]['max'])) ? $msg[$field]['max'] :  'Maximum is %d';

									$push_error($field,['data'=>$value, 'error'=>'max', 'error_detail' => sprintf(trans($err), $rule['max'])],$errors);
								}
									
							}
					}	
					
					if(in_array($rule['type'],['time','date'])){
							
						$t0 = strtotime($value);

						if(isset($rule['min'])){ 
							if($t0<strtotime($rule['min'])){
								$err = (isset($msg[$field]['min'])) ? $msg[$field]['min'] :  'Minimum is '.$rule['min'];
								$push_error($field,['data'=>$value, 'error'=>'min', 'error_detail' => sprintf(trans($err), $rule['min'])],$errors);
							}
								
						}
						
						if(isset($rule['max'])){ 
							if($t0>strtotime($rule['max'])){
								$err = (isset($msg[$field]['max'])) ? $msg[$field]['max'] : 'Maximum is '.$rule['max'];
								$push_error($field,['data'=>$value, 'error'=>'max', 'error_detail' => sprintf(trans($err), $rule['max'])],$errors);
							}
								
						}
					}	
					
				}	
			}
							
		}

		$this->errors = $errors;
		
		return empty($errors);
	}
	
	// ok
	private static function isValidDate($date, $format = 'Y-m-d') {
		$dateObj = \DateTime::createFromFormat($format, $date);
		return $dateObj && $dateObj->format($format) == $date;
	}
}