<?php

use Boctulus\Simplerest\Core\WebRouter;

/*
    Web Test Package - Testing WebRouter Features
*/

// ========================================
// TEST 1: Simple group with closure
// ========================================

WebRouter::group('test-group', function() {
    WebRouter::get('simple', function() {
        return json_encode(['message' => 'Simple group works!', 'route' => '/test-group/simple']);
    });

    WebRouter::get('with-param/{id}', function($id) {
        return json_encode(['message' => 'Group with parameter works!', 'id' => $id, 'route' => '/test-group/with-param/{id}']);
    })->where(['id' => '[0-9]+']);
});

// ========================================
// TEST 2: Nested groups
// ========================================

WebRouter::group('api', function() {
    WebRouter::get('status', function() {
        return json_encode(['status' => 'ok', 'route' => '/api/status']);
    });

    WebRouter::group('v1', function() {
        WebRouter::get('users', function() {
            return json_encode(['users' => ['user1', 'user2'], 'route' => '/api/v1/users']);
        });

        WebRouter::get('products', function() {
            return json_encode(['products' => ['product1', 'product2'], 'route' => '/api/v1/products']);
        });

        // Triple nested group
        WebRouter::group('admin', function() {
            WebRouter::get('logs', function() {
                return json_encode(['logs' => [], 'route' => '/api/v1/admin/logs']);
            });
        });
    });
});

// ========================================
// TEST 3: Routes with multiple HTTP verbs
// ========================================

WebRouter::group('test-verbs', function() {
    WebRouter::get('resource', function() {
        return json_encode(['method' => 'GET', 'message' => 'Retrieved resource']);
    });

    WebRouter::post('resource', function() {
        return json_encode(['method' => 'POST', 'message' => 'Created resource']);
    });

    WebRouter::put('resource', function() {
        return json_encode(['method' => 'PUT', 'message' => 'Updated resource']);
    });

    WebRouter::delete('resource', function() {
        return json_encode(['method' => 'DELETE', 'message' => 'Deleted resource']);
    });
});

// ========================================
// TEST 4: Parameter validation with where()
// ========================================

WebRouter::group('test-validation', function() {
    WebRouter::get('number/{num}', function($num) {
        return json_encode(['number' => $num, 'message' => 'Valid number']);
    })->where(['num' => '[0-9]+']);

    WebRouter::get('text/{word}', function($word) {
        return json_encode(['word' => $word, 'message' => 'Valid text']);
    })->where(['word' => '[a-zA-Z]+']);

    WebRouter::get('slug/{slug}', function($slug) {
        return json_encode(['slug' => $slug, 'message' => 'Valid slug']);
    })->where(['slug' => '[a-z0-9-]+']);
});

// ========================================
// TEST 5: Multiple parameters
// ========================================

WebRouter::group('test-params', function() {
    WebRouter::get('user/{userId}/post/{postId}', function($userId, $postId) {
        return json_encode([
            'userId' => $userId,
            'postId' => $postId,
            'message' => 'User and post retrieved'
        ]);
    })->where(['userId' => '[0-9]+', 'postId' => '[0-9]+']);

    WebRouter::get('calc/{a}/{op}/{b}', function($a, $op, $b) {
        $a = (int)$a;
        $b = (int)$b;
        $result = null;
        switch($op) {
            case 'add': $result = $a + $b; break;
            case 'sub': $result = $a - $b; break;
            case 'mul': $result = $a * $b; break;
            case 'div': $result = $b != 0 ? $a / $b : 'Error: Division by zero'; break;
        }
        return json_encode([
            'a' => $a,
            'operation' => $op,
            'b' => $b,
            'result' => $result
        ]);
    })->where(['a' => '[0-9]+', 'op' => '(add|sub|mul|div)', 'b' => '[0-9]+']);
});
