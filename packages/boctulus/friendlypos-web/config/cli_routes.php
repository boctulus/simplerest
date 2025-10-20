<?php

use Boctulus\Simplerest\Core\CliRouter;

/*
    friendlypos_web Package - CLI Routes
    
    Define CLI commands for the friendlypos_web package.
    Commands can be executed with: php com friendlypos-web <command>
*/

// ========================================
// EXAMPLE 1: Simple command with closure
// ========================================

// CliRouter::command('friendlypos-web:hello', function() {
//     return 'Hello from friendlypos_web!';
// });
// Execute: php com friendlypos-web:hello
//      or: php com friendlypos-web hello

// ========================================
// EXAMPLE 2: Command with parameters
// ========================================

// CliRouter::command('friendlypos-web:greet', function($name) {
//     return "Hello, $name! Welcome to friendlypos_web.";
// });
// Execute: php com friendlypos-web:greet John

// ========================================
// EXAMPLE 3: Command with controller
// ========================================

// CliRouter::command('friendlypos-web:example', 'Boctulus\FriendlyposWeb\Controllers\ExampleController@index');
// Execute: php com friendlypos-web:example

// ========================================
// EXAMPLE 4: Grouped commands
// ========================================

// CliRouter::group('friendlypos-web', function() {
//     
//     // Setup commands
//     CliRouter::command('install', 'Boctulus\FriendlyposWeb\Controllers\SetupController@install');
//     CliRouter::command('uninstall', 'Boctulus\FriendlyposWeb\Controllers\SetupController@uninstall');
//     
//     // Database commands
//     CliRouter::group('db', function() {
//         CliRouter::command('migrate', 'Boctulus\FriendlyposWeb\Controllers\DbController@migrate');
//         CliRouter::command('seed', 'Boctulus\FriendlyposWeb\Controllers\DbController@seed');
//         CliRouter::command('reset', 'Boctulus\FriendlyposWeb\Controllers\DbController@reset');
//     });
//     
//     // Cache commands
//     CliRouter::group('cache', function() {
//         CliRouter::command('clear', function() {
//             return 'Cache cleared successfully!';
//         });
//         CliRouter::command('info', 'Boctulus\FriendlyposWeb\Controllers\CacheController@info');
//     });
// });

// Execute:
//   php com friendlypos-web install
//   php com friendlypos-web db migrate
//   php com friendlypos-web db seed
//   php com friendlypos-web cache clear

// ========================================
// EXAMPLE 5: Command with validation
// ========================================

// CliRouter::command('friendlypos-web:create-user', function($email, $role = 'user') {
//     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//         return "Error: Invalid email address";
//     }
//     return "User created: $email with role: $role";
// });
// Execute: php com friendlypos-web:create-user user@example.com admin

// ========================================
// YOUR COMMANDS HERE
// ========================================

// Add your custom CLI routes below:

