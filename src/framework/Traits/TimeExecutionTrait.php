<?php

namespace Boctulus\Simplerest\Core\Traits;

use Boctulus\Simplerest\Core\Libs\Logger;

trait TimeExecutionTrait
{
    protected $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    public function __destruct()
    {
        // Cuando la instancia de la clase es destruida, calcula y muestra el tiempo de ejecución total
        $totalTime = (microtime(true) - $this->startTime) * 1000; // Convertido a milisegundos
        echo "Total execution time: " . round($totalTime, 2) . " ms\n";
    }

    public function __call(string $name, array $arguments)
    {
        return $this->measureExecutionTime($name, $arguments);
    }

    public static function __callStatic(string $name, array $arguments)
    {
        return (new static())->measureExecutionTime($name, $arguments);
    }

    private function measureExecutionTime(string $method, array $arguments)
    {
        if (!method_exists($this, $method)) {
            throw new \BadMethodCallException("Method $method does not exist");
        }

        $start = microtime(true);
        $result = call_user_func_array([$this, $method], $arguments);
        $end = microtime(true);

        $executionTime = ($end - $start) * 1000; // Convertido a milisegundos

        // Decide si se muestra o se guarda en un log
        if (php_sapi_name() === 'cli') {
            echo "Execution time of $method: " . round($executionTime, 2) . " ms\n";
        } else {
            Logger::log("Execution time of $method: " . round($executionTime, 2) . " ms");
        }

        return $result;
    }
}