<?php declare(strict_types=1);

namespace simplerest\core\libs;

class Msg
{    
	const INVALID_CLASS = [
                'type' => 'GENERAL',
                'code' => 'INVALID_CLASS',
                'text' => "Invalid class"
            ];

	const CLASS_NOT_FOUND = [
                'type' => 'GENERAL',
                'code' => 'CLASS_NOT_FOUND',
                'text' => "Class not found"
            ];

	const MALFORMED_URL = [
                'type' => 'HTTP',
                'code' => 'MALFORMED_URL',
                'text' => "Malformed url"
            ];

	const MISSING_API_VERSION = [
                'type' => 'HTTP>API',
                'code' => 'MISSING_API_VERSION',
                'text' => "API version is missing"
            ];

	const INVALID_FORMAT_API_VERSION = [
                'type' => 'HTTP>API',
                'code' => 'INVALID_FORMAT_API_VERSION',
                'text' => "Invalid format for API version"
            ];

	const VALIDATION_ERROR = [
                'type' => 'VALIDATION',
                'code' => 'VALIDATION_ERROR',
                'text' => "Validation error"
            ];

	const NOT_BETWEEN = [
                'type' => 'VALIDATION',
                'code' => 'NOT_BETWEEN',
                'text' => "%s should be less than %s or gretter than %s"
            ];

	const INVALID_DATA_TYPE = [
                'type' => 'VALIDATION',
                'code' => 'INVALID_DATA_TYPE',
                'text' => "It's not a valid %s"
            ];
 

}

