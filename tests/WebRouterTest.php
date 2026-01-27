<?php

namespace Boctulus\Simplerest\tests;

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\WebRouter;
use Boctulus\Simplerest\Core\Response;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (php_sapi_name() != "cli") {
  return;
}

// require_once __DIR__ . '../../vendor/autoload.php';
require_once __DIR__ . '/../app.php';


/*
 * WebRouter Unit Tests
 *
 * Tests all major features of WebRouter including:
 * - Route registration (GET, POST, PUT, PATCH, DELETE, OPTIONS)
 * - Route groups (simple and nested)
 * - Dynamic parameters
 * - Parameter validation with where()
 * - fromArray() method
 * - Automatic route sorting by specificity
 * - Closures and controller routes
 *
 * Ejecuta con: ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/WebRouterTest.php
 *
*/
class WebRouterTest extends TestCase
{
    protected $router;

    public static function setUpBeforeClass(): void
    {
        // Clear any existing routes before tests
        // This is important to avoid conflicts with other tests
    }

    protected function setUp(): void
    {
        parent::setUp();
        // Get fresh WebRouter instance for each test
        $this->router = WebRouter::getInstance();
    }

    /**
     * Test 1: Basic GET route registration
     */
    public function testCanRegisterSimpleGetRoute()
    {
        WebRouter::get('unittest-simple-get', function() {
            return 'GET route works';
        });

        // Use reflection to access protected routes property
        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        $this->assertArrayHasKey('GET', $routes);
        $this->assertArrayHasKey('unittest-simple-get', $routes['GET']);
    }

    /**
     * Test 2: POST route registration
     */
    public function testCanRegisterPostRoute()
    {
        WebRouter::post('unittest-post-route', function() {
            return 'POST route works';
        });

        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        $this->assertArrayHasKey('POST', $routes);
        $this->assertArrayHasKey('unittest-post-route', $routes['POST']);
    }

    /**
     * Test 3: PUT route registration
     */
    public function testCanRegisterPutRoute()
    {
        WebRouter::put('unittest-put-route', function() {
            return 'PUT route works';
        });

        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        $this->assertArrayHasKey('PUT', $routes);
        $this->assertArrayHasKey('unittest-put-route', $routes['PUT']);
    }

    /**
     * Test 4: PATCH route registration
     */
    public function testCanRegisterPatchRoute()
    {
        WebRouter::patch('unittest-patch-route', function() {
            return 'PATCH route works';
        });

        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        $this->assertArrayHasKey('PATCH', $routes);
        $this->assertArrayHasKey('unittest-patch-route', $routes['PATCH']);
    }

    /**
     * Test 5: DELETE route registration
     */
    public function testCanRegisterDeleteRoute()
    {
        WebRouter::delete('unittest-delete-route', function() {
            return 'DELETE route works';
        });

        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        $this->assertArrayHasKey('DELETE', $routes);
        $this->assertArrayHasKey('unittest-delete-route', $routes['DELETE']);
    }

    /**
     * Test 6: OPTIONS route registration
     */
    public function testCanRegisterOptionsRoute()
    {
        WebRouter::options('unittest-options-route', function() {
            return 'OPTIONS route works';
        });

        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        $this->assertArrayHasKey('OPTIONS', $routes);
        $this->assertArrayHasKey('unittest-options-route', $routes['OPTIONS']);
    }

    /**
     * Test 7: Simple route group
     */
    public function testCanRegisterSimpleGroup()
    {
        WebRouter::group('unittest-group', function() {
            WebRouter::get('simple', function() {
                return 'Group route works';
            });
        });

        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        // The route should be registered as 'unittest-group/simple'
        $this->assertArrayHasKey('unittest-group/simple', $routes['GET']);
    }

