<?php // declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\i18n\Translate;
use Boctulus\Simplerest\Core\Interfaces\IValidator;

/*
    Validador de campos de formulario
    Ver 2.3 Beta
    @author boctulus@gmail.com
*/
class Validator implements IValidator
{
    protected $required = true;
    protected $ignored_fields = [];
    protected $uniques = [];
    protected $errors = [];
    protected $warnings = [];
    protected $table = null;
    protected $current_field;

    function __construct()
    {
        // i18n
        Translate::bind('validator');
    }

    function getErrors(): array
    {
        return $this->errors;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    function setUniques(array $uniques, string $table)
    {
        $this->uniques = $uniques;
        $this->table = $table;
        return $this;
    }

    // Para ser usado en UPDATEs
    function setRequired(bool $state)
    {
        $this->required = $state;
        return $this;
    }

    function ignoreFields(array $fields)
    {
        $this->ignored_fields = $fields;
        return $this;
    }

    // Métodos de validación por tipo
    protected function validateBoolean($value)
    {
        return $value == 0 || $value == 1;
    }

    protected function validateInteger($value)
    {
        return preg_match('/^(-?[0-9]+)+$/', trim($value)) == 1;
    }

    protected function validateFloat($value)
    {
        $value = trim($value);
        return is_numeric($value);
    }

    protected function validateNumber($value)
{
    $value = is_string($value) ? trim($value) : $value;
    // Solo usa ctype_digit si $value es string, de lo contrario usa is_numeric
    return (is_string($value) && ctype_digit($value)) || is_numeric($value);
}

    protected function validateNotNumeric($value)
    {
        $value = trim($value);
        return preg_match('/^[^0-9]+$/', $value) === 1;
    }

    protected function validateString($value)
    {
        if (is_string($value)) {
            return true;
        } elseif (is_array($value) && !empty($value) && array_keys($value) !== range(0, count($value) - 1)) {
            // Es un array asociativo, lo aceptamos como "JSON decodificado"
            $this->warnings[$this->current_field][] = [
                'data' => $value,
                'warning' => 'type',
                'warning_detail' => 'Se esperaba un string, pero se recibió un array. Considera usar tipo "json" en las reglas.'
            ];
            return true;
        }
        return false;
    }

    protected function validateAlpha($value)
    {
        return preg_match('/^[a-z]+$/i', $value) == 1;
    }

    protected function validateAlphaNum($value)
    {
        return preg_match('/^[a-z0-9]+$/i', $value) == 1;
    }

    protected function validateAlphaDash($value)
    {
        return preg_match('/^[a-z\-_]+$/i', $value) == 1;
    }

    protected function validateAlphaNumDash($value)
    {
        return preg_match('/^[a-z0-9\-_]+$/i', $value) == 1;
    }

    protected function validateAlphaSpaces($value)
    {
        return preg_match('/^[a-z ]+$/i', $value) == 1;
    }

    protected function validateAlphaUtf8($value)
    {
        return preg_match('/^[\pL\pM]+$/u', $value) == 1;
    }

    protected function validateAlphaNumUtf8($value)
    {
        return preg_match('/^[\pL\pM0-9]+$/u', $value) == 1;
    }

    protected function validateAlphaDashUtf8($value)
    {
        return preg_match('/^[\pL\pM\-_]+$/u', $value) == 1;
    }

    protected function validateAlphaSpacesUtf8($value)
    {
        return preg_match('/^[\pL\pM\p{Zs}]+$/u', $value) == 1;
    }

    protected function validateEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    protected function validateUrl($value)
    {
        return filter_var($value, FILTER_VALIDATE_URL);
    }

    protected function validateMac($value)
    {
        return filter_var($value, FILTER_VALIDATE_MAC);
    }

    protected function validateDomain($value)
    {
        return filter_var($value, FILTER_VALIDATE_DOMAIN);
    }

    protected function validateDate($value)
    {
        return $this->isValidDate($value);
    }

    protected function validateTime($value)
    {
        return $this->isValidDate($value, 'H:i:s');
    }

    protected function validateDatetime($value)
    {
        return preg_match('/[1-2][0-9]{3}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-5][0-9]/', $value) == 1;
    }

    protected function validateJson($value)
    {
        if (is_string($value)) {
            json_decode($value);
            return json_last_error() === JSON_ERROR_NONE;
        } elseif (is_array($value)) {
            return true;
        }
        return false;
    }

    protected function validateObject($value)
    {
        return is_array($value) && !isset($value[0]);
    }

    protected function validateEither($value, $options)
    {
        foreach ($options['accepts'] as $type) {
            if ($this->isType($value, $type)) {
                return true;
            }
        }
        return false;
    }

    // Alias y mapeo de tipos
    protected $typeAliases = [
        'int' => 'integer',
        'num' => 'number',
        'numeric' => 'number',
        'not-number' => 'not_numeric',
        'not-num' => 'not_numeric',
        'notnum' => 'not_numeric',
        'not-numeric' => 'not_numeric',
        'bool' => 'boolean',
        'str' => 'string',
        'timestamp' => 'datetime',
    ];

    protected $typeMethods = [
        'boolean' => 'validateBoolean',
        'integer' => 'validateInteger',
        'float' => 'validateFloat',
        'number' => 'validateNumber',
        'not_numeric' => 'validateNotNumeric',
        'string' => 'validateString',
        'alpha' => 'validateAlpha',
        'alpha_num' => 'validateAlphaNum',
        'alpha_dash' => 'validateAlphaDash',
        'alpha_num_dash' => 'validateAlphaNumDash',
        'alpha_spaces' => 'validateAlphaSpaces',
        'alpha_utf8' => 'validateAlphaUtf8',
        'alpha_num_utf8' => 'validateAlphaNumUtf8',
        'alpha_dash_utf8' => 'validateAlphaDashUtf8',
        'alpha_spaces_utf8' => 'validateAlphaSpacesUtf8',
        'email' => 'validateEmail',
        'url' => 'validateUrl',
        'mac' => 'validateMac',
        'domain' => 'validateDomain',
        'date' => 'validateDate',
        'time' => 'validateTime',
        'datetime' => 'validateDatetime',
        'json' => 'validateJson',
        'object' => 'validateObject',
        'either' => 'validateEither',
    ];

    function isType($value, string $expected_type, bool $null_throw_exception = false)
    {
        if ($value === null) {
            if (!$null_throw_exception) {
                throw new \InvalidArgumentException('No data');
            } else {
                return false;
            }
        }

        if (empty($expected_type)) {
            throw new \InvalidArgumentException('Data type is undefined');
        }

        // Manejo de regex
        if (substr($expected_type, 0, 6) === 'regex:') {
			$regex = substr($expected_type, 6);
			
			// Verificar si el regex es válido
			if (@preg_match($regex, '') === false) {
				throw new \InvalidArgumentException('Regex malformed');
			}
			
			// Aplicar el regex al valor
			return preg_match($regex, $value) === 1;
		}

        // Manejo de decimal
        if (substr($expected_type, 0, 8) == 'decimal(') {
            $nums = substr($expected_type, 8, -1);
            list($tot, $dec) = explode(',', $nums);
            $f = explode('.', $value);
            return (strlen($value) <= ($tot + 1) && strlen($f[1] ?? '') <= $dec);
        }

        // Resolver alias
        if (isset($this->typeAliases[$expected_type])) {
            $expected_type = $this->typeAliases[$expected_type];
        }

        // Verificar si el tipo es válido
        if (!isset($this->typeMethods[$expected_type])) {
            throw new \InvalidArgumentException('Invalid data type: ' . $expected_type);
        }

        // Llamar al método correspondiente
        $method = $this->typeMethods[$expected_type];
        return $this->$method($value);
    }

    protected function validateStructure($value, $structure, $field_path = '')
    {
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

    protected function validateValue($value, $rules)
    {
        $type = $rules['type'] ?? null;
        if (!$type) {
            return true;
        }

        try {
            return $this->isType($value, $type);
        } catch (\Exception $e) {
            return false;
        }
    }

    function validate(array $data, ?array $rules = null, $fillables = null, $not_fillables = null): bool
    {
        $errors = [];

		if ($rules === []) {
			throw new \InvalidArgumentException('Rules array cannot be empty');
		}

		if ($rules === null) {
			return true;
		}

        if ($fillables !== null) {
            foreach ($data as $field => $value) {
                if (!in_array($field, $fillables)) {
                    $errors[$field][] = [
                        "error" => "fillable",
                        "error_detail" => "Field is not fillable"
                    ];
                }
            }

            if (!empty($errors)) {
                $this->errors = $errors;
                return false;
            }
        }

        if ($not_fillables !== null) {
            foreach ($data as $field => $value) {
                if (in_array($field, $not_fillables)) {
                    $errors[$field][] = [
                        "error" => "not_fillable",
                        "error_detail" => "Field is not fillable"
                    ];
                }
            }

            if (!empty($errors)) {
                $this->errors = $errors;
                return false;
            }
        }

        if (!empty($this->uniques)) {
            foreach ($this->uniques as $unique_field) {
                if (isset($data[$unique_field])) {
                    if (DB::table($this->table)->where([$unique_field => $data[$unique_field]])->exists()) {
                        $errors[$unique_field] = [
                            "error" => "unique",
                            "error_detail" => "Field is not unique"
                        ];
                    }
                }
            }

            if (!empty($errors)) {
                $this->errors = $errors;
                return false;
            }
        }

        $push_error = function ($campo, array $error, array &$errors) {
            if (isset($errors[$campo])) {
                $errors[$campo][] = $error;
            } else {
                $errors[$campo] = [$error];
            }
        };

        $msg = [];
        foreach ($rules as $field => $rule) {
            $this->current_field = $field;

            if (isset($rule['type']) && $rule['type'] == 'array') {
                if (isset($data[$field])) {
                    $value = $data[$field];

                    if (!is_array($value)) {
                        $err = sprintf(trans("Invalid Data type. Expected Array"));
                        $push_error($field, ['data' => $value, 'error' => 'type', 'error_detail' => trans($err)], $errors);
                    }

                    if (isset($rule['len']) && count($value) != $rule['len']) {
                        $err = sprintf(trans("Array has not the expected length of %d"), $rule['len']);
                        $push_error($field, ['data' => $value, 'error' => 'len', 'error_detail' => trans($err)], $errors);
                    }

                    if (isset($rule['min_len']) && count($value) < $rule['min_len']) {
                        $err = sprintf(trans("Array has not the minimum expected length of %d"), $rule['min_len']);
                        $push_error($field, ['data' => $value, 'error' => 'min_len', 'error_detail' => trans($err)], $errors);
                    }

                    if (isset($rule['max_len']) && count($value) > $rule['max_len']) {
                        $err = sprintf(trans("Array has not the maximum expected length of %d"), $rule['max_len']);
                        $push_error($field, ['data' => $value, 'error' => 'max_len', 'error_detail' => trans($err)], $errors);
                    }
                }
            }

            if (isset($rules[$field]['messages'])) {
                $msg[$field] = $rule['messages'];
            }

            if (!isset($data[$field])) {
                if ($this->required && isset($rule['required']) && $rule['required']) {
                    $err = (isset($msg[$field]['required'])) ? $msg[$field]['required'] : "Field is required";
                    $push_error($field, ['data' => null, 'error' => 'required', 'error_detail' => trans($err)], $errors);
                }
                continue;
            }

            foreach ((array) $data[$field] as $value) {
                if (!isset($value) || $value === '' || $value === null) {
                    if ($this->required && isset($rule['required']) && $rule['required']) {
                        $err = (isset($msg[$field]['required'])) ? $msg[$field]['required'] : "Field is required";
                        $push_error($field, ['data' => null, 'error' => 'required', 'error_detail' => trans($err)], $errors);
                    }
                    continue 2;
                }

                if (in_array($field, (array) $this->ignored_fields)) {
                    continue 2;
                }

                if (!isset($rule['required']) || $this->required) {
                    $rule['required'] = false;
                }

                $avoid_type_check = false;
                if ($rule['required']) {
                    if (trim($value) == '') {
                        $err = (isset($msg[$field]['required'])) ? $msg[$field]['required'] : "Field is required";
                        $push_error($field, ['data' => $value, 'error' => 'required', 'error_detail' => trans($err)], $errors);
                    }
                }

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

                if (isset($rule['set'])) {
                    $rule['in'] = $rule['set'];
                }

                if (isset($rule['in'])) {
                    if (!is_array($rule['in'])) {
                        throw new \InvalidArgumentException("IN requires an array");
                    }
                    $err = (isset($msg[$field]['in'])) ? $msg[$field]['in'] : sprintf(trans("%s is not a valid value. Accepted: %s"), $value, implode(',', $rule['in']));
                    if (!in_array($value, $rule['in'])) {
                        $push_error($field, ['data' => $value, 'error' => 'in', 'error_detail' => trans($err)], $errors);
                    }
                }

                if (isset($rule['not_in'])) {
                    if (!is_array($rule['not_in'])) {
                        throw new \InvalidArgumentException("not_in requires an array");
                    }
                    $err = (isset($msg[$field]['not_in'])) ? $msg[$field]['not_in'] : sprintf(trans("%s is not a valid value. Accepted: %s"), $value, implode(',', $rule['not_in']));
                    if (in_array($value, $rule['not_in'])) {
                        $push_error($field, ['data' => $value, 'error' => 'not_in', 'error_detail' => trans($err)], $errors);
                    }
                }

                if (isset($rule['between'])) {
                    if (!is_array($rule['between']) || count($rule['between']) != 2) {
                        throw new \InvalidArgumentException("between requires an array of two values");
                    }
                    if ($value < $rule['between'][0] || $value > $rule['between'][1]) {
                        $err = (isset($msg[$field]['between'])) ? $msg[$field]['between'] : sprintf(trans("%s is not between %s and %s"), $value, $rule['between'][0], $rule['between'][1]);
                        $push_error($field, ['data' => $value, 'error' => 'between', 'error_detail' => trans($err)], $errors);
                    }
                }

                if (isset($rule['not_between'])) {
                    if (!is_array($rule['not_between']) || count($rule['not_between']) != 2) {
                        throw new \InvalidArgumentException("not_between requires an array of two values");
                    }
                    if (!($value < $rule['not_between'][0] || $value > $rule['not_between'][1])) {
                        $err = (isset($msg[$field]['not_between'])) ? $msg[$field]['not_between'] : sprintf(trans("%s should be less than %s or greater than %s"), $value, $rule['not_between'][0], $rule['not_between'][1]);
                        $push_error($field, ['data' => $value, 'error' => 'not_between', 'error_detail' => trans($err)], $errors);
                    }
                }

                if (isset($rule['type']) && in_array($rule['type'], ['number', 'int', 'float', 'double']) && trim($value) == '') {
                    $avoid_type_check = true;
                }

                if (isset($rule['type']) && !$avoid_type_check) {
                    if ($rule['type'] != 'array' && !$this->isType($value, $rule['type'])) {
                        $err = (isset($msg[$field]['type'])) ? $msg[$field]['type'] : sprintf(trans("It's not a valid %s"), $rule['type']);
                        $push_error($field, ['data' => $value, 'error' => 'type', 'error_detail' => sprintf(trans($err), $rule['type'])], $errors);
                    }
                }

                if (isset($rule['type'])) {
                    if (in_array($rule['type'], ['str', 'string', 'not_num', 'email']) || strpos($rule['type'], 'regex:') === 0) {
                        if (isset($rule['min'])) {
                            $rule['min'] = (int) $rule['min'];
                            if (strlen(trim($value)) < $rule['min']) {
                                $err = (isset($msg[$field]['min'])) ? $msg[$field]['min'] : "The minimum length is %d characters";
                                $push_error($field, ['data' => $value, 'error' => 'min', 'error_detail' => sprintf(trans($err), $rule['min'])], $errors);
                            }
                        }

                        if (isset($rule['max'])) {
                            $rule['max'] = (int) $rule['max'];
                            if (strlen(trim($value)) > $rule['max']) {
                                $err = (isset($msg[$field]['max'])) ? $msg[$field]['max'] : 'The maximum length is %d characters';
                                $push_error($field, ['data' => $value, 'error' => 'max', 'error_detail' => sprintf(trans($err), $rule['max'])], $errors);
                            }
                        }
                    }

                    if (in_array($rule['type'], ['number', 'int', 'float', 'double'])) {
                        if (isset($rule['min'])) {
                            $rule['min'] = (float) $rule['min'];
                            if ($value < $rule['min']) {
                                $err = (isset($msg[$field]['min'])) ? $msg[$field]['min'] : 'Minimum is %d';
                                $push_error($field, ['data' => $value, 'error' => 'min', 'error_detail' => sprintf(trans($err), $rule['min'])], $errors);
                            }
                        }

                        if (isset($rule['max'])) {
                            $rule['max'] = (float) $rule['max'];
                            if ($value > $rule['max']) {
                                $err = (isset($msg[$field]['max'])) ? $msg[$field]['max'] : 'Maximum is %d';
                                $push_error($field, ['data' => $value, 'error' => 'max', 'error_detail' => sprintf(trans($err), $rule['max'])], $errors);
                            }
                        }
                    }

                    if (in_array($rule['type'], ['time', 'date'])) {
                        $t0 = strtotime($value);

                        if (isset($rule['min'])) {
                            if ($t0 < strtotime($rule['min'])) {
                                $err = (isset($msg[$field]['min'])) ? $msg[$field]['min'] : 'Minimum is ' . $rule['min'];
                                $push_error($field, ['data' => $value, 'error' => 'min', 'error_detail' => sprintf(trans($err), $rule['min'])], $errors);
                            }
                        }

                        if (isset($rule['max'])) {
                            if ($t0 > strtotime($rule['max'])) {
                                $err = (isset($msg[$field]['max'])) ? $msg[$field]['max'] : 'Maximum is ' . $rule['max'];
                                $push_error($field, ['data' => $value, 'error' => 'max', 'error_detail' => sprintf(trans($err), $rule['max'])], $errors);
                            }
                        }
                    }
                }
            }
        }

        $this->errors = $errors;
        return empty($errors);
    }

    private function isValidDate($date, $format = 'Y-m-d')
    {
        $dateObj = \DateTime::createFromFormat($format, $date);
        return $dateObj && $dateObj->format($format) == $date;
    }
}