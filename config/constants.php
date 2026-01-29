<?php

if (!defined('ROOT_PATH'))
    define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR );

if (!defined('CONFIG_PATH'))
	define('CONFIG_PATH', ROOT_PATH  . 'config' . DIRECTORY_SEPARATOR);

if (!defined('PUBLIC_PATH'))
    define('PUBLIC_PATH', ROOT_PATH . 'public' . DIRECTORY_SEPARATOR); 

if (!defined('PACKAGES_PATH'))
	define('PACKAGES_PATH', ROOT_PATH  . 'packages' . DIRECTORY_SEPARATOR);    

if (!defined('VENDOR_PATH'))
	define('VENDOR_PATH', ROOT_PATH  . 'vendor' . DIRECTORY_SEPARATOR);  

if (!defined('DOCS_PATH'))
	define('DOCS_PATH', ROOT_PATH  . 'docs' . DIRECTORY_SEPARATOR);

if (!defined('UPLOADS_PATH'))
    define('UPLOADS_PATH', ROOT_PATH . 'uploads'. DIRECTORY_SEPARATOR);

if (!defined('STORAGE_PATH'))
    define('STORAGE_PATH', ROOT_PATH . 'storage'. DIRECTORY_SEPARATOR);    
    
if (!defined('CACHE_PATH'))
    define('CACHE_PATH', ROOT_PATH . 'cache'. DIRECTORY_SEPARATOR);  

if (!defined('LOGS_PATH'))
    define('LOGS_PATH', ROOT_PATH . 'logs'. DIRECTORY_SEPARATOR); 

if (!defined('APP_PATH'))
    define('APP_PATH', ROOT_PATH . 'app' . DIRECTORY_SEPARATOR);

if (!defined('BACKUP_PATH'))
	define('BACKUP_PATH', ROOT_PATH  . 'backup' . DIRECTORY_SEPARATOR);

if (!defined('UPDATES_PATH'))
	define('UPDATES_PATH', ROOT_PATH  . 'updates' . DIRECTORY_SEPARATOR);

if (!defined('SRC_PATH'))
    define('SRC_PATH', ROOT_PATH . 'src'. DIRECTORY_SEPARATOR);

if (!defined('DATABASE_PATH'))
    define('DATABASE_PATH', ROOT_PATH . 'database'. DIRECTORY_SEPARATOR);  

if (!defined('ETC_PATH'))
    define('ETC_PATH', ROOT_PATH . 'etc'. DIRECTORY_SEPARATOR);    

if (!defined('CORE_PATH'))
    define('CORE_PATH', SRC_PATH . 'framework'. DIRECTORY_SEPARATOR);

if (!defined('CORE_INTERFACE_PATH'))
	define('CORE_INTERFACE_PATH', CORE_PATH  . 'Interfaces' . DIRECTORY_SEPARATOR);    

if (!defined('CORE_TRAIT_PATH'))
	define('CORE_TRAIT_PATH', CORE_PATH  . 'Traits' . DIRECTORY_SEPARATOR);

if (!defined('CORE_LIBS_PATH'))
    define('CORE_LIBS_PATH', CORE_PATH  . 'Libs' . DIRECTORY_SEPARATOR);

if (!defined('CORE_HELPERS_PATH'))
    define('CORE_HELPERS_PATH', CORE_PATH  . 'Helpers' . DIRECTORY_SEPARATOR);

if (!defined('CLASS_TEMPLATES_PATH'))
	define('CLASS_TEMPLATES_PATH', CORE_PATH  . 'Templates' . DIRECTORY_SEPARATOR);

if (!defined('CORE_EXCEPTIONS_PATH'))
	define('CORE_EXCEPTIONS_PATH', CORE_PATH  . 'Exceptions' . DIRECTORY_SEPARATOR);

if (!defined('MODELS_PATH'))
    define('MODELS_PATH', APP_PATH . 'Models'. DIRECTORY_SEPARATOR);   

if (!defined('SCHEMA_PATH')){
    define('SCHEMA_PATH', APP_PATH . 'Schemas' . DIRECTORY_SEPARATOR);
}

if (!defined('DTO_PATH'))
    define('DTO_PATH', APP_PATH . 'DTO' . DIRECTORY_SEPARATOR);

