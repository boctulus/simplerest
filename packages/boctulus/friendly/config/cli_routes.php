<?php

use Boctulus\Simplerest\Core\CliRouter;

/*
    friendly Package - CLI Routes
    
    Define CLI commands for the friendly package.
    Commands can be executed with: php com friendly <command>
*/

// ========================================
// EXAMPLE 1: Simple command with closure
// ========================================

// CliRouter::command('friendly:hello', function() {
//     return 'Hello from friendly!';
// });
// Execute: php com friendly:hello
//      or: php com friendly hello

// ========================================
// EXAMPLE 2: Command with parameters
// ========================================

// CliRouter::command('friendly:greet', function($name) {
//     return "Hello, $name! Welcome to friendly.";
// });
// Execute: php com friendly:greet John

// ========================================
// EXAMPLE 3: Command with controller
// ========================================

// CliRouter::command('friendly:example', 'Boctulus\Friendly\Controllers\ExampleController@index');
// Execute: php com friendly:example

// ========================================
// EXAMPLE 4: Grouped commands
// ========================================

// CliRouter::group('friendly', function() {
//     
//     // Setup commands
//     CliRouter::command('install', 'Boctulus\Friendly\Controllers\SetupController@install');
//     CliRouter::command('uninstall', 'Boctulus\Friendly\Controllers\SetupController@uninstall');
//     
//     // Database commands
//     CliRouter::group('db', function() {
//         CliRouter::command('migrate', 'Boctulus\Friendly\Controllers\DbController@migrate');
//         CliRouter::command('seed', 'Boctulus\Friendly\Controllers\DbController@seed');
//         CliRouter::command('reset', 'Boctulus\Friendly\Controllers\DbController@reset');
//     });
//     
//     // Cache commands
//     CliRouter::group('cache', function() {
//         CliRouter::command('clear', function() {
//             return 'Cache cleared successfully!';
//         });
//         CliRouter::command('info', 'Boctulus\Friendly\Controllers\CacheController@info');
//     });
// });

// Execute:
//   php com friendly install
//   php com friendly db migrate
//   php com friendly db seed
//   php com friendly cache clear

// ========================================
// EXAMPLE 5: Command with validation
// ========================================

// CliRouter::command('friendly:create-user', function($email, $role = 'user') {
//     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//         return "Error: Invalid email address";
//     }
//     return "User created: $email with role: $role";
// });
// Execute: php com friendly:create-user user@example.com admin

// ========================================
// YOUR COMMANDS HERE
// ========================================

// Add your custom CLI routes below:

