<?php

use Boctulus\Simplerest\Core\CliRouter;

/*
    openfactura-sdk Package - CLI Routes
    
    Define CLI commands for the openfactura-sdk package.
    Commands can be executed with: php com openfactura-sdk <command>
*/

// ========================================
// EXAMPLE 1: Simple command with closure
// ========================================

// CliRouter::command('openfactura-sdk:hello', function() {
//     return 'Hello from openfactura-sdk!';
// });
// Execute: php com openfactura-sdk:hello
//      or: php com openfactura-sdk hello

// ========================================
// EXAMPLE 2: Command with parameters
// ========================================

// CliRouter::command('openfactura-sdk:greet', function($name) {
//     return "Hello, $name! Welcome to openfactura-sdk.";
// });
// Execute: php com openfactura-sdk:greet John

// ========================================
// EXAMPLE 3: Command with controller
// ========================================

// CliRouter::command('openfactura-sdk:example', 'Boctulus\OpenfacturaSdk\Controllers\ExampleController@index');
// Execute: php com openfactura-sdk:example

// ========================================
// EXAMPLE 4: Grouped commands
// ========================================

// CliRouter::group('openfactura-sdk', function() {
//     
//     // Setup commands
//     CliRouter::command('install', 'Boctulus\OpenfacturaSdk\Controllers\SetupController@install');
//     CliRouter::command('uninstall', 'Boctulus\OpenfacturaSdk\Controllers\SetupController@uninstall');
//     
//     // Database commands
//     CliRouter::group('db', function() {
//         CliRouter::command('migrate', 'Boctulus\OpenfacturaSdk\Controllers\DbController@migrate');
//         CliRouter::command('seed', 'Boctulus\OpenfacturaSdk\Controllers\DbController@seed');
//         CliRouter::command('reset', 'Boctulus\OpenfacturaSdk\Controllers\DbController@reset');
//     });
//     
//     // Cache commands
//     CliRouter::group('cache', function() {
//         CliRouter::command('clear', function() {
//             return 'Cache cleared successfully!';
//         });
//         CliRouter::command('info', 'Boctulus\OpenfacturaSdk\Controllers\CacheController@info');
//     });
// });

// Execute:
//   php com openfactura-sdk install
//   php com openfactura-sdk db migrate
//   php com openfactura-sdk db seed
//   php com openfactura-sdk cache clear

// ========================================
// EXAMPLE 5: Command with validation
// ========================================

// CliRouter::command('openfactura-sdk:create-user', function($email, $role = 'user') {
//     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//         return "Error: Invalid email address";
//     }
//     return "User created: $email with role: $role";
// });
// Execute: php com openfactura-sdk:create-user user@example.com admin

// ========================================
// YOUR COMMANDS HERE
// ========================================

// Add your custom CLI routes below:

