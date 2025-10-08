<?php

use Boctulus\Simplerest\Core\CliRouter;

/*
    web-test Package - CLI Routes
    
    Define CLI commands for the web-test package.
    Commands can be executed with: php com web-test <command>
*/

// ========================================
// EXAMPLE 1: Simple command with closure
// ========================================

// CliRouter::command('web-test:hello', function() {
//     return 'Hello from web-test!';
// });
// Execute: php com web-test:hello
//      or: php com web-test hello

// ========================================
// EXAMPLE 2: Command with parameters
// ========================================

// CliRouter::command('web-test:greet', function($name) {
//     return "Hello, $name! Welcome to web-test.";
// });
// Execute: php com web-test:greet John

// ========================================
// EXAMPLE 3: Command with controller
// ========================================

// CliRouter::command('web-test:example', 'Boctulus\WebTest\Controllers\ExampleController@index');
// Execute: php com web-test:example

// ========================================
// EXAMPLE 4: Grouped commands
// ========================================

// CliRouter::group('web-test', function() {
//     
//     // Setup commands
//     CliRouter::command('install', 'Boctulus\WebTest\Controllers\SetupController@install');
//     CliRouter::command('uninstall', 'Boctulus\WebTest\Controllers\SetupController@uninstall');
//     
//     // Database commands
//     CliRouter::group('db', function() {
//         CliRouter::command('migrate', 'Boctulus\WebTest\Controllers\DbController@migrate');
//         CliRouter::command('seed', 'Boctulus\WebTest\Controllers\DbController@seed');
//         CliRouter::command('reset', 'Boctulus\WebTest\Controllers\DbController@reset');
//     });
//     
//     // Cache commands
//     CliRouter::group('cache', function() {
//         CliRouter::command('clear', function() {
//             return 'Cache cleared successfully!';
//         });
//         CliRouter::command('info', 'Boctulus\WebTest\Controllers\CacheController@info');
//     });
// });

// Execute:
//   php com web-test install
//   php com web-test db migrate
//   php com web-test db seed
//   php com web-test cache clear

// ========================================
// EXAMPLE 5: Command with validation
// ========================================

// CliRouter::command('web-test:create-user', function($email, $role = 'user') {
//     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//         return "Error: Invalid email address";
//     }
//     return "User created: $email with role: $role";
// });
// Execute: php com web-test:create-user user@example.com admin

// ========================================
// YOUR COMMANDS HERE
// ========================================

// Add your custom CLI routes below:

