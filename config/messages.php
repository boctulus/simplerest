<?php

/*
    Pre-compiled messages and error codes (flat array)
*/

return [
    'GENERAL>INVALID_CLASS'                          => 'Invalid class',
    'GENERAL>CLASS_NOT_FOUND'                        => 'Class not found',
    'GENERAL>NOT_IMPLEMENTED'                        => 'Not implemented',

    'HTTP>MALFORMED_URL'                             => 'Malformed url',
    'HTTP>BAD_REQUEST'                               => 'Bad request',
    'HTTP>INVALID_JSON'                              => 'Invalid JSON string format',
    'HTTP>MIDDLEWARE_NOT_FOUND'                      => 'Middleware not found',

    'HTTP>AUTH>UNAUTHORIZED'                         => 'Unauthorized',
    'HTTP>AUTH>FORBIDDEN'                            => 'Forbidden',
    'HTTP>AUTH>REQUIRED>REQUIRED_PASSWORD'           => 'Password is required',
    'HTTP>AUTH>REQUIRED>REQUIRED_EMAIL_USERNAME'     => 'Email or username are required',

    'HTTP>API>REQUIRED>MISSING_API_VERSION'          => 'API version is missing',
    'HTTP>API>INVALID_FORMAT_API_VERSION'            => 'Invalid format for API version',
    'HTTP>API>UNSUPPORTED_API_VERSION'               => 'Unsupported API version',
    'HTTP>API>NOT_IMPLEMENTED'                       => 'Not implemented',
    'HTTP>API>OPERATOR_NOT_IMPLEMENTED'              => 'Not implemented Operator`',
    'HTTP>API>REQUIRED>MISSING_ID'                   => 'Missing id in request',
    'HTTP>API>REQUIRED>UNDEFINED_TENANT'             => 'Undefined tenant',
    'HTTP>API>LOCKED_RESOURCE'                       => 'Locked by an admin',
    'HTTP>API>DELETE_ERROR'                          => 'DELETE error',
    'HTTP>API>FORBIDDEN_ROLE_CHANGE'                 => 'Forbidden to change role',
    'HTTP>API>UNKNOWN_FIELD'                         => 'Unknown field',
    'HTTP>API>REQUIRED>UNDEFINED_FOLDER_FIELD'       => 'Undefined folder field',
    'HTTP>API>FOLDER_NOT_FOUND'                      => 'Folder not found',
    'HTTP>API>ENTITY_NOT_FOUND'                      => 'Entity not found',
    'HTTP>API>REQUIRED>REQUIRED_ENTITY'              => 'Entity is required',
    'HTTP>API>REQUIRED>REQUIRED_REFS'                => 'Parameter refs is required',
    'HTTP>API>UNKNOWN_OPERATOR'                      => 'Unknown operator',
    'HTTP>API>COLLECTION_NOT_FOUND'                  => 'Collection not found',

    'HTTP>API>AUTH>REQUIRED_UID'                     => 'Parameter uid is required',
    'HTTP>API>AUTH>USER_NOT_FOUND'                   => 'User not found',
    'HTTP>API>AUTH>DEACTIVATED_ACCOUNT'              => 'Deactivated account',
    'HTTP>API>AUTH>EXPIRED_TOKEN'                    => 'Expired token',

    'VALIDATION>VALIDATION_ERROR'                    => 'Validation error',
    'VALIDATION>NOT_BETWEEN'                         => 'Invalid range',
    'VALIDATION>INVALID_DATA_TYPE'                   => 'Invalid data type',
    'VALIDATION>INVALID_VALIDATION'                  => 'Invalid validation',

    'FILES>NOT_A_FILE'                               => 'Directory but not a file',
    'FILES>UNREADABLE'                               => 'Unreadable file',
    'FILES>NO_FILES'                                 => 'Empty directory',
    'FILES>UNKNOWN_FILE_ID'                          => 'Unknown file id',
    'FILES>FILE_NOT_FOUND'                           => 'File not found',
    'FILES>FILE_PERMISSION_ERROR'                    => 'File permission error',

    'DB>SQL_EXCEPTION'                               => 'SQL Exception',
    'DB>PDO_EXCEPTION'                               => 'PDO Exception',
    'DB>COLUMN_NOT_FOUND'                            => 'Column not found in table',
    'DB>TABLE_NOT_FOUND'                             => 'Table not found',
    'DB>TABLE_ALREADY_EXISTS'                        => 'Table already exists',
    'DB>SCHEMA_ERROR'                                => 'Schema error',
    'DB>EMPTY_SCHEMA'                                => 'Empty schema',

    'CONFIGURATION>MISSING_CONFIG_USER_TABLE'        => 'Table users_table declaration is missing in config file',
];
