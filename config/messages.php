<?php

/*
    Pre-compiled messages and error codes
*/

$_messages = <<<MSG
GENERAL	                INVALID_CLASS			    'Invalid class'
GENERAL                 CLASS_NOT_FOUND             'Class not found'
HTTP                    MALFORMED_URL               'Malformed url'
HTTP                    BAD_REQUEST                 'Bad request'
HTTP                    INVALID_JSON                'Invalid JSON string format'
HTTP                    MIDDLEWARE_NOT_FOUND        'Middleware %s not found'
HTTP>AUTH               UNAUTHORIZED                'Unauthorized'
HTTP>AUTH               FORBIDDEN                   'Forbidden'
HTTP>AUTH>REQUIRED      REQUIRED_PASSWORD           'Password is required'
HTTP>AUTH>REQUIRED      REQUIRED_EMAIL_USERNAME     'Email or username are required'
HTTP>API>REQUIRED       MISSING_API_VERSION         'API version is missing'
HTTP>API                INVALID_FORMAT_API_VERSION  'Invalid format for API version'
HTTP>API                UNSUPPORTED_API_VERSION     'Unsupported API version for %s'
HTTP>API                NOT_IMPLEMENTED             'Not implemented'
HTTP>API                OPERATOR_NOT_IMPLEMENTED    'Operator %s is not implemented'
HTTP>API>REQUIRED       MISSING_ID                  'Missing id in request'
HTTP>API>REQUIRED       UNDEFINED_TENANT            'Undefined tenant'
HTTP>API                LOCKED_RESOURCE             'Locked by an admin'
HTTP>API                DELETE_ERROR                'DELETE error for id=%s'
HTTP>API                FORBIDDEN_ROLE_CHANGE       'Forbidden to change role'
HTTP>API                UNKNOWN_FIELD               'Unknown field %s'
HTTP>API>REQUIRED       UNDEFINED_FOLDER_FIELD      'Undefined folder field'
HTTP>API                FOLDER_NOT_FOUND            'Folder not found'
HTTP>API                ENTITY_NOT_FOUND            'Entity %s not found'
HTTP>API>REQUIRED       REQUIRED_ENTITY             'Entity is required'
HTTP>API>REQUIRED       REQUIRED_REFS               'Patameter refs is required'
HTTP>API                UNKNOWN_OPERATOR            'Unknown operator %s'
HTTP>API                COLLECTION_NOT_FOUND        'Colection not found'
HTTP>API>AUTH           REQUIRED_UID                'Parameter uid is required'
HTTP>API>AUTH           USER_NOT_FOUND              'User not found'
HTTP>API>AUTH           DEACTIVATED_ACCOUNT         'Deactivated account'
HTTP>API>AUTH           EXPIRED_TOKEN               'Expired token'
VALIDATION	            VALIDATION_ERROR		    'Validation error'
VALIDATION              NOT_BETWEEN				    '%s should be less than %s or gretter than %s'
VALIDATION  	        INVALID_DATA_TYPE 		    'It's not a valid %s'
FILES                   NO_FILES                    'No files'
FILES                   UNKNOWN_FILE_ID             'Unknown file id for %s'
FILES                   FILE_NOT_FOUND              'File not found'
FILES                   FILE_PERMISSION_ERROR       'File permission error'
EXCEPTIONS              SQL_EXCEPTION               'SQL Exception'
EXCEPTIONS              PDO_EXCEPTION               'PDO Exception'
CONFIGURATION           MISSING_CONFIG_USER_TABLE   'users_table in config file is required'          
MSG;