if (!defined('DAO_PATH'))
    define('DAO_PATH', APP_PATH . 'DAO' . DIRECTORY_SEPARATOR);

if (!defined('CRONOS_PATH')){
    define('CRONOS_PATH', APP_PATH . 'Background/Cronjobs' . DIRECTORY_SEPARATOR);
}

if (!defined('TASKS_PATH')){
    define('TASKS_PATH', APP_PATH . 'Background/Tasks' . DIRECTORY_SEPARATOR);
}

if (!defined('COMMANDS_PATH')){
    define('COMMANDS_PATH', APP_PATH . 'Commands' . DIRECTORY_SEPARATOR);
}

if (!defined('MIGRATIONS_PATH'))
    define('MIGRATIONS_PATH', DATABASE_PATH . 'Migrations'. DIRECTORY_SEPARATOR);   

if (!defined('SEEDERS_PATH'))
    define('SEEDERS_PATH', DATABASE_PATH . 'seeders'. DIRECTORY_SEPARATOR);    

if (!defined('VIEWS_PATH'))
    define('VIEWS_PATH', APP_PATH .  'Views' . DIRECTORY_SEPARATOR);  

if (!defined('MODULES_PATH'))
    define('MODULES_PATH', APP_PATH .  'Modules' . DIRECTORY_SEPARATOR);  

if (!defined('CONTROLLERS_PATH'))
    define('CONTROLLERS_PATH', APP_PATH . 'Controllers' . DIRECTORY_SEPARATOR);    

if (!defined('PAGES_PATH'))
    define('PAGES_PATH', APP_PATH . 'Pages' . DIRECTORY_SEPARATOR);  

if (!defined('THIRD_PARTY_PATH'))
    define('THIRD_PARTY_PATH', APP_PATH . 'third-party' . DIRECTORY_SEPARATOR);      

// added 22-01-2024
if (!defined('EXCEPTIONS_PATH'))
	define('EXCEPTIONS_PATH', APP_PATH  . 'Exceptions' . DIRECTORY_SEPARATOR);

if (!defined('SECURITY_PATH'))
    define('SECURITY_PATH', STORAGE_PATH . 'security'. DIRECTORY_SEPARATOR);

if (!defined('API_PATH'))
    define('API_PATH', CONTROLLERS_PATH  . 'Api' . DIRECTORY_SEPARATOR); 

if (!defined('INTERFACE_PATH'))
	define('INTERFACE_PATH', APP_PATH  . 'Interfaces' . DIRECTORY_SEPARATOR); 

if (!defined('LIBS_PATH'))
    define('LIBS_PATH', APP_PATH . 'Libs' . DIRECTORY_SEPARATOR);   

if (!defined('TRAIT_PATH'))
    define('TRAIT_PATH', APP_PATH . 'Traits' . DIRECTORY_SEPARATOR); 

if (!defined('HELPERS_PATH'))
    define('HELPERS_PATH', APP_PATH . 'Helpers' . DIRECTORY_SEPARATOR);  

if (!defined('LOCALE_PATH'))
    define('LOCALE_PATH', APP_PATH . 'locale' . DIRECTORY_SEPARATOR);  

if (!defined('MIDDLEWARES_PATH'))
    define('MIDDLEWARES_PATH', APP_PATH . 'Middlewares' . DIRECTORY_SEPARATOR); 

if (!defined('WIDGETS_PATH'))
    define('WIDGETS_PATH', APP_PATH . 'widgets' . DIRECTORY_SEPARATOR);


if (!defined('ASSETS_PATH'))
    define('ASSETS_PATH', PUBLIC_PATH . 'assets' . DIRECTORY_SEPARATOR); 

if (!defined('IMAGES_PATH'))
    define('IMAGES_PATH', ASSETS_PATH . 'images' . DIRECTORY_SEPARATOR);     

if (!defined('CSS_PATH'))
    define('CSS_PATH', ASSETS_PATH . 'css' . DIRECTORY_SEPARATOR);   

if (!defined('JS_PATH'))
    define('JS_PATH', ASSETS_PATH . 'js' . DIRECTORY_SEPARATOR);       