    /**
     * Test 8: Nested route groups
     */
    public function testCanRegisterNestedGroups()
    {
        WebRouter::group('unittest-level1', function() {
            WebRouter::group('level2', function() {
                WebRouter::get('endpoint', function() {
                    return 'Nested group works';
                });
            });
        });

        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        // The route should be registered as 'unittest-level1/level2/endpoint'
        $this->assertArrayHasKey('unittest-level1/level2/endpoint', $routes['GET']);
    }

    /**
     * Test 9: Route with single parameter
     */
    public function testCanRegisterRouteWithParameter()
    {
        WebRouter::get('unittest-user/{id}', function($id) {
            return "User $id";
        });

        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        $this->assertArrayHasKey('unittest-user/{id}', $routes['GET']);
    }

    /**
     * Test 10: Route with multiple parameters
     */
    public function testCanRegisterRouteWithMultipleParameters()
    {
        WebRouter::get('unittest-post/{userId}/comment/{commentId}', function($userId, $commentId) {
            return "User $userId, Comment $commentId";
        });

        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        $this->assertArrayHasKey('unittest-post/{userId}/comment/{commentId}', $routes['GET']);
    }

    /**
     * Test 11: Route with parameter validation using where()
     */
    public function testCanRegisterRouteWithWhereValidation()
    {
        WebRouter::get('unittest-number/{num}', function($num) {
            return "Number: $num";
        })->where(['num' => '[0-9]+']);

        $reflection = new \ReflectionClass(WebRouter::class);

        // Check routes
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();
        $this->assertArrayHasKey('unittest-number/{num}', $routes['GET']);

        // Check wheres
        $wheresProperty = $reflection->getProperty('wheres');
        $wheresProperty->setAccessible(true);
        $wheres = $wheresProperty->getValue();
        $this->assertArrayHasKey('GET', $wheres);
        $this->assertArrayHasKey('unittest-number/{num}', $wheres['GET']);
        $this->assertEquals('[0-9]+', $wheres['GET']['unittest-number/{num}']['num']);
    }

    /**
     * Test 12: Route with multiple parameter validations
     */
    public function testCanRegisterRouteWithMultipleWhereValidations()
    {
        WebRouter::get('unittest-calc/{a}/{b}', function($a, $b) {
            return "Calc: $a + $b";
        })->where(['a' => '[0-9]+', 'b' => '[0-9]+']);

        $reflection = new \ReflectionClass(WebRouter::class);
        $wheresProperty = $reflection->getProperty('wheres');
        $wheresProperty->setAccessible(true);
        $wheres = $wheresProperty->getValue();

        $this->assertArrayHasKey('unittest-calc/{a}/{b}', $wheres['GET']);
        $this->assertEquals('[0-9]+', $wheres['GET']['unittest-calc/{a}/{b}']['a']);
        $this->assertEquals('[0-9]+', $wheres['GET']['unittest-calc/{a}/{b}']['b']);
    }

    /**
     * Test 13: fromArray() with specific HTTP verb
     */
    public function testCanRegisterRoutesFromArrayWithVerb()
    {
        WebRouter::fromArray([
            'GET:/unittest-from-array-get' => function() {
                return 'From array GET';
            },
            'POST:/unittest-from-array-post' => function() {
                return 'From array POST';
            }
        ]);

        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        $this->assertArrayHasKey('unittest-from-array-get', $routes['GET']);
        $this->assertArrayHasKey('unittest-from-array-post', $routes['POST']);
    }

    /**
     * Test 14: fromArray() without verb (should register for all verbs)
     */
    public function testCanRegisterRoutesFromArrayWithoutVerb()
    {
        WebRouter::fromArray([
            '/unittest-all-verbs' => function() {
                return 'All verbs';
            }
        ]);

        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        // Should be registered for all HTTP verbs
        $verbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
        foreach ($verbs as $verb) {
            $this->assertArrayHasKey($verb, $routes);
            $this->assertArrayHasKey('unittest-all-verbs', $routes[$verb]);
        }
    }

