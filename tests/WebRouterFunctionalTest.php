<?php

namespace Boctulus\Simplerest\tests;

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Libs\ApiClient;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (php_sapi_name() != "cli") {
  return;
}

require_once __DIR__ . '/../app.php';

/*
 * WebRouter Functional Tests
 *
 * Tests real HTTP requests to routes defined in web-test package
 * using ApiClient to verify that WebRouter handles requests correctly.
 *
 * Routes tested from: packages/boctulus/web-test/config/routes.php
 *
 * Ejecuta con: ./vendor/bin/phpunit tests/WebRouterFunctionalTest.php
 *
*/
class WebRouterFunctionalTest extends TestCase
{
    protected $baseUrl;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        // Use framework's getBaseUrl() method instead of reinventing the wheel
        $config = \Boctulus\Simplerest\Core\Libs\Config::get();

        // Get base URL - try app_url first (lowercase), then APP_URL (uppercase)
        $baseUrl = $config['app_url'] ?? $config['APP_URL'] ?? 'http://simplerest.lan';

        // If base URL doesn't have protocol, add it
        if (!preg_match('/^https?:\/\//', $baseUrl)) {
            $baseUrl = 'http://' . $baseUrl;
        }

        $this->baseUrl = rtrim($baseUrl, '/');

        // Create ApiClient instance
        $this->client = new ApiClient();
        $this->client->disableSSL(); // For local development
    }

    /**
     * Helper to make GET request and return decoded response
     */
    protected function get($path)
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');

        $this->client
            ->setUrl($url)
            ->get();

        return [
            'status' => $this->client->status(),
            'data' => $this->client->decode()->data(),
            'error' => $this->client->error()
        ];
    }

    /**
     * Helper to make POST request and return decoded response
     */
    protected function post($path, $data = [])
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');

        $this->client
            ->setUrl($url)
            ->setBody($data)
            ->setHeaders(['Content-Type' => 'application/json'])
            ->post();

        return [
            'status' => $this->client->status(),
            'data' => $this->client->decode()->data(),
            'error' => $this->client->error()
        ];
    }

    /**
     * Helper to make PUT request and return decoded response
     */
    protected function put($path, $data = [])
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');

        $this->client
            ->setUrl($url)
            ->setBody($data)
            ->setHeaders(['Content-Type' => 'application/json'])
            ->put();

        return [
            'status' => $this->client->status(),
            'data' => $this->client->decode()->data(),
            'error' => $this->client->error()
        ];
    }

    /**
     * Helper to make DELETE request and return decoded response
     */
    protected function delete($path)
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');

        $this->client
            ->setUrl($url)
            ->delete();

        return [
            'status' => $this->client->status(),
            'data' => $this->client->decode()->data(),
            'error' => $this->client->error()
        ];
    }

    /**
     * Test 1: Simple group route
     * Route: GET /test-group/simple
     */
    public function testSimpleGroupRoute()
    {
        $response = $this->get('/test-group/simple');

        // Better error message when status is 0
        if ($response['status'] === 0) {
            $this->fail(
                "Could not connect to server at {$this->baseUrl}. " .
                "Error: " . ($response['error'] ?: 'Connection failed') . ". " .
                "Make sure the web server is running (Laragon/Apache)."
            );
        }

        $this->assertEquals(200, $response['status'], 'Expected HTTP 200 status');
        $this->assertIsArray($response['data'], 'Response should be array');
        $this->assertEquals('Simple group works!', $response['data']['message']);
        $this->assertEquals('/test-group/simple', $response['data']['route']);
    }

    /**
     * Test 2: Group route with parameter
     * Route: GET /test-group/with-param/{id}
     */
    public function testGroupRouteWithParameter()
    {
        $testId = 123;
        $response = $this->get("/test-group/with-param/$testId");

        $this->assertEquals(200, $response['status']);
        $this->assertIsArray($response['data']);
        $this->assertEquals('Group with parameter works!', $response['data']['message']);
        $this->assertEquals($testId, $response['data']['id']);
        $this->assertEquals('/test-group/with-param/{id}', $response['data']['route']);
    }

    /**
     * Test 3: Group route with invalid parameter (should fail validation)
     * Route: GET /test-group/with-param/{id} where id must be numeric
     */
    public function testGroupRouteWithInvalidParameter()
    {
        $response = $this->get('/test-group/with-param/invalid');

        // Should get 500 or error because validation fails
        $this->assertNotEquals(200, $response['status'],
            'Should not return 200 for invalid parameter');
    }

    /**
     * Test 4: Nested groups - level 1
     * Route: GET /api/status
     */
    public function testNestedGroupLevel1()
    {
        $response = $this->get('/api/status');

        $this->assertEquals(200, $response['status']);
        $this->assertIsArray($response['data']);
        $this->assertEquals('ok', $response['data']['status']);
        $this->assertEquals('/api/status', $response['data']['route']);
    }

    /**
     * Test 5: Nested groups - level 2
     * Route: GET /api/v1/users
     */
    public function testNestedGroupLevel2()
    {
        $response = $this->get('/api/v1/users');

        $this->assertEquals(200, $response['status']);
        $this->assertIsArray($response['data']);
        $this->assertArrayHasKey('users', $response['data']);
        $this->assertIsArray($response['data']['users']);
        $this->assertEquals('/api/v1/users', $response['data']['route']);
    }

    /**
     * Test 6: Triple nested groups
     * Route: GET /api/v1/admin/logs
     */
    public function testTripleNestedGroups()
    {
        $response = $this->get('/api/v1/admin/logs');

        $this->assertEquals(200, $response['status']);
        $this->assertIsArray($response['data']);
        $this->assertArrayHasKey('logs', $response['data']);
        $this->assertEquals('/api/v1/admin/logs', $response['data']['route']);
    }

    /**
     * Test 7: GET verb
     * Route: GET /test-verbs/resource
     */
    public function testGetVerb()
    {
        $response = $this->get('/test-verbs/resource');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('GET', $response['data']['method']);
        $this->assertEquals('Retrieved resource', $response['data']['message']);
    }

    /**
     * Test 8: POST verb
     * Route: POST /test-verbs/resource
     */
    public function testPostVerb()
    {
        $response = $this->post('/test-verbs/resource', ['test' => 'data']);

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('POST', $response['data']['method']);
        $this->assertEquals('Created resource', $response['data']['message']);
    }

    /**
     * Test 9: PUT verb
     * Route: PUT /test-verbs/resource
     */
    public function testPutVerb()
    {
        $response = $this->put('/test-verbs/resource', ['test' => 'data']);

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('PUT', $response['data']['method']);
        $this->assertEquals('Updated resource', $response['data']['message']);
    }

    /**
     * Test 10: DELETE verb
     * Route: DELETE /test-verbs/resource
     */
    public function testDeleteVerb()
    {
        $response = $this->delete('/test-verbs/resource');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('DELETE', $response['data']['method']);
        $this->assertEquals('Deleted resource', $response['data']['message']);
    }

    /**
     * Test 11: Number parameter validation
     * Route: GET /test-validation/number/{num}
     */
    public function testNumberParameterValidation()
    {
        $response = $this->get('/test-validation/number/42');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('42', $response['data']['number']);
        $this->assertEquals('Valid number', $response['data']['message']);
    }

    /**
     * Test 12: Invalid number parameter
     * Route: GET /test-validation/number/{num} - should only accept digits
     */
    public function testInvalidNumberParameter()
    {
        $response = $this->get('/test-validation/number/abc');

        $this->assertNotEquals(200, $response['status'],
            'Should not return 200 for non-numeric parameter');
    }

    /**
     * Test 13: Text parameter validation
     * Route: GET /test-validation/text/{word}
     */
    public function testTextParameterValidation()
    {
        $response = $this->get('/test-validation/text/hello');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('hello', $response['data']['word']);
        $this->assertEquals('Valid text', $response['data']['message']);
    }

    /**
     * Test 14: Invalid text parameter (with numbers)
     * Route: GET /test-validation/text/{word} - should only accept letters
     */
    public function testInvalidTextParameter()
    {
        $response = $this->get('/test-validation/text/hello123');

        $this->assertNotEquals(200, $response['status'],
            'Should not return 200 for text with numbers');
    }

    /**
     * Test 15: Slug parameter validation
     * Route: GET /test-validation/slug/{slug}
     */
    public function testSlugParameterValidation()
    {
        $response = $this->get('/test-validation/slug/my-test-slug-123');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('my-test-slug-123', $response['data']['slug']);
        $this->assertEquals('Valid slug', $response['data']['message']);
    }

    /**
     * Test 16: Invalid slug parameter (with uppercase)
     * Route: GET /test-validation/slug/{slug} - should only accept lowercase, digits, and hyphens
     */
    public function testInvalidSlugParameter()
    {
        $response = $this->get('/test-validation/slug/MySlug');

        $this->assertNotEquals(200, $response['status'],
            'Should not return 200 for slug with uppercase letters');
    }

    /**
     * Test 17: Multiple parameters
     * Route: GET /test-params/user/{userId}/post/{postId}
     */
    public function testMultipleParameters()
    {
        $userId = 10;
        $postId = 25;
        $response = $this->get("/test-params/user/$userId/post/$postId");

        $this->assertEquals(200, $response['status']);
        $this->assertEquals($userId, $response['data']['userId']);
        $this->assertEquals($postId, $response['data']['postId']);
        $this->assertEquals('User and post retrieved', $response['data']['message']);
    }

    /**
     * Test 18: Calculator with add operation
     * Route: GET /test-params/calc/{a}/{op}/{b}
     */
    public function testCalculatorAdd()
    {
        $response = $this->get('/test-params/calc/10/add/5');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals(10, $response['data']['a']);
        $this->assertEquals('add', $response['data']['operation']);
        $this->assertEquals(5, $response['data']['b']);
        $this->assertEquals(15, $response['data']['result']);
    }

    /**
     * Test 19: Calculator with sub operation
     * Route: GET /test-params/calc/{a}/{op}/{b}
     */
    public function testCalculatorSub()
    {
        $response = $this->get('/test-params/calc/20/sub/8');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals(20, $response['data']['a']);
        $this->assertEquals('sub', $response['data']['operation']);
        $this->assertEquals(8, $response['data']['b']);
        $this->assertEquals(12, $response['data']['result']);
    }

    /**
     * Test 20: Calculator with mul operation
     * Route: GET /test-params/calc/{a}/{op}/{b}
     */
    public function testCalculatorMul()
    {
        $response = $this->get('/test-params/calc/7/mul/6');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals(7, $response['data']['a']);
        $this->assertEquals('mul', $response['data']['operation']);
        $this->assertEquals(6, $response['data']['b']);
        $this->assertEquals(42, $response['data']['result']);
    }

    /**
     * Test 21: Calculator with div operation
     * Route: GET /test-params/calc/{a}/{op}/{b}
     */
    public function testCalculatorDiv()
    {
        $response = $this->get('/test-params/calc/20/div/4');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals(20, $response['data']['a']);
        $this->assertEquals('div', $response['data']['operation']);
        $this->assertEquals(4, $response['data']['b']);
        $this->assertEquals(5, $response['data']['result']);
    }

    /**
     * Test 22: Calculator with invalid operation
     * Route: GET /test-params/calc/{a}/{op}/{b} - op must be add|sub|mul|div
     */
    public function testCalculatorInvalidOperation()
    {
        $response = $this->get('/test-params/calc/10/mod/3');

        $this->assertNotEquals(200, $response['status'],
            'Should not return 200 for invalid operation');
    }

    /**
     * Test 23: Calculator division by zero
     * Route: GET /test-params/calc/{a}/{op}/{b}
     */
    public function testCalculatorDivisionByZero()
    {
        $response = $this->get('/test-params/calc/10/div/0');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('Error: Division by zero', $response['data']['result']);
    }

    /**
     * Test 24: Route not found
     */
    public function testRouteNotFound()
    {
        $response = $this->get('/non-existent-route-12345');

        // Should return 404 or some error status
        $this->assertNotEquals(200, $response['status'],
            'Non-existent route should not return 200');
    }

    /**
     * Test 25: Route sorting - specific route takes precedence
     * This tests that /test-group/with-param/123 matches the dynamic route
     * and not some other potential static route
     */
    public function testRoutePriority()
    {
        // Test with valid numeric ID
        $response1 = $this->get('/test-group/with-param/999');
        $this->assertEquals(200, $response1['status']);
        $this->assertEquals(999, $response1['data']['id']);

        // Test with another valid numeric ID
        $response2 = $this->get('/test-group/with-param/1');
        $this->assertEquals(200, $response2['status']);
        $this->assertEquals(1, $response2['data']['id']);
    }
}
