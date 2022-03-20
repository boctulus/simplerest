<?php

namespace simplerest\core\libs;

class SystemConstants
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

	const BAD_REQUEST = [
                'type' => 'HTTP',
                'code' => 'BAD_REQUEST',
                'text' => "Bad request"
            ];

	const INVALID_JSON = [
                'type' => 'HTTP',
                'code' => 'INVALID_JSON',
                'text' => "Invalid JSON string format"
            ];

	const MIDDLEWARE_NOT_FOUND = [
                'type' => 'HTTP',
                'code' => 'MIDDLEWARE_NOT_FOUND',
                'text' => "Middleware %s not found"
            ];

	const UNAUTHORIZED = [
                'type' => 'HTTP>AUTH',
                'code' => 'UNAUTHORIZED',
                'text' => "Unauthorized"
            ];

	const FORBIDDEN = [
                'type' => 'HTTP>AUTH',
                'code' => 'FORBIDDEN',
                'text' => "Forbidden"
            ];

	const REQUIRED_PASSWORD = [
                'type' => 'HTTP>AUTH>REQUIRED',
                'code' => 'REQUIRED_PASSWORD',
                'text' => "Password is required"
            ];

	const REQUIRED_EMAIL_USERNAME = [
                'type' => 'HTTP>AUTH>REQUIRED',
                'code' => 'REQUIRED_EMAIL_USERNAME',
                'text' => "Email or username are required"
            ];

	const MISSING_API_VERSION = [
                'type' => 'HTTP>API>REQUIRED',
                'code' => 'MISSING_API_VERSION',
                'text' => "API version is missing"
            ];

	const INVALID_FORMAT_API_VERSION = [
                'type' => 'HTTP>API',
                'code' => 'INVALID_FORMAT_API_VERSION',
                'text' => "Invalid format for API version"
            ];

	const UNSUPPORTED_API_VERSION = [
                'type' => 'HTTP>API',
                'code' => 'UNSUPPORTED_API_VERSION',
                'text' => "Unsupported API version for %s"
            ];

	const NOT_IMPLEMENTED = [
                'type' => 'HTTP>API',
                'code' => 'NOT_IMPLEMENTED',
                'text' => "Not implemented"
            ];

	const OPERATOR_NOT_IMPLEMENTED = [
                'type' => 'HTTP>API',
                'code' => 'OPERATOR_NOT_IMPLEMENTED',
                'text' => "Operator %s is not implemented"
            ];

	const MISSING_ID = [
                'type' => 'HTTP>API>REQUIRED',
                'code' => 'MISSING_ID',
                'text' => "Missing id in request"
            ];

	const UNDEFINED_TENANT = [
                'type' => 'HTTP>API>REQUIRED',
                'code' => 'UNDEFINED_TENANT',
                'text' => "Undefined tenant"
            ];

	const LOCKED_RESOURCE = [
                'type' => 'HTTP>API',
                'code' => 'LOCKED_RESOURCE',
                'text' => "Locked by an admin"
            ];

	const DELETE_ERROR = [
                'type' => 'HTTP>API',
                'code' => 'DELETE_ERROR',
                'text' => "DELETE error for id=%s"
            ];

	const FORBIDDEN_ROLE_CHANGE = [
                'type' => 'HTTP>API',
                'code' => 'FORBIDDEN_ROLE_CHANGE',
                'text' => "Forbidden to change role"
            ];

	const UNKNOWN_FIELD = [
                'type' => 'HTTP>API',
                'code' => 'UNKNOWN_FIELD',
                'text' => "Unknown field %s"
            ];

	const UNDEFINED_FOLDER_FIELD = [
                'type' => 'HTTP>API>REQUIRED',
                'code' => 'UNDEFINED_FOLDER_FIELD',
                'text' => "Undefined folder field"
            ];

	const FOLDER_NOT_FOUND = [
                'type' => 'HTTP>API',
                'code' => 'FOLDER_NOT_FOUND',
                'text' => "Folder not found"
            ];

	const ENTITY_NOT_FOUND = [
                'type' => 'HTTP>API',
                'code' => 'ENTITY_NOT_FOUND',
                'text' => "Entity %s not found"
            ];

	const REQUIRED_ENTITY = [
                'type' => 'HTTP>API>REQUIRED',
                'code' => 'REQUIRED_ENTITY',
                'text' => "Entity is required"
            ];

	const REQUIRED_REFS = [
                'type' => 'HTTP>API>REQUIRED',
                'code' => 'REQUIRED_REFS',
                'text' => "Patameter refs is required"
            ];

	const UNKNOWN_OPERATOR = [
                'type' => 'HTTP>API',
                'code' => 'UNKNOWN_OPERATOR',
                'text' => "Unknown operator %s"
            ];

	const COLLECTION_NOT_FOUND = [
                'type' => 'HTTP>API',
                'code' => 'COLLECTION_NOT_FOUND',
                'text' => "Colection not found"
            ];

	const REQUIRED_UID = [
                'type' => 'HTTP>API>AUTH',
                'code' => 'REQUIRED_UID',
                'text' => "Parameter uid is required"
            ];

	const USER_NOT_FOUND = [
                'type' => 'HTTP>API>AUTH',
                'code' => 'USER_NOT_FOUND',
                'text' => "User not found"
            ];

	const DEACTIVATED_ACCOUNT = [
                'type' => 'HTTP>API>AUTH',
                'code' => 'DEACTIVATED_ACCOUNT',
                'text' => "Deactivated account"
            ];

	const EXPIRED_TOKEN = [
                'type' => 'HTTP>API>AUTH',
                'code' => 'EXPIRED_TOKEN',
                'text' => "Expired token"
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

	const NO_FILES = [
                'type' => 'FILES',
                'code' => 'NO_FILES',
                'text' => "No files"
            ];

	const UNKNOWN_FILE_ID = [
                'type' => 'FILES',
                'code' => 'UNKNOWN_FILE_ID',
                'text' => "Unknown file id for %s"
            ];

	const FILE_NOT_FOUND = [
                'type' => 'FILES',
                'code' => 'FILE_NOT_FOUND',
                'text' => "File not found"
            ];

	const FILE_PERMISSION_ERROR = [
                'type' => 'FILES',
                'code' => 'FILE_PERMISSION_ERROR',
                'text' => "File permission error"
            ];

	const SQL_EXCEPTION = [
                'type' => 'EXCEPTIONS',
                'code' => 'SQL_EXCEPTION',
                'text' => "SQL Exception"
            ];

	const PDO_EXCEPTION = [
                'type' => 'EXCEPTIONS',
                'code' => 'PDO_EXCEPTION',
                'text' => "PDO Exception"
            ];

	const MISSING_CONFIG_USER_TABLE = [
                'type' => 'CONFIGURATION',
                'code' => 'MISSING_CONFIG_USER_TABLE',
                'text' => "users_table in config file is required"
            ];
 

}

