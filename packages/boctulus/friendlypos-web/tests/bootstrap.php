<?php

// Define the root path for the test environment
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR);
}

// Load the constants
require_once ROOT_PATH . 'config/constants.php';

// Define the env() function if it doesn't exist
if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        return $value;
    }
}

// Define the is_cli() function if it doesn't exist
if (!function_exists('is_cli')) {
    function is_cli() {
        return php_sapi_name() === 'cli';
    }
}

// Define the request() and response() functions that check for global mocks
if (!function_exists('request')) {
    function request() {
        // If a mock has been set in globals by a test, use it
        if (isset($GLOBALS['mockRequest'])) {
            return $GLOBALS['mockRequest'];
        }

        // Return a simple mock implementation for basic operations
        return new class {
            public function getBody($decodeJson = false) {
                return $decodeJson ? [] : null;
            }

            public function header($name) {
                return null;
            }

            public function shiftQuery($name) {
                return null;
            }
        };
    }
}

if (!function_exists('response')) {
    function response() {
        // If a mock has been set in globals by a test, use it
        if (isset($GLOBALS['mockResponse'])) {
            return $GLOBALS['mockResponse'];
        }

        // Return a simple mock implementation for basic operations
        return new class {
            public function status($code) {
                return $this;
            }

            public function json($data) {
                return $this;
            }
        };
    }
}

// Define other helper functions that might be needed
if (!function_exists('setLang')) {
    function setLang($lang = null) {
        // Empty implementation for tests
    }
}

// Define other functions that might be in the SimpleRest framework
if (!function_exists('dd')) {
    function dd(...$args) {
        foreach ($args as $arg) {
            var_dump($arg);
        }
        exit;
    }
}

// Check if vendor/autoload.php exists and load it
if (file_exists(VENDOR_PATH . 'autoload.php')) {
    require_once VENDOR_PATH . 'autoload.php';
} else {
    die('Composer autoload not found. Please run composer install first.');
}

// Load environment variables if Dotenv is available
if (class_exists('Dotenv\\Dotenv')) {
    $class = Dotenv\Dotenv::class;
    $dotenv = $class::createImmutable(ROOT_PATH);
    $dotenv->load();
}

// Define any required environment variables for OpenFactura tests
if (!getenv('OPENFACTURA_SANDBOX')) {
    putenv('OPENFACTURA_SANDBOX=true');
}

if (!getenv('OPENFACTURA_API_KEY_DEV')) {
    putenv('OPENFACTURA_API_KEY_DEV=test_api_key');
}

if (!getenv('OPENFACTURA_API_KEY_PROD')) {
    putenv('OPENFACTURA_API_KEY_PROD=prod_api_key');
}

// Define any other required constants that the tests might need
if (!defined('Boctulus\Simplerest\Core\Libs\CONFIG_PATH')) {
    define('Boctulus\Simplerest\Core\Libs\CONFIG_PATH', CONFIG_PATH);
}

echo "Bootstrap completed successfully\n";