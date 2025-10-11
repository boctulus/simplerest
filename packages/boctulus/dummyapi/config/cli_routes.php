<?php

use Boctulus\Simplerest\Core\CliRouter;

/*
    DummyApi Package - CLI Routes
    
    Define CLI commands for the DummyApi package.
    Commands can be executed with: php com dummyapi <command>
*/

// ========================================
// EXAMPLE 1: Simple command with closure
// ========================================

// CliRouter::command('dummyapi:hello', function() {
//     return 'Hello from DummyApi!';
// });
// Execute: php com dummyapi:hello
//      or: php com dummyapi hello

// ========================================
// EXAMPLE 2: Command with parameters
// ========================================

// CliRouter::command('dummyapi:greet', function($name) {
//     return "Hello, $name! Welcome to DummyApi.";
// });
// Execute: php com dummyapi:greet John

// ========================================
// EXAMPLE 3: Command with controller
// ========================================

// CliRouter::command('dummyapi:example', 'Boctulus\Dummyapi\Controllers\ExampleController@index');
// Execute: php com dummyapi:example

// ========================================
// EXAMPLE 4: Grouped commands
// ========================================

// CliRouter::group('dummyapi', function() {
//     
//     // Setup commands
//     CliRouter::command('install', 'Boctulus\Dummyapi\Controllers\SetupController@install');
//     CliRouter::command('uninstall', 'Boctulus\Dummyapi\Controllers\SetupController@uninstall');
//     
//     // Database commands
//     CliRouter::group('db', function() {
//         CliRouter::command('migrate', 'Boctulus\Dummyapi\Controllers\DbController@migrate');
//         CliRouter::command('seed', 'Boctulus\Dummyapi\Controllers\DbController@seed');
//         CliRouter::command('reset', 'Boctulus\Dummyapi\Controllers\DbController@reset');
//     });
//     
//     // Cache commands
//     CliRouter::group('cache', function() {
//         CliRouter::command('clear', function() {
//             return 'Cache cleared successfully!';
//         });
//         CliRouter::command('info', 'Boctulus\Dummyapi\Controllers\CacheController@info');
//     });
// });

// Execute:
//   php com dummyapi install
//   php com dummyapi db migrate
//   php com dummyapi db seed
//   php com dummyapi cache clear

// ========================================
// EXAMPLE 5: Command with validation
// ========================================

// CliRouter::command('dummyapi:create-user', function($email, $role = 'user') {
//     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//         return "Error: Invalid email address";
//     }
//     return "User created: $email with role: $role";
// });
// Execute: php com dummyapi:create-user user@example.com admin

// ========================================
// YOUR COMMANDS HERE
// ========================================

// Add your custom CLI routes below:

