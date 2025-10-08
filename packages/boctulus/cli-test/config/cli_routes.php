<?php

use Boctulus\Simplerest\Core\CliRouter;

/*
    CLI Test Package - Testing CliRouter Features
*/

// ========================================
// TEST 1: Simple commands
// ========================================

CliRouter::command('test:simple', function() {
    return 'Simple command works!';
});

// ========================================
// TEST 2: Command with parameters
// ========================================

CliRouter::command('test:greet', function($name) {
    return "Hello, $name!";
});

// ========================================
// TEST 3: Group feature
// ========================================

CliRouter::group('test', function() {
    CliRouter::command('group1', function() {
        return 'Group command 1 works!';
    });

    CliRouter::command('group2', function() {
        return 'Group command 2 works!';
    });

    // Nested groups
    CliRouter::group('nested', function() {
        CliRouter::command('deep', function() {
            return 'Nested group command works!';
        });
    });
});

// ========================================
// TEST 4: Alias feature
// ========================================

// Internal error - controller class Boctulus\\Simplerest\\Controllers\\ShortController not found
CliRouter::command('test:longname', function() {
    return 'Command with alias works!';
})->alias('short');

// ========================================
// TEST 5: Name feature
// ========================================

CliRouter::command('test:descriptive', function() {
    return 'Command with name works!';
})->name('my-command');

// ========================================
// TEST 6: Where validation
// ========================================

// Internal error - controller class Boctulus\\Simplerest\\Controllers\\My-commandController not found
CliRouter::command('test:validate', 'Boctulus\CliTest\Controllers\TestController@validate')
    ->where(['num' => '[0-9]+']);

// ========================================
// TEST 7: Multiple parameters
// ========================================

CliRouter::command('test:calc', function($a, $b) {
    return "$a + $b = " . ($a + $b);
});

// ========================================
// TEST 8: Optional parameters (NOT SUPPORTED YET)
// ========================================

// CliRouter::command('test:optional', function($required, $optional = 'default') {
//     return "Required: $required, Optional: $optional";
// });

// ========================================
// TEST 9: Controllers
// ========================================

CliRouter::command('test:controller', 'Boctulus\CliTest\Controllers\TestController@info');

CliRouter::command('test:controller:process', 'Boctulus\CliTest\Controllers\TestController@process');

// ========================================
// TEST 10: Controller within group
// ========================================

CliRouter::group('admin', function() {
    CliRouter::command('users', 'Boctulus\CliTest\Controllers\AdminController@users');
    CliRouter::command('cache', 'Boctulus\CliTest\Controllers\AdminController@cache');
});