    /**
     * Test 15: Route name/alias registration
     */
    public function testCanRegisterRouteWithName()
    {
        WebRouter::get('unittest-named-route', function() {
            return 'Named route';
        })->name('unittest.named.route');

        $reflection = new \ReflectionClass(WebRouter::class);
        $aliasesProperty = $reflection->getProperty('aliases');
        $aliasesProperty->setAccessible(true);
        $aliases = $aliasesProperty->getValue();

        $this->assertArrayHasKey('unittest.named.route', $aliases);
        $this->assertEquals('GET', $aliases['unittest.named.route']['verb']);
        $this->assertEquals('unittest-named-route', $aliases['unittest.named.route']['uri']);
    }

    /**
     * Test 16: Group with multiple routes
     */
    public function testCanRegisterMultipleRoutesInGroup()
    {
        WebRouter::group('unittest-api', function() {
            WebRouter::get('users', function() { return 'Users list'; });
            WebRouter::post('users', function() { return 'Create user'; });
            WebRouter::get('users/{id}', function($id) { return "User $id"; });
        });

        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        $this->assertArrayHasKey('unittest-api/users', $routes['GET']);
        $this->assertArrayHasKey('unittest-api/users', $routes['POST']);
        $this->assertArrayHasKey('unittest-api/users/{id}', $routes['GET']);
    }

    /**
     * Test 17: Triple nested groups
     */
    public function testCanRegisterTripleNestedGroups()
    {
        WebRouter::group('unittest-v1', function() {
            WebRouter::group('admin', function() {
                WebRouter::group('settings', function() {
                    WebRouter::get('profile', function() {
                        return 'Deeply nested route';
                    });
                });
            });
        });

        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        $this->assertArrayHasKey('unittest-v1/admin/settings/profile', $routes['GET']);
    }

    /**
     * Test 18: Route compilation creates patterns for dynamic routes
     */
    public function testCompileCreatesPatternForDynamicRoutes()
    {
        WebRouter::get('unittest-dynamic/{id}', function($id) {
            return "ID: $id";
        });

        WebRouter::compile();

        $reflection = new \ReflectionClass(WebRouter::class);
        $patternsProperty = $reflection->getProperty('routePatterns');
        $patternsProperty->setAccessible(true);
        $patterns = $patternsProperty->getValue();

        $this->assertArrayHasKey('GET', $patterns);
        $this->assertArrayHasKey('unittest-dynamic/{id}', $patterns['GET']);
        $this->assertStringContainsString('([^/]+)', $patterns['GET']['unittest-dynamic/{id}']);
    }

    /**
     * Test 19: Route sorting - more specific routes come first
     */
    public function testRoutesSortedBySpecificity()
    {
        // Register routes in random order
        WebRouter::get('unittest-sort/users/{id}', function($id) { return "User $id"; });
        WebRouter::get('unittest-sort/users/active', function() { return "Active users"; });
        WebRouter::get('unittest-sort/users', function() { return "All users"; });

        WebRouter::compile();

        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        $routeKeys = array_keys($routes['GET']);

        // Find positions
        $posActive = array_search('unittest-sort/users/active', $routeKeys);
        $posId = array_search('unittest-sort/users/{id}', $routeKeys);
        $posAll = array_search('unittest-sort/users', $routeKeys);

        // More specific route (no params) should come before less specific (with params)
        $this->assertLessThan($posId, $posActive,
            "Route 'unittest-sort/users/active' should come before 'unittest-sort/users/{id}'");
    }

