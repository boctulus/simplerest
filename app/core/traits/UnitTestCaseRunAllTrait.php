<?php

namespace Boctulus\Simplerest\Core\traits;


trait UnitTestCaseRunAllTrait
{
     /**    
     * 
     * This trait provides the ability to run PHPUnit tests programmatically via HTTP controllers or routes.
     * 
     * Usage:
     * 1. Importar el trait en la clase que extienda a PHPUnit\Framework\TestCase.
     * 
     * 2. Execute tests via an HTTP action or route:
     * 
     * $tc = new MyExampleTestCase();
     * $tc->runAllTests();
     * 
     * Alternatively, tests can still be executed via CLI:
     * .\vendor\bin\phpunit --bootstrap .\vendor\autoload.php .\tests\MyExampleTest.php
     * 
     */
    public function runAllTests()
    {
        $reflection = new \ReflectionClass($this);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $results = [];

        $failed = 0;
        foreach ($methods as $method) {
            if (strpos($method->name, 'test_') === 0) {
                try {
                    $this->{$method->name}();
                    $results[$method->name] = 'Passed';
                } catch (\Exception $e) {
                    $failed++;
                    $results[$method->name] = 'Failed: ' . $e->getMessage();
                }
            }
        }

        dd("TEST CASE RESULTS");

        foreach ($results as $methodName => $result) {
            print_r("Test $methodName: $result\n");
        }

        if ($failed === 0){
            dd("\r\nAll tests were PASSED O.K.");
        } else {
            dd("\r\nSome tests DID NOT PASSED [!]");
        }       
    }
}