    /**
     * Test 20: Complex route with operation parameter validation
     */
    public function testComplexRouteWithOperationValidation()
    {
        WebRouter::get('unittest-math/{a}/{op}/{b}', function($a, $op, $b) {
            return "Math: $a $op $b";
        })->where([
            'a' => '[0-9]+',
            'op' => '(add|sub|mul|div)',
            'b' => '[0-9]+'
        ]);

        $reflection = new \ReflectionClass(WebRouter::class);
        $wheresProperty = $reflection->getProperty('wheres');
        $wheresProperty->setAccessible(true);
        $wheres = $wheresProperty->getValue();

        $this->assertArrayHasKey('unittest-math/{a}/{op}/{b}', $wheres['GET']);
        $this->assertEquals('[0-9]+', $wheres['GET']['unittest-math/{a}/{op}/{b}']['a']);
        $this->assertEquals('(add|sub|mul|div)', $wheres['GET']['unittest-math/{a}/{op}/{b}']['op']);
        $this->assertEquals('[0-9]+', $wheres['GET']['unittest-math/{a}/{op}/{b}']['b']);
    }

    /**
     * Test 21: Group with parameter validation
     */
    public function testGroupRoutesWithParameterValidation()
    {
        WebRouter::group('unittest-validated', function() {
            WebRouter::get('number/{num}', function($num) {
                return "Number: $num";
            })->where(['num' => '[0-9]+']);

            WebRouter::get('word/{word}', function($word) {
                return "Word: $word";
            })->where(['word' => '[a-zA-Z]+']);
        });

        $reflection = new \ReflectionClass(WebRouter::class);
        $wheresProperty = $reflection->getProperty('wheres');
        $wheresProperty->setAccessible(true);
        $wheres = $wheresProperty->getValue();

        $this->assertArrayHasKey('unittest-validated/number/{num}', $wheres['GET']);
        $this->assertArrayHasKey('unittest-validated/word/{word}', $wheres['GET']);
    }

    /**
     * Test 22: fromArray with mixed verbs and routes
     */
    public function testFromArrayWithMixedConfiguration()
    {
        WebRouter::fromArray([
            'GET:/unittest-mixed-get' => function() { return 'GET'; },
            'POST:/unittest-mixed-post' => function() { return 'POST'; },
            '/unittest-mixed-all' => function() { return 'ALL'; }
        ]);

        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        $this->assertArrayHasKey('unittest-mixed-get', $routes['GET']);
        $this->assertArrayHasKey('unittest-mixed-post', $routes['POST']);

        // unittest-mixed-all should be in all verbs
        $verbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
        foreach ($verbs as $verb) {
            $this->assertArrayHasKey('unittest-mixed-all', $routes[$verb]);
        }
    }

    /**
     * Test 23: Slug parameter validation
     */
    public function testSlugParameterValidation()
    {
        WebRouter::get('unittest-article/{slug}', function($slug) {
            return "Article: $slug";
        })->where(['slug' => '[a-z0-9-]+']);

        $reflection = new \ReflectionClass(WebRouter::class);
        $wheresProperty = $reflection->getProperty('wheres');
        $wheresProperty->setAccessible(true);
        $wheres = $wheresProperty->getValue();

        $this->assertEquals('[a-z0-9-]+', $wheres['GET']['unittest-article/{slug}']['slug']);
    }

    /**
     * Test 24: Route with trailing slash handling
     */
    public function testTrailingSlashIsRemoved()
    {
        WebRouter::get('unittest-trailing/', function() {
            return 'No trailing slash';
        });

        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        // Route should be registered without trailing slash
        $this->assertArrayHasKey('unittest-trailing', $routes['GET']);
    }

    /**
     * Test 25: Multiple routes with same path but different verbs
     */
    public function testSamePathDifferentVerbs()
    {
        WebRouter::get('unittest-resource', function() { return 'GET resource'; });
        WebRouter::post('unittest-resource', function() { return 'POST resource'; });
        WebRouter::put('unittest-resource', function() { return 'PUT resource'; });
        WebRouter::delete('unittest-resource', function() { return 'DELETE resource'; });

        $reflection = new \ReflectionClass(WebRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        $this->assertArrayHasKey('unittest-resource', $routes['GET']);
        $this->assertArrayHasKey('unittest-resource', $routes['POST']);
        $this->assertArrayHasKey('unittest-resource', $routes['PUT']);
        $this->assertArrayHasKey('unittest-resource', $routes['DELETE']);
    }
}
